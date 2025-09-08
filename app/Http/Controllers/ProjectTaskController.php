<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProjectTask;
use App\Models\ClientProject;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;
use Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ProjectTaskController extends Controller
{
    public function index(ClientProject $project, Request $request)
    {
        if ($request->ajax()) {

            $data = ProjectTask::with(['employee'])
                ->where('client_project_id', $project->id)
                ->latest();

            if ($request->status) {
                $data->where('status', $request->status);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('date', function($row) {
                    return Carbon::parse($row->created_at)->format('d-m-Y');
                })
                ->addColumn('employee_name', function($row) {
                    return $row->employee->name ?? '';
                })
                ->addColumn('due_date', function($row) {
                    return $row->due_date ? Carbon::parse($row->due_date)->format('d-m-Y') : '';
                })
                ->addColumn('priority', function($row) {
                    $badgeClass = [
                        'high' => 'bg-danger',
                        'medium' => 'bg-warning',
                        'low' => 'bg-info'
                    ][$row->priority] ?? 'bg-secondary';
                    
                    return '<span class="badge '.$badgeClass.'">'.ucfirst($row->priority).'</span>';
                })
                ->addColumn('status', function($row) {
                    if ($row->is_confirmed == 1) {
                        return '<span class="badge bg-success"><i class="fas fa-check-circle"></i> Confirmed by Client</span>';
                    }

                    $statuses = [
                        1 => 'To Do',
                        2 => 'In Progress',
                        3 => 'Done'
                    ];

                    $currentStatus = $statuses[$row->status] ?? 'Unknown';

                    $html = '
                    <div class="dropdown">
                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" 
                            id="statusDropdown'.$row->id.'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            '.$currentStatus.'
                        </button>
                        <div class="dropdown-menu" aria-labelledby="statusDropdown'.$row->id.'">';

                        foreach ($statuses as $value => $label) {
                            $html .= '<a class="dropdown-item status-change" href="#" data-id="'.$row->id.'" data-status="'.$value.'">'.$label.'</a>';
                        }

                    $html .= '
                        </div>
                    </div>';

                    return $html;
                })
                ->addColumn('action', function($row) {
                    $html = '
                      <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#taskModal'.$row->id.'">View</button>
                      <a href="'.route('client-projects-task.edit-page', $row->id).'" class="btn btn-sm btn-primary">Edit</a>
                      <button class="btn btn-sm btn-danger delete d-none" data-id="'.$row->id.'">Delete</button>

                      <div class="modal fade" id="taskModal'.$row->id.'" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title">'.e($row->title ?? '').'</h5>
                              <a href="'.route('client-projects-task.edit-page', $row->id).'" class="ml-2 text-info" title="Edit Task">
                                  <i class="fas fa-edit"></i>
                              </a>
                              <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                              <div class="list-group-item mb-3">
                                '.$row->task.'
                                <div class="small text-muted mt-1">
                                  <span><strong>Due:</strong> '.($row->due_date ? \Carbon\Carbon::parse($row->due_date)->format("d-m-Y") : "").'</span> &middot;
                                  <span><strong>Status:</strong> '.([1=>"To Do",2=>"In Progress",3=>"Done"][$row->status] ?? "").'</span> &middot;
                                  <span><strong>Priority:</strong> <span class="badge '.(['high'=>'bg-danger','medium'=>'bg-warning','low'=>'bg-info'][$row->priority] ?? 'bg-secondary').'">'.ucfirst($row->priority ?? "").'</span></span> &middot;
                                  <span><strong>Assigned to:</strong> '.($row->employee->name ?? "Unassigned").'</span> &middot;
                                  <span><strong>Created by:</strong> '.($row->creator->name ?? "-").'</span> &middot; 
                                  '.($row->is_confirmed == 1 ? '<span class="badge bg-success"><i class="fas fa-check-circle"></i> Confirmed</span>' : '').' &middot;
                                  <span><strong>Project:</strong> '.($row->clientProject->title ?? "").'</span>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    ';
                    return $html;
                })
                ->rawColumns(['priority', 'status', 'action'])
                ->make(true);
        }

        $employees = User::where('status', 1)->where('user_type', 1)->latest()->get();
        return view('admin.client-projects.tasks', compact('project', 'employees'));
    }

    public function store(ClientProject $project, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'task' => 'required|string',
            'employee_id' => 'required|exists:users,id',
            'priority' => 'required|in:high,medium,low',
            'due_date' => ['required', 'date', 'after_or_equal:today'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = ProjectTask::create([
            'client_project_id' => $project->id,
            'client_id' => $project->client_id,
            'title' => $request->title,
            'task' => $request->task,
            'employee_id' => $request->employee_id,
            'priority' => $request->priority,
            'due_date' => $request->due_date,
            'status' => $request->status,
            'allow_client'  => $request->input('allow_client', 0),
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Task created successfully.',
            'data' => $data
        ], 201);
    }

    public function edit(ProjectTask $task)
    {
        if (!$task) {
            return response()->json([
                'status' => 404,
                'message' => 'Task not found'
            ], 404);
        }
        return response()->json($task);
    }

    public function editPage(ProjectTask $task)
    {
        $employees = User::where('status', 1)->where('user_type', 1)->select('id', 'name')->get();
        return view('admin.client-projects.edit-task', compact('task', 'employees'));
    }

    public function update(Request $request, ProjectTask $task)
    {
        $validator = Validator::make($request->all(), [
            'task' => 'required|string',
            'employee_id' => 'required|exists:users,id',
            'priority' => 'required|in:high,medium,low',
            'due_date' => ['required', 'date'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $task->title = $request->title;
        $task->task = $request->task;
        $task->employee_id = $request->employee_id;
        $task->priority = $request->priority;
        $task->status = $request->status;
        $task->due_date = $request->due_date;
        $task->allow_client = $request->input('allow_client', 0);
        $task->updated_by = auth()->id();

        if ($task->save()) {
            return response()->json([
                'status' => 200,
                'message' => 'Task updated successfully.'
            ], 200);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Server error.'
            ], 500);
        }
    }

    public function destroy(ProjectTask $task)
    {
        if (!$task) {
            return response()->json(['success' => false, 'message' => 'Task not found.'], 404);
        }

        if ($task->delete()) {
            return response()->json(['success' => true, 'message' => 'Task deleted successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to delete task.'], 500);
    }

    public function toggleStatus(Request $request, ProjectTask $task)
    {
        if (!$task) {
            return response()->json(['status' => 404, 'message' => 'Task not found']);
        }

        $task->status = $request->status;
        $task->save();

        return response()->json(['status' => 200, 'message' => 'Status updated successfully']);
    }
}