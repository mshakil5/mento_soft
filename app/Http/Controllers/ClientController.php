<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\ClientType;
use Intervention\Image\Facades\Image;
use Yajra\DataTables\Facades\DataTables;
use Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\LoginRecord;
use Illuminate\Support\Facades\Mail;
use App\Mail\ClientEmail;
use App\Models\CompanyDetails;
use App\Models\ProjectServiceDetail;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Client::with(['clientType', 'projects', 'invoices', 'services.serviceType'])->withCount('projects')->latest();

            if ($request->client_type_id) {
                $data->where('client_type_id', $request->client_type_id);
            }

            if ($request->status) {
                $data->where('status', $request->status);
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
                ->addColumn('projects_count', function($row) {
                    if ($row->projects_count > 0) {
                        return '<a href="'.route('client-projects.index', ['client_id' => $row->id]).'" 
                                  class="badge badge-success" 
                                  title="View Projects">
                                  '.$row->projects_count.'
                                </a>';
                    }
                    return '<span class="badge badge-secondary">0</span>';
                })
                ->addColumn('outstanding_amount', function($row) {
                    $amount = $row->services->where('bill_paid', 0)->sum('amount');

                    if ($amount > 0) {
                        $url = route('project-services.index', [
                            'client_id' => $row->id,
                            'bill_paid' => 0
                        ]);
                        return '<a href="'.$url.'" class="badge badge-success" title="View Outstanding Amount">£' . number_format($amount, 2) . '</a>';
                    }

                    return '<span class="badge badge-secondary">£0.00</span>';
                })
                ->addColumn('status', function($row) {
                    $statuses = [
                        1 => 'Active',
                        2 => 'Pending',
                        3 => 'Paused',
                        4 => 'Prospect'
                    ];
                    
                    $currentStatus = $statuses[$row->status] ?? 'Unknown';
                    
                    return '
                    <div class="dropdown">
                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="statusDropdown'.$row->id.'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            '.$currentStatus.'
                        </button>
                        <div class="dropdown-menu" aria-labelledby="statusDropdown'.$row->id.'">
                            <a class="dropdown-item status-change" href="#" data-id="'.$row->id.'" data-status="1">Active</a>
                            <a class="dropdown-item status-change" href="#" data-id="'.$row->id.'" data-status="2">Pending</a>
                            <a class="dropdown-item status-change" href="#" data-id="'.$row->id.'" data-status="3">Paused</a>
                            <a class="dropdown-item status-change" href="#" data-id="'.$row->id.'" data-status="4">Prospect</a>
                        </div>
                    </div>
                    ';
                })
                ->addColumn('action', function($row) {
                    $details = view('admin.clients.partials.details-modal', ['row' => $row])->render();

                    $buttons = '';

                    if (auth()->user()->can('mail client')) {
                        $buttons .= '<a href="'.route('client.email', $row->id).'" class="btn btn-sm btn-warning">
                                        <i class="fas fa-envelope"></i>
                                    </a> ';
                    }

                    $buttons .= '<a class="btn btn-sm btn-info" data-toggle="modal" data-target="#detailsModal-'.$row->id.'">
                                    View Details
                                </a>';

                    $buttons .= '<div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-secondary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <div class="dropdown-menu p-2" style="min-width: 180px;">';

                    if (auth()->user()->can('edit client')) {
                        $buttons .= '<button class="btn btn-outline-primary btn-sm btn-block mb-1 edit" data-id="'.$row->id.'">Edit</button>';
                        $buttons .= '<button class="btn btn-outline-danger btn-sm btn-block mb-1 delete" data-id="'.$row->id.'">Delete</button>';
                    }

                    $buttons .= '</div></div>';

                    if ($row->projects->count()) {
                        $buttons .= '<a href="'.route('client-projects.index', ['client_id' => $row->id]).'" class="btn btn-success btn-sm btn-block mb-1 d-none">
                                        Projects ('.$row->projects->count().')
                                    </a>';
                    }

                    $buttons .= '</div></div>';

                    $buttons .= '<div class="btn-group ml-1">
                        <button type="button" class="btn btn-sm btn-dark dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        </button>
                        <div class="dropdown-menu p-2" style="min-width: 200px;">';

                    $groupedServices = $row->services->filter(fn($s) => $s->serviceType)->unique('project_service_id');

                    foreach ($groupedServices as $service) {
                        $buttons .= '<a href="'.route('project-services.index', [
                                                    'client_id' => $row->id,
                                                    'project_service_id' => $service->serviceType->id
                                                ]).'" class="btn btn-outline-primary btn-sm btn-block mb-1">'
                                    . $service->serviceType->name .
                                    '</a>';
                    }

                    $buttons .= '</div></div>';
                    $buttons .= $details;

                    return $buttons;
                })
                ->rawColumns(['image', 'status', 'action', 'projects_count', 'outstanding_amount'])
                ->make(true);
        }

        $clientTypes = ClientType::latest()->get();
        return view('admin.clients.index', compact('clientTypes'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_name' => 'required|string|max:255',
            'primary_contact' => 'required|string|max:255',
            'email' => 'required|email',
            'phone1' => 'required|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'password' => 'required|string|min:6',
        ], [
            'business_name.required' => 'Name is required',
            'phone1.required'        => 'Phone is required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $existingUser = User::where('email', $request->email)
                            ->where('user_type', 3)
                            ->first();
        if ($existingUser) {
            return response()->json([
                'status' => 422,
                'errors' => ['email' => ['This email is already registered as a client.']]
            ], 422);
        }

        $user = User::create([
            'name' => $request->business_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => 3,
            'status' => 1,
        ]);

        $client = new Client();
        $client->user_id = $user->id;
        $client->name = $request->business_name;
        $client->email = $request->email;
        $client->phone1 = $request->phone1;
        $client->primary_contact = $request->primary_contact;
        $client->address = $request->address;
        $client->business_name = $request->business_name;
        $client->additional_fields = $request->additional_fields;
        $client->created_by = auth()->id();

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.webp';
            $path = public_path('images/clients/');

            if (!file_exists($path)) mkdir($path, 0755, true);

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
                'message' => 'Client created successfully.',
                'client' => $client
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
            'primary_contact' => 'required|string|max:255',
            'email' => 'required|email',
            'phone1' => 'required|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'password' => 'nullable|string|min:6',
        ], [
            'business_name.required' => 'Name is required',
            'phone1.required'        => 'Phone is required',
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

        $user = $client->user;

        $existingUser = User::where('email', $request->email)
                            ->where('user_type', 3)
                            ->where('id', '!=', $user->id)
                            ->first();
        if ($existingUser) {
            return response()->json([
                'status' => 422,
                'errors' => ['email' => 'This email is already registered as a client.']
            ], 422);
        }

        $user->name = $request->business_name;
        $user->email = $request->email;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        $client->name = $request->business_name;
        $client->email = $request->email;
        $client->phone1 = $request->phone1;
        $client->primary_contact = $request->primary_contact;
        $client->address = $request->address;
        $client->business_name = $request->business_name;
        $client->additional_fields = $request->additional_fields;
        $client->updated_by = auth()->id();

        // Handle image update
        if ($request->hasFile('image')) {
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
                'message' => 'Client updated successfully.',
                'client' => $client
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

        if ($client->user) {
            $client->user->delete();
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

    public function loginForm()
    {
        return view('auth.client-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $email = $request->email;
        $password = $request->password;

        $user = User::where('email', $email)->where('user_type', 3)->first();

        if ($user) {
            if ($user->status != 1) {
                return back()->withInput($request->only('email'))
                            ->withErrors(['email' => 'Your client account is inactive.']);
            }

            if (auth()->attempt(['email' => $email, 'password' => $password])) {
                LoginRecord::create(['user_id' => auth()->id()]);
                return redirect()->route('user.dashboard');
            }

            return back()->withInput($request->only('email'))
                        ->withErrors(['password' => 'Wrong password.']);
        }

        return back()->withInput($request->only('email'))
                    ->withErrors(['email' => 'Credential error.']);
    }

    public function clientEmail($id)
    {
        $client = client::find($id)->select('id', 'name','email')->first();
        
        $services = ProjectServiceDetail::with(['serviceType:id,name', 'project:id,title'])
          ->where('client_id', $id)
          ->where('bill_paid', 0)
          ->get(['id','client_id','project_service_id','client_project_id','amount','bill_paid','start_date','end_date']);

        $mailFooter = CompanyDetails::select('mail_footer')->first();
        return view('admin.clients.email', compact('client', 'mailFooter', 'services'));
    }

    public function sendClientEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'      => 'required|exists:clients,id',
            'subject' => 'required|string|max:255',
            'body'    => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $client = Client::findOrFail($request->id);

        Mail::to($client->email)->send(new ClientEmail($request->subject, $request->body));

        return response()->json([
            'status'  => 'success',
            'message' => 'Email sent successfully.'
        ]);
    }

}