<?php

namespace App\Http\Controllers;

use App\Models\Master;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class MetaController extends Controller
{
    
    public function index()
    {
        
        $data = Master::select('id','name', 'softcode_id', 'meta_image','short_title','long_title', 'meta_title', 'meta_description', 'meta_keywords')->orderby('id','DESC')->where('softcode_id', 7)->get();


        return view('admin.meta.index', compact('data'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category' => 'required|string|max:255|unique:masters,name',
            'short_title' => 'nullable|string',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'required|string',
            'meta_image' => 'nullable|image|max:10240',
        ]);

         if ($validator->fails()) {
            $errorMessage = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>" . implode(", ", $validator->errors()->all()) . "</b></div>";
            return response()->json(['status' => 400, 'message' => $errorMessage]);
        }


        $metadata = new Master;
        $metadata->name = $request->category;
        $metadata->softcode_id = 7;
        $metadata->short_title = $request->short_title;
        $metadata->meta_title = $request->meta_title;
        $metadata->meta_description = $request->meta_description;
        $metadata->meta_keywords = $request->meta_keywords;
        $metadata->created_by = auth()->user()->id;

        if ($request->hasFile('meta_image')) {
            $uploadedFile = $request->file('meta_image');
            $randomName = mt_rand(10000000, 99999999). '.'. $uploadedFile->getClientOriginalExtension();
            $destinationPath = public_path('images/meta/');
            $path = $uploadedFile->move($destinationPath, $randomName); 
            $metadata->meta_image = $randomName;
        }

        $metadata->save();

        $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Created Successfully.</b></div>";

        return response()->json(['status'=> 300,'message'=>$message]);
    }

    public function edit($id)
    {
        $info = Master::where('id', $id)->first();
        return response()->json($info);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category' => 'required|string|max:255|unique:masters,name,' . $request->codeid,
            'short_title' => 'nullable|string',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'required|string',
            'meta_image' => 'nullable|image|max:10240',
        ]);

        if ($validator->fails()) {
            $errorMessage = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>" . implode(", ", $validator->errors()->all()) . "</b></div>";
            return response()->json(['status' => 400, 'message' => $errorMessage]);
        }

        $metadata = Master::find($request->codeid);

        if ($request->hasFile('meta_image')) {
            $uploadedFile = $request->file('meta_image');
            $randomName = mt_rand(10000000, 99999999). '.'. $uploadedFile->getClientOriginalExtension();
            $destinationPath = public_path('images/meta/');
            $path = $uploadedFile->move($destinationPath, $randomName); 
            $metadata->meta_image = $randomName;
        }

        $metadata->name = $request->category;
        $metadata->short_title = $request->short_title;
        $metadata->meta_title = $request->meta_title;
        $metadata->meta_description = $request->meta_description;
        $metadata->meta_keywords = $request->meta_keywords;
        $metadata->updated_by = auth()->user()->id;
        $metadata->save();

        $message = "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Updated Successfully.</b></div>";

        return response()->json(['status' => 300, 'message' => $message, 'short_description' => $request->short_description]);
    }

    public function delete($id)
    {
        $metadata = Master::find($id);

        if (!$metadata) {
            return response()->json(['success' => false, 'message' => 'Data not found.']);
        }


        if ($metadata->feature_image && file_exists(public_path('images/products/' . $metadata->feature_image))) {
            unlink(public_path('images/products/' . $metadata->feature_image));
        }

        $metadata->delete();

        return response()->json(['success' => true, 'message' => 'Data  deleted successfully.']);
    }
}
