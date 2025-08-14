<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClientProject;
use App\Models\ProjectService;
use Validator;
use Yajra\DataTables\Facades\DataTables;

class ProjectServiceController extends Controller
{
    public function index(ClientProject $project, Request $request)
    {
          if ($request->ajax()) {
              $today = \Carbon\Carbon::today();
              $warningDate = $today->copy()->addWeek();

              $data = ProjectService::with('details')
                  ->withCount('details')
                  ->with(['details' => function($q) {
                      $q->where('status', 1)
                        ->orderByDesc('end_date')
                        ->limit(1);
                  }])
                  ->where('client_project_id', $project->id)
                  ->latest();

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
                  ->addColumn('action', function($row) use ($today, $warningDate) {
                      $detailsUrl = route('client-project-services.details', $row->id);

                      $total = $row->details_count;

                      $lastDetail = $row->details->first();
                      $statusBadge = '';

                      if ($lastDetail) {
                          if ($lastDetail->end_date < $today) {
                              $statusBadge = "<span class='badge badge-danger ml-1'>Expired</span>";
                          } elseif ($lastDetail->end_date >= $today && $lastDetail->end_date <= $warningDate) {
                              $statusBadge = "<span class='badge badge-warning ml-1'>Near Expiry</span>";
                          } else {
                              $statusBadge = "<span class='badge badge-success ml-1'>Active</span>";
                          }
                      }

                      $countBadge = $total ? "<span class='badge badge-light ml-1'>{$total}</span>" : '';

                      return '
                          <a href="'.$detailsUrl.'" class="btn btn-sm btn-primary">
                              Details '.$countBadge.' '.$statusBadge.'
                          </a>
                          <button class="btn btn-sm btn-info edit" data-id="'.$row->id.'">Edit</button>
                          <button class="btn btn-sm btn-danger delete" data-id="'.$row->id.'">Delete</button>
                      ';
                  })
                  ->rawColumns(['status', 'action'])
                  ->make(true);
          }

        return view('admin.client-projects.services', compact('project'));
    }

    public function store(ClientProject $project, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'errors' => $validator->errors()], 422);
        }

        $service = ProjectService::create([
            'client_project_id' => $project->id,
            'name' => $request->name,
            'description' => $request->description,
            'status' => true,
            'created_by' => auth()->id(),
        ]);

        return response()->json(['status' => 200, 'message' => 'Service created successfully.', 'data' => $service], 201);
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
            return response()->json(['status' => 200, 'message' => 'Service updated successfully.'], 200);
        }
        return response()->json(['status' => 500, 'message' => 'Server error.'], 500);
    }

    public function destroy(ProjectService $service)
    {
        if (!$service) {
            return response()->json(['success' => false, 'message' => 'Service not found.'], 404);
        }
        if ($service->delete()) {
            return response()->json(['success' => true, 'message' => 'Service deleted successfully.']);
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