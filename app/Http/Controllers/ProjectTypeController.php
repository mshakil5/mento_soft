<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProjectType;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Validator;

class ProjectTypeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ProjectType::latest();
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

        return view('admin.project-types.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:project_types,name',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = new ProjectType;
        $data->name = $request->name;
        $data->slug = Str::slug($request->name);
        $data->description = $request->description;
        $data->created_by = auth()->id();

        if ($data->save()) {
            return response()->json([
                'status' => 200,
                'message' => 'Project Type created successfully.'
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
        $projectType = ProjectType::find($id);
        if (!$projectType) {
            return response()->json([
                'status' => 404,
                'message' => 'Project Type not found'
            ], 404);
        }
        return response()->json($projectType);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:project_types,name,'.$request->codeid,
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $projectType = ProjectType::find($request->codeid);
        if (!$projectType) {
            return response()->json([
                'status' => 404,
                'message' => 'Project Type not found'
            ], 404);
        }

        $projectType->name = $request->name;
        $projectType->slug = Str::slug($request->name);
        $projectType->description = $request->description;
        $projectType->updated_by = auth()->id();

        if ($projectType->save()) {
            return response()->json([
                'status' => 200,
                'message' => 'Project Type updated successfully.'
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
        $projectType = ProjectType::find($id);
        
        if (!$projectType) {
            return response()->json(['success' => false, 'message' => 'Project Type not found.'], 404);
        }

        $projectType->deleted_by = auth()->id();
        $projectType->save();

        if ($projectType->delete()) {
            return response()->json(['success' => true, 'message' => 'Project Type deleted successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to delete Project Type.'], 500);
    }

    public function toggleStatus(Request $request)
    {
        $projectType = ProjectType::find($request->project_type_id);
        if (!$projectType) {
            return response()->json(['status' => 404, 'message' => 'Project Type not found']);
        }

        $projectType->status = $request->status;
        $projectType->save();

        return response()->json(['status' => 200, 'message' => 'Status updated successfully']);
    }
}