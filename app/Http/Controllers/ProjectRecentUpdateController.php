<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProjectRecentUpdate;
use App\Models\ClientProject;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;
use Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProjectRecentUpdateController extends Controller
{
    public function index(ClientProject $project, Request $request)
    {
        if ($request->ajax()) {
            $data = ProjectRecentUpdate::with(['user'])
                ->where('client_project_id', $project->id)
                ->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('date', function($row) {
                    return Carbon::parse($row->created_at)->format('d-m-Y');
                })
                ->addColumn('description', function($row) {
                    return Str::limit(strip_tags($row->description), 100);
                })
                ->addColumn('attachment', function($row) {
                    if ($row->attachment) {
                        return '<a href="'.asset('images/recent-updates/'.$row->attachment).'" download class="btn btn-sm btn-info">
                            <i class="fas fa-download"></i>
                        </a>';
                    }
                    return 'No attachment';
                })
                ->addColumn('action', function($row) {
                    return '
                      <button class="btn btn-sm btn-info edit" data-id="'.$row->id.'">Edit</button>
                      <button class="btn btn-sm btn-danger delete" data-id="'.$row->id.'">Delete</button>
                    ';
                })
                ->rawColumns(['attachment', 'action'])
                ->make(true);
        }

        $users = User::where('status', 1)->get();
        return view('admin.client-projects.updates', compact('project', 'users'));
    }

    public function store(ClientProject $project, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'attachment' => 'nullable|file|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = [
            'client_project_id' => $project->id,
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'created_by' => auth()->id(),
        ];

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/recent-updates'), $fileName);
            $data['attachment'] = $fileName;
        }

        $update = ProjectRecentUpdate::create($data);

        return response()->json([
            'status' => 200,
            'message' => 'Update created successfully.',
            'data' => $update
        ], 201);
    }

    public function edit(ProjectRecentUpdate $update)
    {
        if (!$update) {
            return response()->json([
                'status' => 404,
                'message' => 'Update not found'
            ], 404);
        }
        return response()->json($update);
    }

    public function update(Request $request, ProjectRecentUpdate $update)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'attachment' => 'nullable|file|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $update->title = $request->title;
        $update->description = $request->description;
        $update->updated_by = auth()->id();

        if ($request->hasFile('attachment')) {
            // Delete old attachment if exists
            if ($update->attachment && file_exists(public_path('images/recent-updates/'.$update->attachment))) {
                unlink(public_path('images/recent-updates/'.$update->attachment));
            }
            
            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/recent-updates'), $fileName);
            $update->attachment = $fileName;
        }

        if ($update->save()) {
            return response()->json([
                'status' => 200,
                'message' => 'Update saved successfully.'
            ], 200);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Server error.'
            ], 500);
        }
    }

    public function destroy(ProjectRecentUpdate $update)
    {
        if (!$update) {
            return response()->json(['success' => false, 'message' => 'Update not found.'], 404);
        }

        // Delete attachment if exists
        if ($update->attachment && file_exists(public_path('images/recent-updates/'.$update->attachment))) {
            unlink(public_path('images/recent-updates/'.$update->attachment));
        }

        $update->deleted_by = auth()->id();
        $update->save();

        if ($update->delete()) {
            return response()->json(['success' => true, 'message' => 'Update deleted successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to delete update.'], 500);
    }
}