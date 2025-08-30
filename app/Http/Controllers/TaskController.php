<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProjectTask;
use App\Models\ClientProject;
use Illuminate\Foundation\Auth\User;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $userId = auth()->id();

            $query = ProjectTask::with([
                'clientProject:id,title',
                'employee:id,name',
                'creator:id,name',
            ])->withCount(['messages as unread_messages_count' => function ($q) use ($userId) {
                $q->where('user_id', '!=', $userId)
                  ->whereDoesntHave('views', fn($q) => $q->where('user_id', $userId));
            }]);

            if ($request->status) {
                $query->where('status', $request->status);
            }

            if ($request->search) {
                $query->where('task', 'like', "%{$request->search}%");
            }

            if ($request->client_project_id) {
                $query->where('client_project_id', $request->client_project_id);
            }

            $query->orderBy('created_at', 'desc');

            return DataTables::of($query)
                ->addColumn('task', function ($row) use ($userId) {
                    $taskText = Str::limit($row->task, 100);

                    $priorityClass = match($row->priority) {
                        'high' => 'badge badge-danger',
                        'medium' => 'badge badge-warning',
                        'low' => 'badge badge-success',
                        default => '',
                    };

                    $projectTitle = $row->clientProject->title ?? 'N/A';
                    $unreadCount = $row->unread_messages_count;

                    $html = '<div data-toggle="modal" data-target="#taskModal-'.$row->id.'" style="cursor:pointer;">';
                    $html .= '<div class="d-flex flex-column">';

                    $html .= '<div class="d-flex justify-content-between align-items-start mb-2">';
                    $html .= '  <span class="flex-grow-1">' . $taskText . '</span>';
                    $html .= '  <div class="d-flex align-items-center">';

                    if ($unreadCount > 0) {
                        $html .= '<span class="badge badge-warning mr-2">' . $unreadCount . '</span>';
                    }

                    if (is_null($row->employee_id)) {
                        $html .= '<i class="fas fa-user-slash text-danger mr-2" title="Unassigned"></i>';
                    }

                    $html .= '    <span class="' . $priorityClass . '">' . ucfirst($row->priority) . '</span>';
                    $html .= '  </div>';
                    $html .= '</div>';

                    $html .= '<div class="align-self-end text-muted small">' . $projectTitle . '</div>';
                    $html .= '</div>';
                    $html .= '</div>';

                    $html .= view('admin.client-projects.partials.task_list-modal', ['row' => $row])->render();

                    return $html;
                })
                ->rawColumns(['task'])
                ->make(true);
        }

        $clientProjects = ClientProject::select('id', 'title')->latest()->get();
        return view('admin.client-projects.task_index', compact('clientProjects'));
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
        
        $messagesHtml = view('admin.client-projects.partials.task_messages', compact('messages'))->render();

        $task->load(['activities.causer']);
        
        $timelineHtml = view('admin.client-projects.partials.task_timeline', compact('task'))->render();

        return response()->json([
            'messagesHtml' => $messagesHtml,
            'timelineHtml' => $timelineHtml,
        ]);
    }

    public function store(Request $request, ProjectTask $task)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $message = $task->messages()->create([
            'user_id' => Auth::id(),
            'message' => $request->message
        ]);

        $messages = $task->messages()->with('sender:id,name')->orderBy('created_at','asc')->get();
        $html = view('admin.client-projects.partials.task_messages', compact('messages'))->render();

        return response()->json(['html' => $html]);
    }
}
