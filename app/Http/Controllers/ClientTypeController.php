<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClientType;
use Yajra\DataTables\Facades\DataTables;
use Validator;

class ClientTypeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ClientType::latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row) {
                    $buttons = '
                      <button class="btn btn-sm btn-info edit" data-id="'.$row->id.'">Edit</button>
                      <button class="btn btn-sm btn-danger delete" data-id="'.$row->id.'">Delete</button>
                    ';

                    if ($row->clients->count()) {
                        $buttons .= ' <a href="'.route('clients.index', ['client_type_id' => $row->id]).'" class="btn btn-sm btn-success">
                                        Clients ('.$row->clients->count().')
                                    </a>';
                    }

                    return $buttons;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.client-types.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:client_types,name',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = new ClientType;
        $data->name = $request->name;
        $data->description = $request->description;
        $data->created_by = auth()->id();

        if ($data->save()) {
            return response()->json([
                'status' => 200,
                'message' => 'Client Type created successfully.'
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
        $clientType = ClientType::find($id);
        if (!$clientType) {
            return response()->json([
                'status' => 404,
                'message' => 'Client Type not found'
            ], 404);
        }
        return response()->json($clientType);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:client_types,name,'.$request->codeid,
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $clientType = ClientType::find($request->codeid);
        if (!$clientType) {
            return response()->json([
                'status' => 404,
                'message' => 'Client Type not found'
            ], 404);
        }

        $clientType->name = $request->name;
        $clientType->description = $request->description;
        $clientType->updated_by = auth()->id();

        if ($clientType->save()) {
            return response()->json([
                'status' => 200,
                'message' => 'Client Type updated successfully.'
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
        $clientType = ClientType::find($id);
        
        if (!$clientType) {
            return response()->json(['success' => false, 'message' => 'Client Type not found.'], 404);
        }

        if ($clientType->delete()) {
            return response()->json(['success' => true, 'message' => 'Client Type deleted successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to delete client type.'], 500);
    }
}