<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClientReview;
use Intervention\Image\Facades\Image;
use Yajra\DataTables\Facades\DataTables;
use Validator;

class ClientReviewController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ClientReview::latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('image', function($row) {
                    if ($row->image) {
                        return '<a href="'.asset("images/client-reviews/".$row->image).'" target="_blank">
                                    <img src="'.asset("images/client-reviews/".$row->image).'" style="max-width:80px; height:auto;">
                                </a>';
                    }
                    return '';
                })
                ->addColumn('sl', function ($row) {
                    return $row->sl ?? '';
                })
                ->addColumn('status', function($row) {
                    $checked = $row->status == 1 ? 'checked' : '';
                    return '<div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input toggle-status" id="customSwitchStatus'.$row->id.'" data-id="'.$row->id.'" '.$checked.'>
                                <label class="custom-control-label" for="customSwitchStatus'.$row->id.'"></label>
                            </div>';
                })
                ->addColumn('action', function($row) {
                    return '
                      <button class="btn btn-sm btn-info edit" data-id="'.$row->id.'">Edit</button>
                      <button class="btn btn-sm btn-danger delete" data-id="'.$row->id.'">Delete</button>
                    ';
                })
                ->rawColumns(['image', 'status', 'action'])
                ->make(true);
        }

        return view('admin.client-reviews.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'review' => 'nullable|string',
            'video_link' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = new ClientReview;
        $data->name = $request->name;
        $data->review = $request->review;
        $data->video_link = $request->video_link;
        $data->sl = $request->sl ?? 0;
        $data->created_by = auth()->id();

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.webp';
            $path = public_path('images/client-reviews/');

            // Ensure directory exists
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            // Save image
            Image::make($image)
                ->resize(200, 200, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 85)
                ->save($path . $imageName);

            $data->image = $imageName;
        }

        if ($data->save()) {
            return response()->json([
                'status' => 200,
                'message' => 'Client review created successfully.'
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
        $review = ClientReview::find($id);
        if (!$review) {
            return response()->json([
                'status' => 404,
                'message' => 'Client review not found'
            ], 404);
        }
        return response()->json($review);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'review' => 'nullable|string',
            'video_link' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $review = ClientReview::find($request->codeid);
        if (!$review) {
            return response()->json([
                'status' => 404,
                'message' => 'Client review not found'
            ], 404);
        }

        $review->name = $request->name;
        $review->review = $request->review;
        $review->video_link = $request->video_link;
        $review->sl = $request->sl ?? 0;
        $review->updated_by = auth()->id();

        // Handle image update
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($review->image && file_exists(public_path('images/client-reviews/' . $review->image))) {
                unlink(public_path('images/client-reviews/' . $review->image));
            }

            $image = $request->file('image');
            $imageName = time() . '.webp';
            $path = public_path('images/client-reviews/');

            // Save image
            Image::make($image)
                ->resize(200, 200, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 85)
                ->save($path . $imageName);

            $review->image = $imageName;
        }

        if ($review->save()) {
            return response()->json([
                'status' => 200,
                'message' => 'Client review updated successfully.'
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
        $review = ClientReview::find($id);
        
        if (!$review) {
            return response()->json(['success' => false, 'message' => 'Client review not found.'], 404);
        }

        // Delete image if exists
        if ($review->image && file_exists(public_path('images/client-reviews/' . $review->image))) {
            unlink(public_path('images/client-reviews/' . $review->image));
        }

        if ($review->delete()) {
            return response()->json(['success' => true, 'message' => 'Client review deleted successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to delete client review.'], 500);
    }

    public function toggleStatus(Request $request)
    {
        $review = ClientReview::find($request->review_id);
        if (!$review) {
            return response()->json(['status' => 404, 'message' => 'Client review not found']);
        }

        $review->status = $request->status;
        $review->save();

        return response()->json(['status' => 200, 'message' => 'Status updated successfully']);
    }
}