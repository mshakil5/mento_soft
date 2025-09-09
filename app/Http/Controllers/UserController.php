<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\ClientProject;
use App\Models\ProjectTask;
use Illuminate\Support\Facades\Auth;
use App\Models\ProjectServiceDetail;

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

        $projects = ClientProject::with([
            'tasks' => function($q) {
                $q->where('allow_client', 1)
                  ->latest()
                  ->with('employee');
            },
            'recentUpdates' => function($q) {
                $q->latest();
            },
            'services' => function($q) use ($user) {
                $q->where('bill_paid', 1)
                  ->where('client_id', $user->client->id);
            }
        ])
        ->where('client_id', $user->client->id)
        ->latest()
        ->paginate(10);
        // ->get();
        // dd($projects);

        return view('user.projects', compact('projects'));
    }

    public function tasks(Request $request)
    {
        $user = auth()->user();
        $userId = $user->id;

        $projects = ClientProject::where('client_id', $user->client->id)
            ->whereHas('tasks')
            ->get();

        $tab = $request->tab ?? 'all';

        $query = ProjectTask::with(['employee', 'clientProject'])
            ->withCount(['messages as unread_messages_count' => function ($q) use ($userId) {
                $q->where('user_id', '!=', $userId)
                  ->whereDoesntHave('views', fn($q2) => $q2->where('user_id', $userId));
            }])
            ->where('client_id', $user->client->id)
            ->where('allow_client', 1)
            ->when($request->project, fn($q, $projectId) => $q->where('client_project_id', $projectId));

        switch($tab) {
            case 'tobeconfirmed':
                $query->where('status', 3)->where('is_confirmed', 0);
                break;
            case 'inprogress':
                $query->where('status', 2);
                break;
            case 'todo':
                $query->where('status', 1);
                break;
            case 'confirmed':
                $query->where('status', 3)->where('is_confirmed', 1);
                break;
            default:
                break;
        }

        $tasks = $query->latest()->paginate(10)->withQueryString();

        return view('user.tasks', compact('tasks', 'projects', 'tab'));
    }

    public function storeTask(Request $request)
    {
        $request->validate([
            'project_id'  => 'required|exists:client_projects,id',
            'title'        => 'required|string',
            'task'        => 'required|string'
        ]);

        $project = ClientProject::findOrFail($request->project_id);

        ProjectTask::create([
            'client_project_id' => $project->id,
            'client_id'         => $project->client_id,
            'employee_id'         => $project->employee_id,
            'title'              => $request->title,
            'task'              => $request->task,
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
            'description' => 'required|string',
            'title'       => 'required|string'
        ]);

        $task = ProjectTask::findOrFail($id);
        $task->title = $request->title;
        $task->task = $request->description;
        $task->save();

        return redirect()->back()->with('success', 'Task updated successfully!');
    }

    public function confirmTask(ProjectTask $task)
    {
        $task->is_confirmed = $task->is_confirmed ? 0 : 1;
        $task->save();
          
        return back()->with('success', 'Task status updated.');
    }

    public function services(Request $request)
    {
        $user = auth()->user();
        $clientId = $user->client->id;

        $projects = ClientProject::where('client_id', $clientId)->get();

        // Type 1: latest
        $latestType1Ids = ProjectServiceDetail::where('type', 1)
            ->where('client_id', $clientId)
            ->when($request->bill_paid !== null, fn($q) => $q->where('bill_paid', $request->bill_paid))
            ->selectRaw('MAX(id) as id')
            ->groupBy('project_service_id','client_id','client_project_id','amount','cycle_type','is_auto')
            ->pluck('id')->toArray();

        // Type 2: unpaid first
        $type2UnpaidIds = ProjectServiceDetail::where('type', 2)
            ->where('client_id', $clientId)
            ->when($request->bill_paid !== null, fn($q) => $q->where('bill_paid', $request->bill_paid))
            // ->where('bill_paid', 0)
            ->selectRaw('MAX(id) as id')
            ->groupBy('project_service_id','client_id','client_project_id','amount','cycle_type','is_auto')
            ->pluck('id')->toArray();

        if (!empty($type2UnpaidIds)) {
            $type2Ids = $type2UnpaidIds;
        } elseif (ProjectServiceDetail::where('type', 2)->where('client_id', $clientId)->where('is_renewed', 1)->exists()) {
            $type2Ids = ProjectServiceDetail::where('type', 2)
                ->where('client_id', $clientId)
                ->where('is_renewed', 1)
                ->selectRaw('MAX(id) as id')
                ->groupBy('project_service_id','client_id','client_project_id','amount','cycle_type','is_auto')
                ->pluck('id')->toArray();
        } else {
            $type2Ids = ProjectServiceDetail::where('type', 2)
                ->where('client_id', $clientId)
                ->where('bill_paid', 1)
                ->where('is_renewed', 0)
                ->selectRaw('MAX(id) as id')
                ->groupBy('project_service_id','client_id','client_project_id','amount','cycle_type','is_auto')
                ->pluck('id')->toArray();
        }

        $idsToDisplay = array_merge($latestType1Ids, $type2Ids);

        $services = ProjectServiceDetail::with(['serviceType','project','transaction'])
            ->where('client_id', $clientId)
            ->whereIn('id', $idsToDisplay)
            ->when($request->project, fn($q,$p) => $q->where('client_project_id', $p))
            ->latest()
            ->get();

        return view('user.services', compact('services','projects'));
    }
}
