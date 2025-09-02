<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;
use Validator;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $employee = User::with('roles')->where('user_type', 1)->latest();
            return DataTables::of($employee)
                ->addIndexColumn()
                ->addColumn('date', function($row) {
                    return date('d-m-Y', strtotime($row->created_at));
                })
                ->addColumn('role', function($row) {
                    return $row->roles->first() ? $row->roles->first()->name : '';
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

                    $details = view('admin.employees.details-modal', ['row' => $row])->render();

                    $detailsBtn = ' <button class="btn btn-sm btn-secondary" data-toggle="modal" data-target="#detailsModal-'.$row->id.'">Details</button>';

                    return $editBtn . $deleteBtn . $detailsBtn . $details;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        
        $roles = Role::all();
        return view('admin.employees.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'contact_no' => 'required',
            'role_id' => 'required',
            'password' => 'required|string|min:6|confirmed',
            'nid' => 'nullable|file'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $employee = new User;
        $employee->name = $request->name;
        $employee->email = $request->email;
        $employee->contact_no = $request->contact_no;
        $employee->joining_date = $request->joining_date;
        $employee->em_contact_person = $request->em_contact_person;
        $employee->em_contact_no = $request->em_contact_no;
        $employee->address = $request->address;
        $employee->salary = $request->salary;
        $employee->bank_details = $request->bank_details;
        $employee->password = Hash::make($request->password);
        $employee->user_type = 1;
        $employee->status = 1;
        $employee->created_by = auth()->id();

        if ($request->hasFile('nid')) {
            $nidFile = $request->file('nid');
            $nidName = time() . '_' . $nidFile->getClientOriginalName();
            $path = public_path('images/employees/');
            if (!file_exists($path)) mkdir($path, 0755, true);
            $nidFile->move($path, $nidName);
            $employee->nid = $nidName;
        }

        if ($employee->save()) {
            $role = Role::findOrFail($request->role_id);
            $employee->syncRoles([$role->name]); 
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
        $employee = User::with('roles')->find($id);
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
            'password' => 'nullable|string|min:6|confirmed'
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
        $employee->contact_no = $request->contact_no;
        $employee->joining_date = $request->joining_date;
        $employee->em_contact_person = $request->em_contact_person;
        $employee->em_contact_no = $request->em_contact_no;
        $employee->address = $request->address;
        $employee->salary = $request->salary;
        $employee->bank_details = $request->bank_details;
        $employee->updated_by = auth()->id();

        if ($request->password) {
            $employee->password = Hash::make($request->password);
        }

        if ($request->hasFile('nid')) {
            $nidFile = $request->file('nid');
            $nidName = time() . '_' . $nidFile->getClientOriginalName();
            $path = public_path('images/employees/');
            if (!file_exists($path)) mkdir($path, 0755, true);

            if ($employee->nid && file_exists($path . $employee->nid)) {
                unlink($path . $employee->nid);
            }

            $nidFile->move($path, $nidName);
            $employee->nid = $nidName;
        }

        if ($employee->save()) {
            $role = Role::findOrFail($request->role_id);
            $employee->syncRoles([$role->name]); 
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