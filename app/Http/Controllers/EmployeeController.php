<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;
use Validator;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('date', function($row) {
                    return date('d-m-Y', strtotime($row->created_at));
                })
                ->addColumn('user_type', function($row) {
                    $types = [
                        1 => 'Admin',
                        2 => 'Manager',
                        3 => 'User'
                    ];
                    return $types[$row->is_type] ?? 'Unknown';
                })
                ->addColumn('status', function($row) {
                    $checked = $row->status == 1 ? 'checked' : '';
                    return '<div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input toggle-status" id="customSwitchStatus'.$row->id.'" data-id="'.$row->id.'" '.$checked.'>
                                <label class="custom-control-label" for="customSwitchStatus'.$row->id.'"></label>
                            </div>';
                })
                ->addColumn('action', function($row) {
                    $editBtn = '<button class="btn btn-sm btn-info edit" data-id="'.$row->id.'">Edit</button>';
                    $deleteBtn = auth()->id() !== $row->id
                        ? ' <button class="btn btn-sm btn-danger delete" data-id="'.$row->id.'">Delete</button>'
                        : '';
                    return $editBtn . $deleteBtn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('admin.employees.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'is_type' => 'required|in:1,2,3',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = new User;
        $data->name = $request->name;
        $data->email = $request->email;
        $data->password = Hash::make($request->password);
        $data->is_type = $request->is_type;
        $data->status = 1;
        $data->created_by = auth()->id();

        if ($data->save()) {
            return response()->json([
                'status' => 200,
                'message' => 'Employee created successfully.'
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
        $employee = User::find($id);
        if (!$employee) {
            return response()->json([
                'status' => 404,
                'message' => 'Employee not found'
            ], 404);
        }
        return response()->json($employee);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$request->codeid,
            'password' => 'nullable|string|min:6|confirmed',
            'is_type' => 'required|in:1,2,3',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $employee = User::find($request->codeid);
        if (!$employee) {
            return response()->json([
                'status' => 404,
                'message' => 'Employee not found'
            ], 404);
        }

        $employee->name = $request->name;
        $employee->email = $request->email;
        $employee->is_type = $request->is_type;
        $employee->updated_by = auth()->id();

        if ($request->password) {
            $employee->password = Hash::make($request->password);
        }

        if ($employee->save()) {
            return response()->json([
                'status' => 200,
                'message' => 'Employee updated successfully.'
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
        $employee = User::find($id);
        
        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Employee not found.'], 404);
        }

        $employee->deleted_by = auth()->id();
        $employee->save();

        if ($employee->delete()) {
            return response()->json(['success' => true, 'message' => 'Employee deleted successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to delete employee.'], 500);
    }

    public function toggleStatus(Request $request)
    {
        $employee = User::find($request->employee_id);
        if (!$employee) {
            return response()->json(['status' => 404, 'message' => 'Employee not found']);
        }

        $employee->status = $request->status;
        $employee->save();

        return response()->json(['status' => 200, 'message' => 'Status updated successfully']);
    }
}