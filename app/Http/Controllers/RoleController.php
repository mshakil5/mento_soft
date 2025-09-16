<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;
use Validator;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Role::with('permissions');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('permissions', function($row){
                    return $row->permissions->pluck('name')->implode(', ');
                })
                ->addColumn('action', function($row) {
                    return '
                        <button class="btn btn-sm btn-info edit" data-id="'.$row->id.'">Edit</button>
                        <button class="btn btn-sm btn-danger delete d-none" data-id="'.$row->id.'">Delete</button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $permissions = Permission::all();
        return view('admin.roles.index', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'required|array|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'errors' => $validator->errors()], 422);
        }

        $role = Role::create(['name' => $request->name]);

        $permissions = Permission::whereIn('id', $request->permissions)->get();
        $role->syncPermissions($permissions);

        return response()->json(['status' => 200, 'message' => 'Role created successfully.']);
    }

    public function edit($id)
    {
        $role = Role::with('permissions')->find($id);
        if (!$role) return response()->json(['status' => 404, 'message' => 'Role not found'], 404);
        return response()->json($role);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codeid' => 'required|exists:roles,id',
            'name' => 'required|string|max:255|unique:roles,name,'.$request->codeid,
            'permissions' => 'required|array|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'errors' => $validator->errors()], 422);
        }

        $role = Role::find($request->codeid);
        $role->update(['name' => $request->name]);

        $permissions = Permission::whereIn('id', $request->permissions)->get();
        $role->syncPermissions($permissions);

        return response()->json(['status' => 200, 'message' => 'Role updated successfully.']);
    }

    public function destroy($id)
    {
        $role = Role::find($id);
        if (!$role) return response()->json(['success' => false, 'message' => 'Role not found.'], 404);

        $role->delete();
        return response()->json(['success' => true, 'message' => 'Role deleted successfully.']);
    }
}