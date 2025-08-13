<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductClient;
use Intervention\Image\Facades\Image;
use Yajra\DataTables\Facades\DataTables;
use Validator;

class ProductClientController extends Controller
{
    public function index(Request $request, Product $product)
    {
        if ($request->ajax()) {
            $data = $product->clients()->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('image', function($row) {
                    return '<a href="'.asset("images/product-clients/".$row->image).'" target="_blank">
                                <img src="'.asset("images/product-clients/".$row->image).'" style="max-width:80px; height:auto;">
                            </a>';
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
                    return '<button class="btn btn-sm btn-danger delete" data-id="'.$row->id.'">Delete</button>';
                })
                ->rawColumns(['image', 'status', 'action'])
                ->make(true);
        }

        return view('admin.products.clients.index', compact('product'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $image = $request->file('image');
        $imageName = time() . '.webp';
        $path = public_path('images/product-clients/');

        // Ensure directory exists
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        // Save image
        Image::make($image)
        ->fit(150, 90)
        ->encode('webp', 85)
        ->save($path . $imageName);

        $data = ProductClient::create([
            'product_id' => $request->product_id,
            'image' => $imageName,
            'sl' => $request->sl ?? 0
        ]);

        if ($data) {
            return response()->json([
                'status' => 200,
                'message' => 'Client added successfully.'
            ], 201);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Server error.'
            ], 500);
        }
    }

    public function destroy($id)
    {
        $client = ProductClient::find($id);
        
        if (!$client) {
            return response()->json(['success' => false, 'message' => 'Client not found.'], 404);
        }

        // Delete image if exists
        if ($client->image && file_exists(public_path('images/product-clients/' . $client->image))) {
            unlink(public_path('images/product-clients/' . $client->image));
        }

        if ($client->delete()) {
            return response()->json(['success' => true, 'message' => 'Client deleted successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to delete client.'], 500);
    }

    public function toggleStatus(Request $request)
    {
        $client = ProductClient::find($request->client_id);
        if (!$client) {
            return response()->json(['status' => 404, 'message' => 'Client not found']);
        }

        $client->status = $request->status;
        $client->save();

        return response()->json(['status' => 200, 'message' => 'Status updated successfully']);
    }
}