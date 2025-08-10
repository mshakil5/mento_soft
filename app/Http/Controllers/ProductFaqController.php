<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductFaq;
use Yajra\DataTables\Facades\DataTables;
use Validator;

class ProductFaqController extends Controller
{
    public function index(Request $request, Product $product)
    {
        if ($request->ajax()) {
            $data = $product->faqs()->latest();
            return DataTables::of($data)
                ->addIndexColumn()
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
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('admin.products.faqs.index', compact('product'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = ProductFaq::create([
            'product_id' => $request->product_id,
            'question' => $request->question,
            'answer' => $request->answer,
            'sl' => $request->sl ?? 0
        ]);

        if ($data) {
            return response()->json([
                'status' => 200,
                'message' => 'FAQ added successfully.'
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
        $faq = ProductFaq::find($id);
        if (!$faq) {
            return response()->json([
                'status' => 404,
                'message' => 'FAQ not found'
            ], 404);
        }
        return response()->json($faq);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $faq = ProductFaq::find($request->codeid);
        if (!$faq) {
            return response()->json([
                'status' => 404,
                'message' => 'FAQ not found'
            ], 404);
        }

        $faq->question = $request->question;
        $faq->answer = $request->answer;
        $faq->sl = $request->sl ?? 0;

        if ($faq->save()) {
            return response()->json([
                'status' => 200,
                'message' => 'FAQ updated successfully.'
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
        $faq = ProductFaq::find($id);
        
        if (!$faq) {
            return response()->json(['success' => false, 'message' => 'FAQ not found.'], 404);
        }

        if ($faq->delete()) {
            return response()->json(['success' => true, 'message' => 'FAQ deleted successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to delete FAQ.'], 500);
    }

    public function toggleStatus(Request $request)
    {
        $faq = ProductFaq::find($request->faq_id);
        if (!$faq) {
            return response()->json(['status' => 404, 'message' => 'FAQ not found']);
        }

        $faq->status = $request->status;
        $faq->save();

        return response()->json(['status' => 200, 'message' => 'Status updated successfully']);
    }
}