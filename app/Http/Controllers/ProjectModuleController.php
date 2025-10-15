<?php

namespace App\Http\Controllers;

use App\Models\ProjectModule;
use App\Models\ClientProject;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Validator;
use Carbon\Carbon;

class ProjectModuleController extends Controller
{
    public function index(ClientProject $project, Request $request)
    {
        if ($request->ajax()) {
            $data = ProjectModule::where('client_project_id', $project->id)->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('start_date', fn($row) => $row->start_date ? Carbon::parse($row->start_date)->format('d-m-Y') : '')
                ->addColumn('end_date', fn($row) => $row->estimated_end_date ? Carbon::parse($row->estimated_end_date)->format('d-m-Y') : '')
                ->addColumn('status', function($row){
                    $statuses = [1=>'To Do',2=>'In Progress',3=>'Done'];
                    $current = $statuses[$row->status] ?? 'Unknown';
                    $html = '<div class="dropdown">
                        <button class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown">'.$current.'</button>
                        <div class="dropdown-menu">';
                    foreach($statuses as $val=>$label){
                        $html .= '<a class="dropdown-item status-change" href="#" data-id="'.$row->id.'" data-status="'.$val.'">'.$label.'</a>';
                    }
                    return $html.'</div></div>';
                })
                ->addColumn('action', function($row){
                    return '<button class="btn btn-sm btn-primary edit" data-id="'.$row->id.'">Edit</button>
                            <button class="btn btn-sm btn-danger delete" data-id="'.$row->id.'">Delete</button>';
                })
                ->rawColumns(['status','action'])
                ->make(true);
        }

        return view('admin.client-projects.modules', compact('project'));
    }

    public function store(ClientProject $project, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'=>'required|string',
            'description'=>'required|string',
            'status'=>'required|in:1,2,3',
            'start_date'=>'required|date',
            'estimated_end_date'=>'required|date|after_or_equal:start_date',
        ]);

        if($validator->fails()){
            return response()->json(['status'=>422,'errors'=>$validator->errors()],422);
        }

        $module = ProjectModule::create([
            'client_project_id'=>$project->id,
            'title'=>$request->title,
            'description'=>$request->description,
            'status'=>$request->status,
            'start_date'=>$request->start_date,
            'estimated_end_date'=>$request->estimated_end_date,
            'created_by'=>auth()->id()
        ]);

        return response()->json(['status'=>200,'message'=>'Module created successfully.','data'=>$module]);
    }

    public function edit(ProjectModule $module)
    {
        return response()->json($module);
    }

    public function update(Request $request, ProjectModule $module)
    {
        $validator = Validator::make($request->all(), [
            'title'=>'required|string',
            'description'=>'required|string',
            'status'=>'required|in:1,2,3',
            'start_date'=>'required|date',
            'estimated_end_date'=>'required|date|after_or_equal:start_date',
        ]);

        if($validator->fails()){
            return response()->json(['status'=>422,'errors'=>$validator->errors()],422);
        }

        $module->update([
            'title'=>$request->title,
            'description'=>$request->description,
            'status'=>$request->status,
            'start_date'=>$request->start_date,
            'estimated_end_date'=>$request->estimated_end_date,
            'updated_by'=>auth()->id()
        ]);

        return response()->json(['status'=>200,'message'=>'Module updated successfully.','data'=>$module]);
    }

    public function destroy(ProjectModule $module)
    {
        $module->delete();
        return response()->json(['success'=>true,'message'=>'Module deleted successfully.']);
    }

    public function toggleStatus(Request $request, ProjectModule $module)
    {
        $module->update(['status'=>$request->status]);
        return response()->json(['status'=>200,'message'=>'Status updated successfully']);
    }
}
