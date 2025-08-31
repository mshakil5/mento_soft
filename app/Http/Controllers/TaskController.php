<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProjectTask;
use App\Models\ClientProject;
use Illuminate\Foundation\Auth\User;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
                    $taskText = $row->title;

                    $priorityClass = match($row->priority) {
                        'high' => 'badge badge-danger',
                        'medium' => 'badge badge-warning',
                        'low' => 'badge badge-success',
                        default => '',
                    };

                    $projectTitle = $row->clientProject->title ?? '';
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
                    } else {
                        $html .= '<span class="badge bg-info mr-2" title="Assigned Employee">' . ($row->employee->name ?? '') . '</span>';
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

    public function allTasks(Request $request)
    {
        if ($request->ajax()) {

            $data = ProjectTask::with(['employee', 'clientProject'])->latest();

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
                ->addColumn('project_title', function($row) {
                    return $row->clientProject->title ?? '';
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
                      <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#taskModal'.$row->id.'">View</button>
                      <a href="'.route('client-projects-task.edit-page', $row->id).'" class="btn btn-sm btn-info">Edit</a>
                      <button class="btn btn-sm btn-danger delete" data-id="'.$row->id.'">Delete</button>

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

        $employees = User::where('status', 1)->latest()->get();
        return view('admin.client-projects.all_tasks', compact('employees'));
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
