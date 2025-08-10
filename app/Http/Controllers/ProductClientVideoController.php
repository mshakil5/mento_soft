<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductClientVideo;
use Yajra\DataTables\Facades\DataTables;
use Validator;

class ProductClientVideoController extends Controller
{
    public function index(Request $request, Product $product)
    {
        if ($request->ajax()) {
            $data = $product->videos()->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('video_preview', function($row) {
                    return '<div class="video-preview">
                                <video width="150" controls>
                                    <source src="'.asset('images/products/product-clients/'.$row->video_path).'" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>';
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
                ->rawColumns(['video_preview', 'status', 'action'])
                ->make(true);
        }

        return view('admin.products.clients.video', compact('product'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'client_name' => 'required|string|max:255',
            'video' => 'required|file|mimetypes:video/mp4,video/webm|max:51200', // 50MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $video = $request->file('video');
            $videoName = 'client_'.time().'.'.$video->getClientOriginalExtension();
            $path = public_path('images/products/product-clients/');

            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            $video->move($path, $videoName);

            $data = ProductClientVideo::create([
                'product_id' => $request->product_id,
                'client_name' => $request->client_name,
                'video_path' => $videoName,
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Client video added successfully.'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Server error: '.$e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $video = ProductClientVideo::find($id);
        if (!$video) {
            return response()->json([
                'status' => 404,
                'message' => 'Video not found'
            ], 404);
        }
        return response()->json($video);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_name' => 'required|string|max:255',
            'video' => 'nullable|file|mimetypes:video/mp4,video/webm|max:51200',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $video = ProductClientVideo::find($request->codeid);
        if (!$video) {
            return response()->json([
                'status' => 404,
                'message' => 'Video not found'
            ], 404);
        }

        try {
            $video->client_name = $request->client_name;

            if ($request->hasFile('video')) {
                // Delete old video
                if ($video->video_path && file_exists(public_path('images/products/product-clients/'.$video->video_path))) {
                    unlink(public_path('images/products/product-clients/'.$video->video_path));
                }

                $newVideo = $request->file('video');
                $videoName = 'client_'.time().'.'.$newVideo->getClientOriginalExtension();
                $path = public_path('images/products/product-clients/');
                $newVideo->move($path, $videoName);
                $video->video_path = $videoName;
            }

            $video->save();

            return response()->json([
                'status' => 200,
                'message' => 'Client video updated successfully.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Server error: '.$e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $video = ProductClientVideo::find($id);
        
        if (!$video) {
            return response()->json(['success' => false, 'message' => 'Video not found.'], 404);
        }

        try {
            if ($video->video_path && file_exists(public_path('images/products/product-clients/'.$video->video_path))) {
                unlink(public_path('images/products/product-clients/'.$video->video_path));
            }

            $video->delete();

            return response()->json(['success' => true, 'message' => 'Video deleted successfully.']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete video: '.$e->getMessage()], 500);
        }
    }

    public function toggleStatus(Request $request)
    {
        $video = ProductClientVideo::find($request->video_id);
        if (!$video) {
            return response()->json(['status' => 404, 'message' => 'Video not found']);
        }

        $video->status = $request->status;
        $video->save();

        return response()->json(['status' => 200, 'message' => 'Status updated successfully']);
    }
}