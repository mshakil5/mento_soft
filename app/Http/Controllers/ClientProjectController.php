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

class ClientProjectController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ClientProject::with('client')->withCount('recentUpdates')->latest();

            if ($request->client_id) {
                $data->where('client_id', $request->client_id);
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
                ->addColumn('client_name', function($row) {
                    return $row->client->business_name ?? 'N/A';
                })
                ->addColumn('status', function($row) {
                    $statuses = [
                        1 => 'Pending',
                        2 => 'In Progress',
                        3 => 'Completed',
                        4 => 'On Hold'
                    ];
                    
                    $currentStatus = $statuses[$row->status] ?? 'Unknown';
                    
                    return '
                    <div class="dropdown">
                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="statusDropdown'.$row->id.'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            '.$currentStatus.'
                        </button>
                        <div class="dropdown-menu" aria-labelledby="statusDropdown'.$row->id.'">
                            <a class="dropdown-item status-change" href="#" data-id="'.$row->id.'" data-status="1">Pending</a>
                            <a class="dropdown-item status-change" href="#" data-id="'.$row->id.'" data-status="2">In Progress</a>
                            <a class="dropdown-item status-change" href="#" data-id="'.$row->id.'" data-status="3">Completed</a>
                            <a class="dropdown-item status-change" href="#" data-id="'.$row->id.'" data-status="4">On Hold</a>
                        </div>
                    </div>
                    ';
                })
                ->setRowClass(function($row) {
                    $today = \Carbon\Carbon::today();

                    $domainExpiry = $row->domain_expiry_date ? \Carbon\Carbon::parse($row->domain_expiry_date) : null;
                    $hostingExpiry = $row->hosting_expiry_date ? \Carbon\Carbon::parse($row->hosting_expiry_date) : null;

                    $warning = false;
                    $danger = false;

                    if ($domainExpiry) {
                        $daysDomain = $today->diffInDays($domainExpiry, false); // false = allow negative
                        if ($daysDomain < 0) {
                            $danger = true; // expired
                        } elseif ($daysDomain <= 7) {
                            $warning = true; // expiring this week
                        }
                    }

                    if ($hostingExpiry) {
                        $daysHosting = $today->diffInDays($hostingExpiry, false);
                        if ($daysHosting < 0) {
                            $danger = true;
                        } elseif ($daysHosting <= 7) {
                            $warning = true;
                        }
                    }

                    if ($danger) {
                        return 'table-danger'; // Bootstrap red bg
                    } elseif ($warning) {
                        return 'table-warning'; // Bootstrap yellow bg
                    }

                    return '';
                })
                ->addColumn('action', function($row) {
                    $indicators = [];
                    $today = \Carbon\Carbon::today();

                    if ($row->domain_expiry_date) {
                        $days = $today->diffInDays($row->domain_expiry_date, false);
                        if ($days < 0) {
                            $indicators[] = 'Domain expired';
                        } elseif ($days <= 7) {
                            $indicators[] = 'Domain expiring soon';
                        }
                    }

                    if ($row->hosting_expiry_date) {
                        $days = $today->diffInDays($row->hosting_expiry_date, false);
                        if ($days < 0) {
                            $indicators[] = 'Hosting expired';
                        } elseif ($days <= 7) {
                            $indicators[] = 'Hosting expiring soon';
                        }
                    }

                    $percent = $row->completed_percentage;
                    $badgeClass = 'bg-warning';
                    if ($percent >= 100) {
                        $badgeClass = 'bg-success';
                    } elseif ($percent < 20) {
                        $badgeClass = 'bg-danger';
                    }

                    $indicatorsHtml = count($indicators) ? '<div style="font-size: 0.8rem;">' . implode('<br>', $indicators) . '</div>' : '';

                    return '
                      <a href="'.route('client-projects.tasks', $row->id).'" class="btn btn-sm '.$badgeClass.'">
                        Tasks
                        <span class="badge '.$badgeClass.'" style="font-size: 0.75rem;">'.$percent.'%</span>
                      </a>
                      <a href="'.route('client-projects.updates', $row->id).'" class="btn btn-sm btn-primary">
                        Updates'.($row->recent_updates_count > 0 ? ' <span class="badge badge-light ml-1">'.$row->recent_updates_count.'</span>' : '').'
                      </a>
                      <button class="btn btn-sm btn-info edit" data-id="'.$row->id.'">Edit</button>
                      <button class="btn btn-sm btn-danger delete" data-id="'.$row->id.'">Delete</button>
                      '.$indicatorsHtml;
                })

                ->rawColumns(['image', 'status', 'action'])
                ->make(true);
        }

        $clients = Client::latest()->get();
        $employees = User::where('status', 1)->get();
        return view('admin.client-projects.index', compact('clients', 'employees'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:255',
            'domain' => 'nullable|string|max:255',
            'project_url' => 'nullable|url|max:255',
            'tech_stack' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'domain_expiry_date' => 'nullable|date|after_or_equal:start_date',
            'hosting_expiry_date' => 'nullable|date|after_or_equal:start_date',
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
        $data->tech_stack = $request->tech_stack;
        $data->description = $request->description;
        $data->additional_info = $request->additional_info;
        $data->start_date = $request->start_date;
        $data->end_date = $request->end_date;
        $data->domain_expiry_date = $request->domain_expiry_date;
        $data->hosting_expiry_date = $request->hosting_expiry_date;
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
                'message' => 'Client Project created successfully.'
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
            'domain' => 'nullable|string|max:255',
            'project_url' => 'nullable|url|max:255',
            'tech_stack' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'domain_expiry_date' => 'nullable|date|after_or_equal:start_date',
            'hosting_expiry_date' => 'nullable|date|after_or_equal:start_date',
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
        $project->domain = $request->domain;
        $project->project_url = $request->project_url;
        $project->tech_stack = $request->tech_stack;
        $project->description = $request->description;
        $project->additional_info = $request->additional_info;
        $project->start_date = $request->start_date;
        $project->end_date = $request->end_date;
        $project->domain_expiry_date = $request->domain_expiry_date;
        $project->hosting_expiry_date = $request->hosting_expiry_date;
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
                'message' => 'Client Project updated successfully.'
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

        $project->deleted_by = auth()->id();
        $project->save();

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