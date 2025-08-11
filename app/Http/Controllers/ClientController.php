<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\ClientType;
use Intervention\Image\Facades\Image;
use Yajra\DataTables\Facades\DataTables;
use Validator;
use Illuminate\Support\Carbon;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Client::with(['clientType', 'projects', 'invoices'])->latest();

            if ($request->client_type_id) {
                $data->where('client_type_id', $request->client_type_id);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('image', function($row) {
                    if ($row->image) {
                        return '<a href="'.asset("images/clients/".$row->image).'" target="_blank">
                                    <img src="'.asset("images/clients/".$row->image).'" style="max-width:80px; height:auto;">
                                </a>';
                    }
                    return '';
                })
                ->addColumn('date', function($row) {
                    return Carbon::parse($row->created_at)->format('d-m-Y');
                })
                // ->addColumn('client_type', function($row) {
                //     return $row->clientType->name ?? 'N/A';
                // })
                ->addColumn('status', function($row) {
                    $checked = $row->status == 1 ? 'checked' : '';
                    return '<div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input toggle-status" id="customSwitchStatus'.$row->id.'" data-id="'.$row->id.'" '.$checked.'>
                                <label class="custom-control-label" for="customSwitchStatus'.$row->id.'"></label>
                            </div>';
                })
                ->addColumn('action', function($row) {
                    $buttons = '
                      <button class="btn btn-sm btn-info edit" data-id="'.$row->id.'">Edit</button>
                      <button class="btn btn-sm btn-danger delete" data-id="'.$row->id.'">Delete</button>
                    ';

                    if ($row->projects->count()) {
                        $buttons .= '<a href="'.route('client-projects.index', ['client_id' => $row->id]).'" class="btn btn-sm btn-success">
                                        Projects ('.$row->projects->count().')
                                    </a>';
                    }
                    if ($row->invoices->count()) {
                        $buttons .= '<a href="'.route('invoices.index', ['client_id' => $row->id]).'" class="btn btn-sm btn-primary ml-1">
                                        Invoices ('.$row->invoices->count().')
                                    </a>';
                    }

                    return $buttons;
                })
                ->rawColumns(['image', 'status', 'action'])
                ->make(true);
        }

        $clientTypes = ClientType::latest()->get();
        return view('admin.clients.index', compact('clientTypes'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone1' => 'required|string|max:20',
            'phone2' => 'nullable|string|max:20',
            // 'client_type_id' => 'nullable|exists:client_types,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = new Client;
        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone1 = $request->phone1;
        $data->phone2 = $request->phone2;
        $data->on_going = $request->on_going;
        $data->one_of = $request->one_of;
        $data->address = $request->address;
        $data->business_name = $request->business_name;
        $data->client_type_id = $request->client_type_id;
        $data->additional_fields = $request->additional_fields;
        $data->created_by = auth()->id();

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.webp';
            $path = public_path('images/clients/');

            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

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
                'message' => 'Client created successfully.',
                'client' => $data
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
        $client = Client::find($id);
        if (!$client) {
            return response()->json([
                'status' => 404,
                'message' => 'Client not found'
            ], 404);
        }
        return response()->json($client);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone1' => 'required|string|max:20',
            'phone2' => 'nullable|string|max:20',
            // 'client_type_id' => 'nullable|exists:client_types,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $client = Client::find($request->codeid);
        if (!$client) {
            return response()->json([
                'status' => 404,
                'message' => 'Client not found'
            ], 404);
        }

        $client->name = $request->name;
        $client->email = $request->email;
        $client->phone1 = $request->phone1;
        $client->phone2 = $request->phone2;
        $client->on_going = $request->on_going;
        $client->one_of = $request->one_of;
        $client->address = $request->address;
        $client->business_name = $request->business_name;
        $client->client_type_id = $request->client_type_id;
        $client->additional_fields = $request->additional_fields;
        $client->updated_by = auth()->id();

        // Handle image update
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($client->image && file_exists(public_path('images/clients/' . $client->image))) {
                unlink(public_path('images/clients/' . $client->image));
            }

            $image = $request->file('image');
            $imageName = time() . '.webp';
            $path = public_path('images/clients/');

            Image::make($image)
                ->resize(400, 400, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 85)
                ->save($path . $imageName);

            $client->image = $imageName;
        }

        if ($client->save()) {
            return response()->json([
                'status' => 200,
                'message' => 'Client updated successfully.'
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
        $client = Client::find($id);
        
        if (!$client) {
            return response()->json(['success' => false, 'message' => 'Client not found.'], 404);
        }

        // Delete image if exists
        if ($client->image && file_exists(public_path('images/clients/' . $client->image))) {
            unlink(public_path('images/clients/' . $client->image));
        }

        if ($client->delete()) {
            return response()->json(['success' => true, 'message' => 'Client deleted successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to delete client.'], 500);
    }

    public function toggleStatus(Request $request)
    {
        $client = Client::find($request->client_id);
        if (!$client) {
            return response()->json(['status' => 404, 'message' => 'Client not found']);
        }

        $client->status = $request->status;
        $client->save();

        return response()->json(['status' => 200, 'message' => 'Status updated successfully']);
    }
}