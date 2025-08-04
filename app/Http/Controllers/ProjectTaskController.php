<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProjectTask;
use App\Models\User;
use App\Models\ClientProject;

class ProjectTaskController extends Controller
{
    public function getByProject($project_id)
    {
        $tasks = ProjectTask::where('client_project_id', $project_id)->latest()->get();
        $employees = User::where('status', 1)->get();
        $authId = auth()->id();
        $project = ClientProject::findOrFail($project_id);

        $html = '<div class="container-fluid p-0">';

        foreach ($tasks as $index => $task) {
            $sl = $index + 1;
            $dueDateVal = $task->due_date ? date('Y-m-d', strtotime($task->due_date)) : '';
            $today = date('Y-m-d');

            $taskBgClass = '';
            if ($dueDateVal == $today) {
                $taskBgClass = 'bg-warning';
            } elseif ($dueDateVal < $today && $dueDateVal != '') {
                $taskBgClass = 'bg-danger text-white';
            }

            $checked = $task->status == 1 ? 'checked' : '';
            $disabled = $task->employee_id != $authId ? 'disabled' : '';

            $employeeOptions = '';
            foreach ($employees as $emp) {
                $selected = $task->employee_id == $emp->id ? 'selected' : '';
                $employeeOptions .= '<option value="'.$emp->id.'" '.$selected.'>'.e($emp->name).'</option>';
            }

            $html .= '
            <div class="row align-items-center flex-wrap mb-2" data-task-id="'.$task->id.'">
              <div class="col-auto">
                <input type="checkbox" class="toggle-task-status" data-task-id="'.$task->id.'" '.$checked.' '.$disabled.'>
              </div>
              <div class="col task-text '.$taskBgClass.'" contenteditable="true" style="min-width: 150px; border: 1px solid transparent; padding: 5px; cursor: text;">
                '.e($task->task).'
              </div>
              <div class="col-2">
                <select class="form-control select2 employee-select" style="width: 100%;" data-task-id="'.$task->id.'">
                  '.$employeeOptions.'
                </select>
              </div>
              <div class="col-2" style="min-width: 140px;">
                <input type="date" class="form-control due-date-input" data-task-id="'.$task->id.'" value="'.$dueDateVal.'">
              </div>
              <div class="col-auto text-right">
                <button class="btn btn-sm btn-danger delete-task-btn" title="Delete Task" style="padding: 4px 8px;">
                  <i class="fas fa-trash-alt"></i>
                </button>
              </div>
            </div>';
        }

        $html .= '</div>';

        return response()->json([
            'html' => $html,
            'project_name' => $project->title,
            'progress' => $project->completed_percentage,
        ]);
    }

    public function storeTask(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:client_projects,id',
            'task' => 'required|string',
            'employee_id' => 'required|exists:users,id',
            'due_date' => ['required', 'date', 'after_or_equal:today'],
        ]);

        ProjectTask::create([
            'client_project_id' => $request->project_id,
            'task' => $request->task,
            'employee_id' => $request->employee_id,
            'due_date' => $request->due_date,
            'created_by' => auth()->id(),
        ]);

        return response()->json(['message' => 'Task added successfully']);
    }

    public function updateTask(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:project_tasks,id',
            'task' => 'sometimes|required|string',
            'employee_id' => 'sometimes|nullable|exists:users,id',
            'due_date' => ['sometimes', 'nullable', 'date', 'after_or_equal:today'],
        ]);

        $task = ProjectTask::findOrFail($request->task_id);

        if ($request->has('task')) $task->task = $request->task;
        if ($request->has('employee_id')) $task->employee_id = $request->employee_id;
        if ($request->has('due_date')) $task->due_date = $request->due_date;
        $task->updated_by = auth()->id();

        $task->save();

        return response()->json(['message' => 'Task updated successfully']);
    }

    public function deleteTask(ProjectTask $task)
    {
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }

    public function toggleStatus(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:project_tasks,id',
        ]);

        $task = ProjectTask::findOrFail($request->task_id);

        if ($task->employee_id != auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->status = $task->status == 1 ? 0 : 1;
        $task->updated_by = auth()->id();
        $task->save();

        return response()->json(['message' => 'Task status updated successfully']);
    }

}
