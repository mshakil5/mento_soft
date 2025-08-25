<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProjectTask;
use App\Models\ClientProject;
use Illuminate\Foundation\Auth\User;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ProjectTask::with(['clientProject:id,title', 'employee:id,name']);

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
            ->addColumn('task', function ($row) {
                $taskText = Str::limit($row->task, 100);

                $priorityClass = '';
                switch ($row->priority) {
                    case 'high': $priorityClass = 'badge badge-danger'; break;
                    case 'medium': $priorityClass = 'badge badge-warning'; break;
                    case 'low': $priorityClass = 'badge badge-success'; break;
                }

                $projectTitle = $row->clientProject->title ?? 'N/A';

                $html = '<div data-toggle="modal" data-target="#taskModal-'.$row->id.'" style="cursor:pointer;">';
                $html .= '<div class="d-flex flex-column">';
                $html .= '  <div class="d-flex justify-content-between align-items-start mb-2">';
                $html .= '      <span class="flex-grow-1">' . $taskText . '</span>';
                $html .= '      <span class="' . $priorityClass . '">' . ucfirst($row->priority) . '</span>';
                $html .= '  </div>';
                $html .= '  <div class="align-self-end text-muted small">' . $projectTitle . '</div>';
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
}
