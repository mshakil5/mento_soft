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
                    return $row->employee->name ?? 'N/A';
                })
                ->addColumn('task', function($row) {
                    return Str::limit(strip_tags($row->task), 200);
                })
                ->addColumn('due_date', function($row) {
                    return $row->due_date ? Carbon::parse($row->due_date)->format('d-m-Y') : 'N/A';
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
                ->rawColumns(['priority', 'status', 'action'])
                ->make(true);
        }

        $employees = User::where('status', 1)->get();
        return view('admin.client-projects.tasks', compact('project', 'employees'));
    }

    public function store(ClientProject $project, Request $request)
    {
        $validator = Validator::make($request->all(), [
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
            'task' => $request->task,
            'employee_id' => $request->employee_id,
            'priority' => $request->priority,
            'due_date' => $request->due_date,
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

    public function update(Request $request, ProjectTask $task)
    {
        $validator = Validator::make($request->all(), [
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

        $task->task = $request->task;
        $task->employee_id = $request->employee_id;
        $task->priority = $request->priority;
        $task->due_date = $request->due_date;
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

        $task->deleted_by = auth()->id();
        $task->save();

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