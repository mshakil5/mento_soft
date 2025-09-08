<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProjectService;
use Validator;
use Yajra\DataTables\Facades\DataTables;

class ServiceTypeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ProjectService::latest();

            if ($request->status !== null) {
                $data->where('status', $request->status);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function($row) {
                    $checked = $row->status ? 'checked' : '';
                    return '<div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input toggle-status" id="customSwitchStatus'.$row->id.'" data-id="'.$row->id.'" '.$checked.'>
                                <label class="custom-control-label" for="customSwitchStatus'.$row->id.'"></label>
                            </div>';
                })
                ->addColumn('action', function($row) {
                    return '
                        <button class="btn btn-sm btn-info edit" data-id="'.$row->id.'">Edit</button>
                        <button class="btn btn-sm btn-danger delete d-none" data-id="'.$row->id.'">Delete</button>
                    ';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('admin.client-projects.service_types');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'errors' => $validator->errors()], 422);
        }

        $service = ProjectService::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => true,
            'created_by' => auth()->id(),
        ]);

        return response()->json(['status' => 200, 'message' => 'Created successfully.', 'data' => $service], 201);
    }

    public function edit(ProjectService $service)
    {
        if (!$service) {
            return response()->json(['status' => 404, 'message' => 'Service not found'], 404);
        }
        return response()->json($service);
    }

    public function update(Request $request, ProjectService $service)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'errors' => $validator->errors()], 422);
        }

        $service->name = $request->name;
        $service->description = $request->description;
        $service->updated_by = auth()->id();

        if ($service->save()) {
            return response()->json(['status' => 200, 'message' => 'Updated successfully.'], 200);
        }
        return response()->json(['status' => 500, 'message' => 'Server error.'], 500);
    }

    public function destroy(ProjectService $service)
    {
        if (!$service) {
            return response()->json(['success' => false, 'message' => 'Service not found.'], 404);
        }
        if ($service->delete()) {
            return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
        }
        return response()->json(['success' => false, 'message' => 'Failed to delete service.'], 500);
    }

    public function toggleStatus(Request $request, ProjectService $service)
    {
        if (!$service) {
            return response()->json(['status' => 404, 'message' => 'Service not found']);
        }
        $service->status = $request->status;
        $service->save();
        return response()->json(['status' => 200, 'message' => 'Status updated successfully']);
    }
}
