<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\ClientProject;
use App\Models\ProjectTask;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function userProfile()
    {
        $user = auth()->user()->load('client');
        return view('user.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'business_name'   => 'required|string|max:255',
            'primary_contact' => 'required|string|max:255',
            'phone1'          => 'required|string|max:20',
            'address'         => 'nullable|string|max:500',
            'password'        => 'nullable|string|min:6|confirmed',
        ]);

        $user = auth()->user();

        $user->name = $request->business_name;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        $user->client->update([
            'business_name'   => $request->business_name,
            'primary_contact' => $request->primary_contact,
            'phone1'          => $request->phone1,
            'address'         => $request->address,
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function userPassword()
    {
        return view('user.password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password changed successfully.');
    }

    public function projects()
    {
        $user = auth()->user();

        $projects = ClientProject::where('client_id', $user->client->id)
            ->latest()
            ->paginate(10);

        return view('user.projects', compact('projects'));
    }

    public function tasks()
    {
        $user = auth()->user();

        $tasks = ProjectTask::with('employee', 'clientProject')
            ->where('client_id', $user->client->id)
            ->latest()
            ->paginate(10);
        $clientProjects = ClientProject::where('client_id', $user->client->id)->select('id', 'title')->get();
        return view('user.tasks', compact('tasks', 'clientProjects'));
    }

    public function storeTask(Request $request)
    {
        $request->validate([
            'project_id'  => 'required|exists:client_projects,id',
            'task'        => 'required|string',
            'priority'    => 'required|in:high,medium,low',
            'due_date'    => 'nullable|date',
            'status'      => 'required|in:1,2,3',
        ]);

        $project = ClientProject::findOrFail($request->project_id);

        ProjectTask::create([
            'client_project_id' => $project->id,
            'client_id'         => $project->client_id,
            'task'              => $request->task,
            'priority'          => $request->priority,
            'due_date'          => $request->due_date,
            'status'            => $request->status,
            'created_by'        => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Task created successfully!');
    }

    public function messages(ProjectTask $task)
    {
        $userId = auth()->id();

        $messages = $task->messages()->with('sender:id,name')->orderBy('created_at','asc')->get();

        foreach ($messages as $message) {
            if (!$message->views()->where('user_id', $userId)->exists()) {
                $message->views()->create(['user_id' => $userId]);
            }
        }

        $html = view('user.task_messages', compact('messages'))->render();

        return response()->json(['html' => $html]);
    }

    public function storeMessage(Request $request, ProjectTask $task)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $message = $task->messages()->create([
            'user_id' => Auth::id(),
            'message' => $request->message
        ]);

        $messages = $task->messages()->with('sender:id,name')->orderBy('created_at','asc')->get();
        $html = view('user.task_messages', compact('messages'))->render();

        return response()->json(['html' => $html]);
    }

    public function updateTask(Request $request, $id)
    {
        $request->validate([
            'priority' => 'required|in:high,medium,low',
            'due_date' => 'nullable|date',
            'status' => 'required|in:1,2,3',
            'description' => 'required|string',
        ]);

        $task = ProjectTask::findOrFail($id);

        $task->priority = $request->priority;
        $task->due_date = $request->due_date;
        $task->status = $request->status;
        $task->task = $request->description;
        $task->save();

        return redirect()->back()->with('success', 'Task updated successfully!');
    }

}
