<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileController extends Controller
{
    public function removeFile(Request $request)
    {
        $request->validate([
            'model' => 'required|string',
            'id' => 'required|integer',
            'col' => 'required|string',
            'path' => 'required|string',
            'filename' => 'required|string',
        ]);

        $modelClass = "App\\Models\\" . $request->model;

        if (!class_exists($modelClass)) {
            return response()->json(['success' => false, 'message' => 'Model not found.'], 404);
        }

        $item = $modelClass::find($request->id);
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Record not found.'], 404);
        }

        $filePath = public_path($request->path . '/' . $request->filename);

        if ($item->{$request->col} && file_exists($filePath)) {
            unlink($filePath);
        }

        $item->{$request->col} = null;
        $item->save();

        return response()->json(['success' => true, 'message' => 'File removed successfully.']);
    }
}