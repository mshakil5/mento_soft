<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductFeature;
use Intervention\Image\Facades\Image;
use Yajra\DataTables\Facades\DataTables;
use Validator;

class ProductFeatureController extends Controller
{
    public function index(Request $request, Product $product)
    {
        if ($request->ajax()) {
            $data = $product->features()->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('image', function($row) {
                    if ($row->image) {
                        return '<a href="'.asset("images/product-features/".$row->image).'" target="_blank">
                                    <img src="'.asset("images/product-features/".$row->image).'" style="max-width:80px; height:auto;">
                                </a>';
                    }
                    return '';
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

        return view('admin.products.features.index', compact('product'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = new ProductFeature;
        $data->product_id = $request->product_id;
        $data->title = $request->title;
        $data->icon = $request->icon;
        $data->description = $request->description;
        $data->sl = $request->sl ?? 0;

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.webp';
            $path = public_path('images/product-features/');

            // Ensure directory exists
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            // Save image
            Image::make($image)
                ->resize(800, null, function ($constraint) {
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
                'message' => 'Product feature created successfully.'
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
        $feature = ProductFeature::find($id);
        if (!$feature) {
            return response()->json([
                'status' => 404,
                'message' => 'Feature not found'
            ], 404);
        }
        return response()->json($feature);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $feature = ProductFeature::find($request->codeid);
        if (!$feature) {
            return response()->json([
                'status' => 404,
                'message' => 'Feature not found'
            ], 404);
        }

        $feature->title = $request->title;
        $feature->icon = $request->icon;
        $feature->description = $request->description;
        $feature->sl = $request->sl ?? 0;

        // Handle image update
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($feature->image && file_exists(public_path('images/product-features/' . $feature->image))) {
                unlink(public_path('images/product-features/' . $feature->image));
            }

            $image = $request->file('image');
            $imageName = time() . '.webp';
            $path = public_path('images/product-features/');

            // Save image
            Image::make($image)
                ->resize(800, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 85)
                ->save($path . $imageName);

            $feature->image = $imageName;
        }

        if ($feature->save()) {
            return response()->json([
                'status' => 200,
                'message' => 'Product feature updated successfully.'
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
        $feature = ProductFeature::find($id);
        
        if (!$feature) {
            return response()->json(['success' => false, 'message' => 'Feature not found.'], 404);
        }

        // Delete image if exists
        if ($feature->image && file_exists(public_path('images/product-features/' . $feature->image))) {
            unlink(public_path('images/product-features/' . $feature->image));
        }

        if ($feature->delete()) {
            return response()->json(['success' => true, 'message' => 'Feature deleted successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to delete feature.'], 500);
    }

    public function toggleStatus(Request $request)
    {
        $feature = ProductFeature::find($request->feature_id);
        if (!$feature) {
            return response()->json(['status' => 404, 'message' => 'Feature not found']);
        }

        $feature->status = $request->status;
        $feature->save();

        return response()->json(['status' => 200, 'message' => 'Status updated successfully']);
    }
}