<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TeamMember;
use Intervention\Image\Facades\Image;
use Yajra\DataTables\Facades\DataTables;
use Validator;

class TeamMemberController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = TeamMember::latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('image', function($row) {
                    if ($row->image) {
                        return '<a href="'.asset("images/team-members/".$row->image).'" target="_blank">
                                    <img src="'.asset("images/team-members/".$row->image).'" style="max-width:80px; height:auto;">
                                </a>';
                    }
                    return '';
                })
                ->addColumn('sl', function ($row) {
                    return $row->sl ?? '';
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
                ->rawColumns(['image', 'status', 'action'])
                ->make(true);
        }

        return view('admin.team-members.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = new TeamMember;
        $data->title = $request->title;
        $data->name = $request->name;
        $data->description = $request->description;
        $data->sl = $request->sl ?? 0;
        $data->created_by = auth()->id();

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.webp';
            $path = public_path('images/team-members/');

            // Ensure directory exists
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            // Save image
            Image::make($image)
                ->resize(400, 400, function ($constraint) {
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
                'message' => 'Team member created successfully.'
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
        $member = TeamMember::find($id);
        if (!$member) {
            return response()->json([
                'status' => 404,
                'message' => 'Team member not found'
            ], 404);
        }
        return response()->json($member);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $member = TeamMember::find($request->codeid);
        if (!$member) {
            return response()->json([
                'status' => 404,
                'message' => 'Team member not found'
            ], 404);
        }

        $member->title = $request->title;
        $member->name = $request->name;
        $member->description = $request->description;
        $member->sl = $request->sl ?? 0;
        $member->updated_by = auth()->id();

        // Handle image update
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($member->image && file_exists(public_path('images/team-members/' . $member->image))) {
                unlink(public_path('images/team-members/' . $member->image));
            }

            $image = $request->file('image');
            $imageName = time() . '.webp';
            $path = public_path('images/team-members/');

            // Save image
            Image::make($image)
                ->resize(400, 400, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 85)
                ->save($path . $imageName);

            $member->image = $imageName;
        }

        if ($member->save()) {
            return response()->json([
                'status' => 200,
                'message' => 'Team member updated successfully.'
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
        $member = TeamMember::find($id);
        
        if (!$member) {
            return response()->json(['success' => false, 'message' => 'Team member not found.'], 404);
        }

        // Delete image if exists
        if ($member->image && file_exists(public_path('images/team-members/' . $member->image))) {
            unlink(public_path('images/team-members/' . $member->image));
        }

        if ($member->delete()) {
            return response()->json(['success' => true, 'message' => 'Team member deleted successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to delete team member.'], 500);
    }

    public function toggleStatus(Request $request)
    {
        $member = TeamMember::find($request->member_id);
        if (!$member) {
            return response()->json(['status' => 404, 'message' => 'Team member not found']);
        }

        $member->status = $request->status;
        $member->save();

        return response()->json(['status' => 200, 'message' => 'Status updated successfully']);
    }
}