<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FaqQuestion;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class FAQController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = FaqQuestion::orderBy('id', 'DESC');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                        <button class="btn btn-sm btn-info edit" data-id="' . $row->id . '">Edit</button>
                        <button class="btn btn-sm btn-danger delete" data-id="' . $row->id . '">Delete</button>
                    ';
                })
                ->rawColumns(['action', 'answer'])
                ->make(true);
        }

        return view('admin.faq_questions.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required|string|max:255',
            'answer'   => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = new FaqQuestion;
        $data->question = $request->question;
        $data->answer = $request->answer;
        $data->created_by = auth()->id();

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

    public function edit($id)
    {
        $faq = FaqQuestion::findOrFail($id);
        return response()->json($faq);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required|string|max:255',
            'answer'   => 'required|string',
            'codeid'       => 'required|exists:faq_questions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $faq = FaqQuestion::findOrFail($request->codeid);
        $faq->question = $request->question;
        $faq->answer = $request->answer;
        $faq->updated_by = auth()->id();

        if ($faq->save()) {
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

    public function delete($id)
    {
        $faq = FaqQuestion::findOrFail($id);

        if ($faq->delete()) {
            return response()->json([
                'status' => 200,
                'message' => 'Deleted successfully.'
            ], 200);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Server error.'
            ], 500);
        }
    }
}
