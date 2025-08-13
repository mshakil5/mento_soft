<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Yajra\DataTables\Facades\DataTables;
use Validator;
use App\Models\ProjectSlider;
use App\Models\ProjectType;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Project::with('service')->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('thumbnail', function($row) {
                    if ($row->thumbnail_image) {
                        return '<a href="'.asset("images/projects/thumbnails/".$row->thumbnail_image).'" target="_blank">
                                    <img src="'.asset("images/projects/thumbnails/".$row->thumbnail_image).'" style="max-width:80px; height:auto;">
                                </a>';
                    }
                    return '';
                })
                ->addColumn('sl', function ($row) {
                    return $row->sl ?? '';
                })
                // ->addColumn('service', function($row) {
                //     return $row->service->title ?? 'N/A';
                // })
                ->addColumn('status', function($row) {
                    $checked = $row->status == 1 ? 'checked' : '';
                    return '<div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input toggle-status" id="customSwitchStatus'.$row->id.'" data-id="'.$row->id.'" '.$checked.'>
                                <label class="custom-control-label" for="customSwitchStatus'.$row->id.'"></label>
                            </div>';
                })
                ->addColumn('featured', function($row) {
                    $checked = $row->is_featured == 1 ? 'checked' : '';
                    return '<div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input toggle-featured" id="customSwitchFeatured'.$row->id.'" data-id="'.$row->id.'" '.$checked.'>
                                <label class="custom-control-label" for="customSwitchFeatured'.$row->id.'"></label>
                            </div>';
                })
                ->addColumn('action', function($row) {
                    return '
                      <button class="btn btn-sm btn-info edit" data-id="'.$row->id.'">Edit</button>
                      <button class="btn btn-sm btn-danger delete" data-id="'.$row->id.'">Delete</button>
                    ';
                })
                ->rawColumns(['thumbnail', 'status', 'featured', 'action'])
                ->make(true);
        }

        $services = Service::where('status', 1)->get();
        $projectTypes = ProjectType::where('status', 1)->get();
        return view('admin.projects.index', compact('services', 'projectTypes'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'nullable|exists:services,id',
            'project_type_id' => 'required|exists:project_types,id',
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'demo_video' => 'nullable|file|mimetypes:video/mp4,video/webm|max:51200', // 50MB
            'meta_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = new Project;
        $data->service_id = $request->service_id;
        $data->project_type_id = $request->project_type_id;
        $data->title = $request->title;
        $data->slug = Str::slug($request->title) . '-' . Str::random(6);
        $data->sub_title = $request->sub_title;
        $data->project_url = $request->project_url;
        $data->short_desc = $request->short_desc;
        $data->long_desc = $request->long_desc;
        $data->technologies_used = $request->technologies_used;
        $data->functional_features = $request->functional_features;
        $data->sl = $request->sl ?? 0;
        $data->meta_title = $request->meta_title;
        $data->meta_description = $request->meta_description;
        $data->meta_keywords = $request->meta_keywords;
        $data->created_by = auth()->id();

        // Handle main image upload and create thumbnail
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.webp';
            $path = public_path('images/projects/');
            $thumbPath = $path . 'thumbnails/';

            // Ensure directories exist
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            if (!file_exists($thumbPath)) {
                mkdir($thumbPath, 0755, true);
            }

            // Save main image
            Image::make($image)
                ->resize(1200, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 85)
                ->save($path . $imageName);

            $data->image = $imageName;

            // Save thumbnail
            $thumbnailName = 'thumb_' . $imageName;
            Image::make($image)
                ->resize(400, 300, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 80)
                ->save($thumbPath . $thumbnailName);

            $data->thumbnail_image = $thumbnailName;
        }

        // Handle demo video
        if ($request->hasFile('demo_video')) {
            $video = $request->file('demo_video');
            $videoName = 'video_' . time() . '.' . $video->getClientOriginalExtension();
            $path = public_path('images/projects/videos/');

            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            $video->move($path, $videoName);
            $data->demo_video = $videoName;
        }

        // Handle meta image
        if ($request->hasFile('meta_image')) {
            $metaImage = $request->file('meta_image');
            $metaImageName = 'meta_' . time() . '.webp';
            $path = public_path('images/projects/meta/');

            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            Image::make($metaImage)
                ->resize(1200, 630, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 80)
                ->save($path . $metaImageName);

            $data->meta_image = $metaImageName;
        }

        $data->save();

        if ($request->hasFile('slider_images')) {
            $path = public_path('images/projects/sliders/');

            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            foreach ($request->file('slider_images') as $image) {
                $imageName = time() . '_' . uniqid() . '.webp';

                Image::make($image)
                    ->fit(1200, 600)
                    ->encode('webp', 85)
                    ->save($path . $imageName);

                $data->projectSliders()->create([
                    'image' => $imageName,
                    'created_by' => auth()->id()
                ]);
            }
        }

        if ($data->save()) {
            return response()->json([
                'status' => 200,
                'message' => 'Project created successfully.'
            ], 201);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Server error.'
            ], 500);
        }
    }

    public function edit($id)
    {
        $project = Project::with('projectSliders')->find($id);
        if (!$project) {
            return response()->json([
                'status' => 404,
                'message' => 'Project not found'
            ], 404);
        }
        return response()->json($project);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'nullable|exists:services,id',
            'project_type_id' => 'required|exists:project_types,id',
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'demo_video' => 'nullable|file|mimetypes:video/mp4,video/webm|max:51200', // 50MB
            'meta_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $project = Project::find($request->codeid);
        if (!$project) {
            return response()->json([
                'status' => 404,
                'message' => 'Project not found'
            ], 404);
        }

        $project->service_id = $request->service_id;
        $project->project_type_id = $request->project_type_id;
        $project->title = $request->title;
        $project->title = $request->title;
        $project->sub_title = $request->sub_title;
        $project->project_url = $request->project_url;
        $project->short_desc = $request->short_desc;
        $project->long_desc = $request->long_desc;
        $project->technologies_used = $request->technologies_used;
        $project->functional_features = $request->functional_features;
        $project->sl = $request->sl ?? 0;
        $project->meta_title = $request->meta_title;
        $project->meta_description = $request->meta_description;
        $project->meta_keywords = $request->meta_keywords;
        $project->updated_by = auth()->id();

        // Handle main image update and create new thumbnail
        if ($request->hasFile('image')) {
            // Delete old images if they exist
            if ($project->image && file_exists(public_path('images/projects/' . $project->image))) {
                unlink(public_path('images/projects/' . $project->image));
            }
            if ($project->thumbnail_image && file_exists(public_path('images/projects/thumbnails/' . $project->thumbnail_image))) {
                unlink(public_path('images/projects/thumbnails/' . $project->thumbnail_image));
            }

            $image = $request->file('image');
            $imageName = time() . '.webp';
            $path = public_path('images/projects/');

            // Save main image
            Image::make($image)
                ->resize(1200, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 85)
                ->save($path . $imageName);

            $project->image = $imageName;

            // Create and save thumbnail from main image
            $thumbnailName = 'thumb_' . $imageName;
            Image::make($image)
                ->resize(400, 300, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 80)
                ->save($path . 'thumbnails/' . $thumbnailName);

            $project->thumbnail_image = $thumbnailName;
        }

        // Handle demo video update
        if ($request->hasFile('demo_video')) {
            // Delete old video if exists
            if ($project->demo_video && file_exists(public_path('images/projects/videos/' . $project->demo_video))) {
                unlink(public_path('images/projects/videos/' . $project->demo_video));
            }

            $video = $request->file('demo_video');
            $videoName = 'video_' . time() . '.' . $video->getClientOriginalExtension();
            $path = public_path('images/projects/videos/');
            
            $video->move($path, $videoName);
            $project->demo_video = $videoName;
        }

        // Handle meta image update
        if ($request->hasFile('meta_image')) {
            // Delete old meta image if exists
            if ($project->meta_image && file_exists(public_path('images/projects/meta/' . $project->meta_image))) {
                unlink(public_path('images/projects/meta/' . $project->meta_image));
            }

            $metaImage = $request->file('meta_image');
            $metaImageName = 'meta_' . time() . '.webp';
            $path = public_path('images/projects/meta/');
            
            Image::make($metaImage)
                ->resize(1200, 630, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 80)
                ->save($path . $metaImageName);

            $project->meta_image = $metaImageName;
        }

        $project->save();

        if ($request->hasFile('slider_images')) {
            $path = public_path('images/projects/sliders/');
            
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            foreach ($request->file('slider_images') as $image) {
                $imageName = time() . '_' . uniqid() . '.webp';
                
                Image::make($image)
                    ->fit(1200, 600)
                    ->encode('webp', 85)
                    ->save($path . $imageName);

                $project->projectSliders()->create([
                    'image' => $imageName,
                    'created_by' => auth()->id()
                ]);
            }
        }

        if ($project->save()) {
            return response()->json([
                'status' => 200,
                'message' => 'Project updated successfully.'
            ], 200);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Server error.'
            ], 500);
        }
    }

    public function destroy($id)
    {
        $project = Project::find($id);
        
        if (!$project) {
            return response()->json(['success' => false, 'message' => 'Project not found.'], 404);
        }

        // Delete files if they exist
        if ($project->image && file_exists(public_path('images/projects/' . $project->image))) {
            unlink(public_path('images/projects/' . $project->image));
        }
        
        if ($project->thumbnail_image && file_exists(public_path('images/projects/thumbnails/' . $project->thumbnail_image))) {
            unlink(public_path('images/projects/thumbnails/' . $project->thumbnail_image));
        }
        
        if ($project->demo_video && file_exists(public_path('images/projects/videos/' . $project->demo_video))) {
            unlink(public_path('images/projects/videos/' . $project->demo_video));
        }
        
        if ($project->meta_image && file_exists(public_path('images/projects/meta/' . $project->meta_image))) {
            unlink(public_path('images/projects/meta/' . $project->meta_image));
        }

        if ($project->delete()) {
            return response()->json(['success' => true, 'message' => 'Project deleted successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to delete project.'], 500);
    }

    public function toggleStatus(Request $request)
    {
        $project = Project::find($request->project_id);
        if (!$project) {
            return response()->json(['status' => 404, 'message' => 'Project not found']);
        }

        $project->status = $request->status;
        $project->save();

        return response()->json(['status' => 200, 'message' => 'Status updated successfully']);
    }

    public function toggleFeatured(Request $request)
    {
        $project = Project::find($request->project_id);
        if (!$project) {
            return response()->json(['status' => 404, 'message' => 'Project not found']);
        }

        $project->is_featured = $request->is_featured;
        $project->save();

        return response()->json(['status' => 200, 'message' => 'Featured status updated successfully']);
    }

    public function destroySlider($id)
    {
        $slider = ProjectSlider::find($id);
        
        if (!$slider) {
            return response()->json(['success' => false, 'message' => 'Slider not found'], 404);
        }

        if ($slider->image && file_exists(public_path('images/projects/sliders/' . $slider->image))) {
            unlink(public_path('images/projects/sliders/' . $slider->image));
        }

        $slider->delete();
        return response()->json(['success' => true, 'message' => 'Slider deleted successfully']);
    }
}