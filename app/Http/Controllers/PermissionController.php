<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;
use Validator;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Permission::latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row) {
                    return '
                        <button class="btn btn-sm btn-info edit" data-id="'.$row->id.'">Edit</button>
                        <button class="btn btn-sm btn-danger delete d-none" data-id="'.$row->id.'">Delete</button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.permissions.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:permissions,name'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'errors' => $validator->errors()], 422);
        }

        Permission::create(['name' => $request->name]);

        return response()->json(['status' => 200, 'message' => 'Permission created successfully.']);
    }

    public function edit($id)
    {
        $permission = Permission::find($id);
        if (!$permission) return response()->json(['status' => 404, 'message' => 'Permission not found'], 404);
        return response()->json($permission);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:permissions,name,'.$request->codeid
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'errors' => $validator->errors()], 422);
        }

        $permission = Permission::find($request->codeid);
        if (!$permission) return response()->json(['status' => 404, 'message' => 'Permission not found'], 404);

        $permission->update(['name' => $request->name]);

        return response()->json(['status' => 200, 'message' => 'Permission updated successfully.']);
    }

    public function destroy($id)
    {
        $permission = Permission::find($id);
        if (!$permission) return response()->json(['success' => false, 'message' => 'Permission not found.'], 404);

        $permission->delete();
        return response()->json(['success' => true, 'message' => 'Permission deleted successfully.']);
    }
}