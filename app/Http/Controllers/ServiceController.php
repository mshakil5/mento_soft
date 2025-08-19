<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Yajra\DataTables\Facades\DataTables;
use Validator;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    public function getService(Request $request)
    {
        if ($request->ajax()) {
            $data = Service::latest();
            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('icon', function($row) {
                if ($row->icon) {
                    return '<a href="'.asset("images/service/icon/".$row->icon).'" target="_blank">
                                <img src="'.asset("images/service/icon/".$row->icon).'" style="max-width:50px; height:auto;">
                            </a>';
                }
                return '';
            })
            ->addColumn('image', function($row) {
                if ($row->image) {
                    return '<a href="'.asset("images/service/".$row->image).'" target="_blank">
                                <img src="'.asset("images/service/".$row->image).'" style="max-width:100px; height:auto;">
                            </a>';
                }
                return '';
            })
            // ->addColumn('video', function($row) {
            //     if ($row->video) {
            //         return '<video width="160" controls>
            //                     <source src="'.asset("images/service/videos/".$row->video).'" type="video/mp4">
            //                     Your browser does not support the video tag.
            //                 </video>';
            //     }
            //     return '';
            // })
            ->addColumn('status', function($row) {
                $checked = $row->status == 1 ? 'checked' : '';
                return '<div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input toggle-status" id="customSwitchStatus'.$row->id.'" data-id="'.$row->id.'" '.$checked.'>
                            <label class="custom-control-label" for="customSwitchStatus'.$row->id.'"></label>
                        </div>';
            })
            ->addColumn('title', function($row) {
                return $row->title;
            })
            ->addColumn('sl', function ($row) {
                return $row->sl ?? '';
            })
            ->addColumn('action', function($row) {
                return '
                  <button class="btn btn-sm btn-info edit" data-id="'.$row->id.'">Edit</button>
                  <button class="btn btn-sm btn-danger delete" data-id="'.$row->id.'">Delete</button>
                ';
            })
            ->rawColumns(['icon', 'image', 'video', 'status', 'action'])
            ->make(true);

        }

        return view('admin.service.index');
    }

    public function serviceStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255', Rule::unique('services')->whereNull('deleted_at')],
            'video' => 'nullable|file|mimetypes:video/mp4,video/webm|max:51200',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $data = new Service;
        $data->sl = $request->sl;
        $data->title = $request->title;
        $data->slug = Str::slug($request->title);
        $data->short_desc = $request->short_desc;
        $data->long_desc = $request->long_desc;
        $data->youtube_link = $request->youtube_link;
        $data->meta_title = $request->meta_title;
        $data->meta_description = $request->meta_description;
        $data->meta_keywords = $request->meta_keywords;
        $data->created_by = auth()->id();

        // Icon upload
        if ($request->hasFile('icon')) {
            $uploadedFile = $request->file('icon');
            $iconName = mt_rand(10000000, 99999999) . '.webp';
            $destinationPath = public_path('images/service/icon/');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            Image::make($uploadedFile)
                ->resize(100, 100, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 80)
                ->save($destinationPath . $iconName);

            $data->icon = $iconName;
        }

        // Image upload
        if ($request->hasFile('image')) {
            $uploadedFile = $request->file('image');
            $imageName = mt_rand(10000000, 99999999) . '.webp';
            $destinationPath = public_path('images/service/');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            Image::make($uploadedFile)
                ->resize(800, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 80)
                ->save($destinationPath . $imageName);

            $data->image = $imageName;
        }

        // Video upload
        if ($request->hasFile('video')) {
            $uploadedFile = $request->file('video');
            $videoName = 'video_' . mt_rand(10000000, 99999999) . '.' . $uploadedFile->getClientOriginalExtension();
            $destinationPath = public_path('images/service/videos/');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $uploadedFile->move($destinationPath, $videoName);
            $data->video = $videoName;
        }

        // Meta image upload
        if ($request->hasFile('meta_image')) {
            $uploadedFile = $request->file('meta_image');
            $metaImageName = mt_rand(10000000, 99999999) . '.webp';
            $destinationPath = public_path('images/service/meta/');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            Image::make($uploadedFile)
                ->resize(1200, 630, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 80)
                ->save($destinationPath . $metaImageName);

            $data->meta_image = $metaImageName;
        }

        if ($data->save()) {
            return response()->json([
                'status' => 200,
                'message' => 'Created successfully.'
            ], 201);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Server error.'
            ], 500);
        }
    }

    public function serviceEdit($id)
    {
        return response()->json(Service::find($id));
    }

    public function serviceUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|' . Rule::unique('services', 'title')->ignore($request->codeid)->whereNull('deleted_at'),
            'video' => 'nullable|file|mimetypes:video/mp4,video/webm|max:51200',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $service = Service::find($request->codeid);
        $service->sl = $request->sl;
        $service->title = $request->title;
        $service->slug = Str::slug($request->title);
        $service->short_desc = $request->short_desc;
        $service->long_desc = $request->long_desc;
        $service->youtube_link = $request->youtube_link;
        $service->meta_title = $request->meta_title;
        $service->meta_description = $request->meta_description;
        $service->meta_keywords = $request->meta_keywords;
        $service->updated_by = auth()->id();

        // Icon update
        if ($request->hasFile('icon')) {
            $uploadedFile = $request->file('icon');

            if ($service->icon && file_exists(public_path('images/service/icon/' . $service->icon))) {
                unlink(public_path('images/service/icon/' . $service->icon));
            }

            $iconName = mt_rand(10000000, 99999999) . '.webp';
            $destinationPath = public_path('images/service/icon/');

            Image::make($uploadedFile)
                ->resize(100, 100, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 80)
                ->save($destinationPath . $iconName);

            $service->icon = $iconName;
        }

        // Image update
        if ($request->hasFile('image')) {
            $uploadedFile = $request->file('image');

            if ($service->image && file_exists(public_path('images/service/' . $service->image))) {
                unlink(public_path('images/service/' . $service->image));
            }

            $imageName = mt_rand(10000000, 99999999) . '.webp';
            $destinationPath = public_path('images/service/');

            Image::make($uploadedFile)
                ->resize(800, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 80)
                ->save($destinationPath . $imageName);

            $service->image = $imageName;
        }

        // Video update
        if ($request->hasFile('video')) {
            // Delete old video if exists
            if ($service->video && file_exists(public_path('images/service/videos/' . $service->video))) {
                unlink(public_path('images/service/videos/' . $service->video));
            }

            $uploadedFile = $request->file('video');
            $videoName = 'video_' . mt_rand(10000000, 99999999) . '.' . $uploadedFile->getClientOriginalExtension();
            $destinationPath = public_path('images/service/videos/');

            $uploadedFile->move($destinationPath, $videoName);
            $service->video = $videoName;
        }

        // Meta image update
        if ($request->hasFile('meta_image')) {
            $uploadedFile = $request->file('meta_image');

            if ($service->meta_image && file_exists(public_path('images/service/meta/' . $service->meta_image))) {
                unlink(public_path('images/service/meta/' . $service->meta_image));
            }

            $metaImageName = mt_rand(10000000, 99999999) . '.webp';
            $destinationPath = public_path('images/service/meta/');

            Image::make($uploadedFile)
                ->resize(1200, 630, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 80)
                ->save($destinationPath . $metaImageName);

            $service->meta_image = $metaImageName;
        }

        if ($service->save()) {
            return response()->json([
                'status' => 200,
                'message' => 'Updated successfully.'
            ], 200);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Server error.'
            ], 500);
        }
    }

    public function serviceDelete($id)
    {
        $service = Service::find($id);
        
        if (!$service) {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        // Delete icon file if exists
        if ($service->icon && file_exists(public_path('images/service/icon/' . $service->icon))) {
            unlink(public_path('images/service/icon/' . $service->icon));
        }

        // Delete image file if exists
        if ($service->image && file_exists(public_path('images/service/' . $service->image))) {
            unlink(public_path('images/service/' . $service->image));
        }

        if ($service->video && file_exists(public_path('images/service/videos/' . $service->video))) {
            unlink(public_path('images/service/videos/' . $service->video));
        }

        // Delete meta image file if exists
        if ($service->meta_image && file_exists(public_path('images/service/meta/' . $service->meta_image))) {
            unlink(public_path('images/service/meta/' . $service->meta_image));
        }

        if ($service->delete()) {
            return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to delete.'], 500);
        }
    }

    public function toggleStatus(Request $request)
    {
        $service = Service::find($request->service_id);
        if (!$service) {
            return response()->json(['status' => 404, 'message' => 'Service not found']);
        }

        $service->status = $request->status;
        $service->save();

        return response()->json(['status' => 200, 'message' => 'Status updated successfully']);
    }
}