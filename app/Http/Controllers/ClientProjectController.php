<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClientProject;
use App\Models\Client;
use Intervention\Image\Facades\Image;
use Yajra\DataTables\Facades\DataTables;
use Validator;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Invoice;
use App\Models\InvoiceDetail;

class ClientProjectController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ClientProject::with([
                  'client',
                  'recentUpdates' => function($query) {
                      $query->latest();
                  },
                  'tasks' => function($query) {
                      $query->latest()->take(5)
                      ->with(['employee', 'creator'])
                      ->where(function($q) {
                      $q->where('employee_id', auth()->id())
                          ->orWhere('allow_employee', 1); 
                      });
                  },
                  'services.serviceType'
              ])
              ->withCount(['recentUpdates', 'tasks'])
              ->latest();

            if ($request->client_id) {
                $data->where('client_id', $request->client_id);
            }

            if ($request->client_filter_id) {
                $data->where('client_id', $request->client_filter_id);
            }

            if ($request->status) {
                $data->where('status', $request->status);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('image', function($row) {
                    if ($row->image) {
                        return '<a href="'.asset("images/client-projects/".$row->image).'" target="_blank">
                                    <img src="'.asset("images/client-projects/".$row->image).'" style="max-width:80px; height:auto;">
                                </a>';
                    }
                    return '';
                })
                ->addColumn('date', function($row) {
                    return Carbon::parse($row->created_at)->format('d-m-Y');
                })
                ->addColumn('start_date', function($row) {
                    return $row->start_date ? Carbon::parse($row->start_date)->format('d-m-Y') : '';
                })
                ->addColumn('due_date', function($row) {
                    return $row->due_date ? Carbon::parse($row->due_date)->format('d-m-Y') : '';
                })
                ->addColumn('amount', function($row) {
                    return '£' . number_format($row->amount, 0);
                })
                ->addColumn('received', function($row) {
                    $serviceTxnsSum = Transaction::where('transaction_type', 'Received')
                        ->whereHas('projectServiceDetail', function($q) use ($row) {
                            $q->where('client_project_id', $row->id);
                        })->sum('amount');

                    $invoiceSum = InvoiceDetail::whereHas('invoice', fn($q) => $q->where('status', 2))
                        ->where('client_project_id', $row->id)
                        ->sum('total_inc_vat');

                    $totalReceived = $serviceTxnsSum + $invoiceSum;

                    if ($totalReceived > 0) {
                        $badge = '<a href="' . route('transactions.index', ['project_id' => $row->id, 'status' => 'Received']) . '" class="badge bg-success text-white" style="text-decoration:none;">£' . number_format($totalReceived, 0) . '</a>';
                    } else {
                        $badge = '<span class="badge bg-secondary">£0</span>';
                    }

                    return $badge;
                })
                ->addColumn('client_name', function($row) {
                    return $row->client->business_name ?? '';
                })
                ->addColumn('status', function($row) {
                    $statuses = [
                        1 => 'Planned',
                        2 => 'In Progress',
                        3 => 'Blocked',
                        4 => 'Done',
                    ];
                    
                    $currentStatus = $statuses[$row->status] ?? 'Unknown';
                    
                    return '
                    <div class="dropdown">
                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="statusDropdown'.$row->id.'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            '.$currentStatus.'
                        </button>
                        <div class="dropdown-menu" aria-labelledby="statusDropdown'.$row->id.'">
                            <a class="dropdown-item status-change" href="#" data-id="'.$row->id.'" data-status="1">Planned</a>
                            <a class="dropdown-item status-change" href="#" data-id="'.$row->id.'" data-status="2">In Progress</a>
                            <a class="dropdown-item status-change" href="#" data-id="'.$row->id.'" data-status="3">Blocked</a>
                            <a class="dropdown-item status-change" href="#" data-id="'.$row->id.'" data-status="4">Done</a>
                        </div>
                    </div>
                    ';
                })
                ->addColumn('action', function($row) {
                    $percent = $row->completed_percentage;
                    $badgeClass = $percent >= 100 ? 'bg-success' : ($percent < 20 ? 'bg-danger' : 'bg-warning');
                    $details = view('admin.client-projects.partials.details-modal', ['row' => $row])->render();

                    $buttons = '<a class="btn btn-sm btn-success mr-1" href="' . route('client-projects.tasks', $row->id) . '">
                                    Tasks
                                    <span class="badge '.$badgeClass.'" style="font-size: 0.75rem;">'.$percent.'%</span>
                                </a>';

                    $buttons .= '<a class="btn btn-sm btn-info mr-1" data-toggle="modal" data-target="#detailsModal-'.$row->id.'">
                                    View
                                </a>';

                    $buttons .= '<div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown">
                                        Actions
                                    </button>
                                    <div class="dropdown-menu">';

                    if (auth()->user()->can('edit project')) {
                        $buttons .= '<a href="#" class="dropdown-item edit" data-id="'.$row->id.'">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>';
                    }

                    $buttons .= '<a href="#" class="dropdown-item delete d-none" data-id="'.$row->id.'">
                                    <i class="fas fa-trash"></i> Delete
                                </a>';

                    $buttons .= '</div></div>'.$details;

                    $groupedServices = $row->services->filter(fn($s) => $s->serviceType)->unique('project_service_id');

                    if ($groupedServices->count()) {
                        $buttons .= '<div class="btn-group ml-1">
                                        <button type="button" class="btn btn-sm btn-dark dropdown-toggle" data-toggle="dropdown">
                                            Services
                                        </button>
                                        <div class="dropdown-menu">';

                        foreach ($groupedServices as $service) {
                            $buttons .= '<a href="'.route('project-services.index', [
                                                    'client_id' => $row->client_id,
                                                    'project_service_id' => $service->serviceType->id
                                                ]).'" class="dropdown-item">'
                                        . $service->serviceType->name .
                                        '</a>';
                        }

                        $buttons .= '</div></div>';
                    }

                    return $buttons;
                })
                ->rawColumns(['image', 'status', 'action', 'received'])
                ->make(true);
        }

        $clients = Client::where('status', 1)->select('id', 'business_name')->latest()->get();
        $employees = User::where('status', 1)->where('user_type', 1)->select('id', 'name')->get();
        return view('admin.client-projects.index', compact('clients', 'employees'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:255',
            'project_manager' => 'required',
            'domain' => 'nullable|string|max:255',
            'project_url' => 'nullable|url|max:255',
            'tech_stack' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:1,2,3,4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = new ClientProject;
        $data->client_id = $request->client_id;
        $data->title = $request->title;
        $data->domain = $request->domain;
        $data->project_url = $request->project_url;
        $data->employee_id = $request->project_manager;
        $data->tech_stack = $request->tech_stack;
        $data->description = $request->description;
        $data->additional_info = $request->additional_info;
        $data->start_date = $request->start_date;
        $data->due_date = $request->due_date;
        $data->amount = $request->amount ?? 0;
        $data->status = $request->status;
        $data->created_by = auth()->id();

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.webp';
            $path = public_path('images/client-projects/');

            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            Image::make($image)
                ->resize(800, 600, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 85)
                ->save($path . $imageName);

            $data->image = $imageName;
        }

        if ($data->save()) {
            return response()->json([
                'status' => 200,
                'message' => 'Project created successfully.'
            ], 201);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Server error.'
            ], 500);
        }
    }

    public function edit($id)
    {
        $project = ClientProject::find($id);
        if (!$project) {
            return response()->json([
                'status' => 404,
                'message' => 'Client Project not found'
            ], 404);
        }
        return response()->json($project);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:255',
            'project_manager' => 'required',
            'domain' => 'nullable|string|max:255',
            'project_url' => 'nullable|url|max:255',
            'tech_stack' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:1,2,3,4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $project = ClientProject::find($request->codeid);
        if (!$project) {
            return response()->json([
                'status' => 404,
                'message' => 'Client Project not found'
            ], 404);
        }

        $project->client_id = $request->client_id;
        $project->title = $request->title;
        $project->employee_id = $request->project_manager;
        $project->domain = $request->domain;
        $project->project_url = $request->project_url;
        $project->tech_stack = $request->tech_stack;
        $project->description = $request->description;
        $project->additional_info = $request->additional_info;
        $project->start_date = $request->start_date;
        $project->due_date = $request->due_date;
        $project->amount = $request->amount ?? 0;
        $project->status = $request->status;
        $project->updated_by = auth()->id();

        // Handle image update
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($project->image && file_exists(public_path('images/client-projects/' . $project->image))) {
                unlink(public_path('images/client-projects/' . $project->image));
            }

            $image = $request->file('image');
            $imageName = time() . '.webp';
            $path = public_path('images/client-projects/');

            Image::make($image)
                ->resize(800, 600, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 85)
                ->save($path . $imageName);

            $project->image = $imageName;
        }

        if ($project->save()) {
            return response()->json([
                'status' => 200,
                'message' => 'Project updated successfully.'
            ], 200);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Server error.'
            ], 500);
        }
    }

    public function destroy($id)
    {
        $project = ClientProject::find($id);
        
        if (!$project) {
            return response()->json(['success' => false, 'message' => 'Client Project not found.'], 404);
        }

        // Delete image if exists
        if ($project->image && file_exists(public_path('images/client-projects/' . $project->image))) {
            unlink(public_path('images/client-projects/' . $project->image));
        }

        if ($project->delete()) {
            return response()->json(['success' => true, 'message' => 'Client Project deleted successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to delete client project.'], 500);
    }

    public function toggleStatus(Request $request)
    {
        $project = ClientProject::find($request->project_id);
        if (!$project) {
            return response()->json(['status' => 404, 'message' => 'Client Project not found']);
        }

        $project->status = $request->status;
        $project->save();

        return response()->json(['status' => 200, 'message' => 'Status updated successfully']);
    }
}