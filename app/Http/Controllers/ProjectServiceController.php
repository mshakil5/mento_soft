<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProjectService;
use Validator;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ProjectServiceDetail;
use Carbon\Carbon;
use App\Models\Client;
use App\Models\Transaction;
use App\Models\ClientProject;
use Mail;
use Illuminate\Support\Facades\DB;

class ProjectServiceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ProjectServiceDetail::with(['serviceType', 'client', 'project'])->latest();

            $latestType1Ids = ProjectServiceDetail::where('type', 1)
              // ->where('bill_paid', '!=', 1)
              ->selectRaw('MAX(id) as id')
              ->groupBy('project_service_id', 'client_id', 'client_project_id')
              ->pluck('id')
              ->toArray();

            $type2Ids = ProjectServiceDetail::where('type', 2)
              ->selectRaw('MAX(id) as id')
              ->groupBy('project_service_id', 'client_id', 'client_project_id')
              ->pluck('id')
              ->toArray();

            // $type2Ids = ProjectServiceDetail::where('type', 2)->pluck('id')->toArray();
        
            $idsToDisplay = array_merge($latestType1Ids, $type2Ids);

            $data = $data->whereIn('id', $idsToDisplay);

            if ($request->client_filter_id) {
                $data = $data->where('client_id', $request->client_filter_id);
            }

            if ($request->client_id) {
                $data = $data->where('client_id', $request->client_id);
            }

            if ($request->project_filter_id) {
                $data = $data->where('client_project_id', $request->project_filter_id);
            }

            if ($request->service_filter_type_id) {
                $data = $data->where('project_service_id', $request->service_filter_type_id);
            }

            if ($request->project_service_id) {
                $data = $data->where('project_service_id', $request->project_service_id);
            }

          if ($request->has('bill_paid')) {
              $data = $data->where('bill_paid', $request->bill_paid);
          }

          if ($request->has('status')) {
              $data = $data->where('status', $request->status);
          }

          $monthlyLimit = Carbon::now()->addDays(7)->format('Y-m-d');
          $yearlyLimit  = Carbon::now()->addMonths(3)->format('Y-m-d');

          if ($request->has('expiring') && $request->expiring == 1) {
              $data = $data->where(function($query) use ($monthlyLimit, $yearlyLimit) {
                  $query->where(function($q) use ($monthlyLimit) {
                      $q->where('cycle_type', 1)
                        ->whereRaw("STR_TO_DATE(end_date, '%Y-%m-%d') <= ?", [$monthlyLimit]);
                  })
                  ->orWhere(function($q) use ($yearlyLimit) {
                      $q->where('cycle_type', 2)
                        ->whereRaw("STR_TO_DATE(end_date, '%Y-%m-%d') <= ?", [$yearlyLimit]);
                  });
              });
          }

          if ($request->has('due')) {
              $currentMonthStart = Carbon::now()->startOfMonth()->format('Y-m-d');
              $currentMonthEnd   = Carbon::now()->endOfMonth()->format('Y-m-d');
              $nextMonthStart    = Carbon::now()->addMonth()->startOfMonth()->format('Y-m-d');
              $nextMonthEnd      = Carbon::now()->addMonth()->endOfMonth()->format('Y-m-d');

              if ($request->due === 'current') {
                  $data->whereRaw("STR_TO_DATE(start_date, '%Y-%m-%d') BETWEEN ? AND ?", [$currentMonthStart, $currentMonthEnd]);
              } elseif ($request->due === 'next') {
                  $data->whereRaw("STR_TO_DATE(start_date, '%Y-%m-%d') BETWEEN ? AND ?", [$nextMonthStart, $nextMonthEnd]);
              } elseif ($request->due === 'previous') {
                  $data->whereRaw("STR_TO_DATE(start_date, '%Y-%m-%d') < ?", [Carbon::now()->format('Y-m-d')]);
              }
          }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('checkbox', function($row) {
                    return '<input type="checkbox" class="row-checkbox" value="'.$row->id.'" data-client_id="'.$row->client_id.'">';
                })
                // ->addColumn('start_date', fn($row) => $row->start_date ? Carbon::parse($row->start_date)->format('d-m-Y') : 'N/A')
                ->addColumn('start_date', function ($row) {
                    if ($row->type == 1) {
                        $firstRecord = ProjectServiceDetail::where('project_service_id', $row->project_service_id)
                            ->where('client_id', $row->client_id)
                            ->where('client_project_id', $row->client_project_id)
                            ->where('type', 1)
                            ->oldest('start_date')
                            ->first();
                        return $firstRecord->start_date ? Carbon::parse($firstRecord->start_date)->format('d-m-Y') : '';
                    }
                    return $row->start_date ? Carbon::parse($row->start_date)->format('d-m-Y') : '';
                })
                ->addColumn('end_date', function ($row) {
                    if ($row->bill_paid == 1) {
                        return '';
                    }
                    return $row->end_date ? Carbon::parse($row->end_date)->format('d-m-Y') : '';
                })
                ->addColumn('due_date', function ($row) {
                    if ($row->bill_paid == 1) return '';
                    return $row->due_date ? Carbon::parse($row->due_date)->format('d-m-Y') : '';
                })
                ->addColumn('next_renewal', function($row) {
                    if ($row->is_auto == 1 && $row->next_start_date && $row->next_end_date) {
                        $cycle = $row->cycle_type == 1 ? 'Monthly' : ($row->cycle_type == 2 ? 'Yearly' : '');
                        return Carbon::parse($row->next_start_date)->format('d-m-Y') 
                            . ' - ' 
                            . Carbon::parse($row->next_end_date)->format('d-m-Y')
                            . ($cycle ? " ({$cycle})" : '');
                    }
                    return '';
                })
                ->addColumn('service_type', function($row) {
                    $typeText = $row->type == 1 ? 'In House' : 'Third Party';
                    return $row->serviceType?->name . ' (' . $typeText . ')';
                })
                ->addColumn('client_name', fn($row) => $row->client?->name)
                ->addColumn('project_title', fn($row) => $row->project?->title)
                ->addColumn('amount', function ($row) {
                    if ($row->bill_paid == 1) return '';
                    if ($row->type == 1) {
                        $totalAmount = ProjectServiceDetail::where('project_service_id', $row->project_service_id)
                            ->where('client_id', $row->client_id)
                            ->where('client_project_id', $row->client_project_id)
                            ->where('bill_paid', '!=', 1)
                            ->where('type', 1)
                            ->sum('amount');
                        $count = ProjectServiceDetail::where('project_service_id', $row->project_service_id)
                            ->where('client_id', $row->client_id)
                            ->where('bill_paid', '!=', 1)
                            ->where('client_project_id', $row->client_project_id)
                            ->where('type', 1)
                            ->count();

                        $cycleText = $row->cycle_type == 1 ? 'month' : 'year';
                        return '£' . number_format($row->amount, 0) . ' x ' . $count . ' ' . $cycleText . ' = £' . number_format($totalAmount, 0);
                    }
                    return '£' . number_format($row->amount, 0);
                })
                ->addColumn('note', fn($row) => \Str::limit(strip_tags($row->note), 100))
                ->addColumn('status', function($row) {
                    $checked = $row->status ? 'checked' : '';
                    return '<div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input toggle-status" id="customSwitchStatus'.$row->id.'" data-id="'.$row->id.'" '.$checked.'>
                                <label class="custom-control-label" for="customSwitchStatus'.$row->id.'"></label>
                            </div>';
                })
                ->addColumn('action', function($row) {
                    $btn = '';

                    //in house service details
                    if ($row->type == 1) {
                        $btn .= '<button class="btn btn-sm btn-secondary" data-toggle="modal" data-target="#billDetailsModal'.$row->id.'">
                                    <i class="fas fa-list"></i>
                                </button>';

                        $btn .= '
                        <div class="modal fade" id="billDetailsModal'.$row->id.'" tabindex="-1" role="dialog" aria-hidden="true">
                          <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title">Services</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                              </div>
                              <div class="modal-body">
                                <table class="table table-bordered">
                                  <thead>
                                    <tr>
                                      <th>#</th>
                                      <th>Date</th>
                                      <th>Amount</th>
                                      <th>Status</th>
                                    </tr>
                                  </thead>
                                  <tbody>';

                        $bills = ProjectServiceDetail::where('project_service_id', $row->project_service_id)
                            ->where('client_id', $row->client_id)
                            ->where('client_project_id', $row->client_project_id)
                            ->where('type', 1)
                            ->orderBy('start_date')
                            ->get();

                        foreach ($bills as $index => $bill) {
                            $dateRange = '';
                            if ($bill->start_date && $bill->end_date) {
                                $dateRange = Carbon::parse($bill->start_date)->format('j F Y') . ' - ' .
                                            Carbon::parse($bill->end_date)->format('j F Y');
                            }

                            if ($bill->bill_paid) {
                                $status = '<span class="badge badge-success">Received</span>';
                            } elseif ($bill->due_date && Carbon::parse($bill->due_date)->lt(Carbon::today())) {
                                $status = '<span class="badge badge-danger">Overdue</span>';
                            } else {
                                $status = '<span class="badge badge-warning">Pending</span>';
                            }

                            $btn .= '<tr>
                                      <td>'.($index+1).'</td>
                                      <td>'.$dateRange.'</td>
                                      <td>£'.number_format($bill->amount, 0).'</td>
                                      <td>'.$status.'</td>
                                    </tr>';
                        }

                        $btn .= '   </tbody>
                                </table>
                              </div>
                            </div>
                          </div>
                        </div>';
                    }

                    if ($row->isPending()) {
                        $isType1 = $row->type == 1;
                        $buttonText = $isType1 ? 'Receive' : 'Renew';
                        
                        $btn .= '<button class="btn btn-sm btn-success" data-toggle="modal" data-target="#receiveModal'.$row->id.'">'.$buttonText.'</button>';
                        if (auth()->user()->can('edit service')) {
                            $btn .= ' <button class="btn btn-sm btn-info edit" data-id="'.$row->id.'">Edit</button>';
                        }
                        $btn .= ' <button class="btn btn-sm btn-danger delete d-none" data-id="'.$row->id.'">Delete</button>';

                        // Modal
                        $btn .= '
                        <div class="modal fade" id="receiveModal'.$row->id.'" tabindex="-1" role="dialog" aria-hidden="true">
                          <div class="modal-dialog">
                            <form method="POST" action="'.route('project-service.receive').'" class="receive-form">
                              '.csrf_field().'
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title">'.$buttonText.' Payment</h5>
                                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">';
                        
                        if ($isType1) {
                            $bills = ProjectServiceDetail::where('project_service_id', $row->project_service_id)
                                ->where('client_id', $row->client_id)
                                ->where('client_project_id', $row->client_project_id)
                                ->where('type', 1)
                                ->where('bill_paid', '!=', 1)
                                ->orderBy('start_date')->get();
                            
                            $btn .= '<div class="mb-3">
                                        <label>Select Month(s) <span class="text-danger">*</span></label>
                                        <select name="bill_ids[]" class="form-control bill-select select2" multiple required>';
                            foreach ($bills as $bill) {
                                $btn .= '<option value="'.$bill->id.'" data-amount="'.$bill->amount.'">'.
                                        Carbon::parse($bill->start_date)->format('d-m-Y').' - '.
                                        Carbon::parse($bill->end_date)->format('d-m-Y').
                                        ' ( £'.number_format($bill->amount, 0).' )</option>';
                            }
                            $btn .= '</select></div>';
                            $btn .= '<div class="mb-3">
                                        <label>Total Amount</label>
                                        <input type="text" class="form-control total-amount" readonly>
                                    </div>';
                        } else {
                            $btn .= '<input type="hidden" name="bill_ids[]" value="'.$row->id.'">';
                            $btn .= '<div class="mb-3">
                                        <label>Total Amount</label>
                                        <input type="text" class="form-control total-amount" readonly value="£'.number_format($row->amount,2).'">
                                    </div>';
                        }

                        $btn .= '<div class="mb-3">
                                    <label>Payment Type <span class="text-danger">*</span></label>
                                    <select name="payment_type" class="form-control" required>
                                        <option value="Cash">Cash</option>
                                        <option value="Bank">Bank</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label>Note</label>
                                    <textarea name="note" class="form-control"></textarea>
                                </div>
                                </div>
                                <div class="modal-footer">
                                  <button type="submit" class="btn btn-success">'.$buttonText.'</button>
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                              </div>
                            </form>
                          </div>
                        </div>';
                    } else {
                        $disabledText = $row->type == 1 ? 'Received' : 'Renewed';
                        $btn .= '<button class="btn btn-sm btn-success" disabled>'.$disabledText.'</button>';
                    }

                    $btn .= '<a href="'.route('project-services.invoice.show', $row->id).'" class="btn btn-sm btn-primary" target="_blank">Invoice</a> ';

                    $btn .= '<a href="'.route('client.email', ['id' => $row->client_id, 'type' => 'ProjectServiceDetail', 'type_id' => $row->id]).'" 
                                class="btn btn-sm btn-warning" 
                                title="Send Email">
                                <i class="fas fa-envelope"></i>
                            </a>';


                    return $btn;
                })
                ->rawColumns(['status', 'action', 'is_renewed', 'checkbox'])
                ->make(true);
        }

        $serviceTypes = ProjectService::where('status', 1)->latest()->get();
        $clients = Client::where('status', 1)->select('id', 'business_name')->latest()->get();
        $projects = ClientProject::select('id', 'title')->latest()->get();
        return view('admin.client-projects.services', compact('serviceTypes', 'clients', 'projects'));
    }

    public function invoice($id)
    {
        $service = ProjectServiceDetail::with(['serviceType', 'client'])->findOrFail($id);
        return view('admin.client-projects.service_invoice', compact('service'));
    }

    public function sendMultiEmail(Request $request)
    {
      dd($request->all());
      return response()->json(['message' => 'Service invoice email sent successfully.']);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_type_id' => 'required',
            'client_id' => 'required',
            'client_project_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|after_or_equal:start_date',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
            'cycle_type' => 'nullable|in:1,2',
            'is_auto' => 'nullable|boolean',
            'type' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'errors' => $validator->errors()], 422);
        }

        $isAuto = $request->input('is_auto') == 1 ? 1 : 0;
        $cycleType = $request->input('cycle_type');

        $exists = ProjectServiceDetail::where([
            'project_service_id' => $request->service_type_id,
            'client_id' => $request->client_id,
            'client_project_id' => $request->client_project_id,
            'type' => $request->type,
            'is_auto' => $isAuto,
            'cycle_type' => $cycleType,
            'status' => 1,
        ])->exists();

        if ($exists) {
            return response()->json([
                'status' => 422,
                'errors' => [
                    'duplicate' => ['An active record with the same details already exists.']
                ]
            ], 422);
        }

        $startDate = Carbon::parse($request->start_date)->format('Y-m-d');
        $endDate   = Carbon::parse($request->end_date)->format('Y-m-d');

        $nextStartDate = Carbon::parse($endDate)->addDay()->format('Y-m-d');
        $nextEndDate = null;
        if ($cycleType == 1) {
            $nextEndDate = Carbon::parse($nextStartDate)->addMonthNoOverflow()->subDay()->format('Y-m-d');
        } elseif ($cycleType == 2) {
            $nextEndDate = Carbon::parse($nextStartDate)->addYear()->subDay()->format('Y-m-d');
        }

        $dueDate = null;
        if ($cycleType == 1) {
            $dueDate = Carbon::parse($endDate)->subWeeks(2)->format('Y-m-d');
        } elseif ($cycleType == 2) {
            $dueDate = Carbon::parse($endDate)->subMonths(3)->format('Y-m-d');
        }

        $detail = ProjectServiceDetail::create([
            'project_service_id' => $request->service_type_id,
            'client_id' => $request->client_id,
            'client_project_id' => $request->client_project_id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'due_date' => $dueDate,
            'amount' => $request->amount,
            'note' => $request->note,
            'status' => true,
            'type' => $request->type,
            'is_auto' => $isAuto,
            'cycle_type' => $cycleType,
            'next_start_date' => $nextStartDate,
            'next_end_date' => $nextEndDate,
            'created_by' => auth()->id(),
        ]);
        
        $service = ProjectService::find($request->service_type_id);

        $transaction = new Transaction();
        $transaction->date = $startDate;
        $transaction->project_service_detail_id = $detail->id;
        $transaction->client_id = $request->client_id;
        $transaction->table_type = 'Income';
        $transaction->transaction_type = 'Due';
        $transaction->payment_type = 'Bank';
        $transaction->description = $detail->note ?? "Due for {$service->name} for service period ".$startDate." to ".$endDate;
        $transaction->amount = $detail->amount;
        $transaction->at_amount = $detail->amount;
        $transaction->created_by = auth()->id();
        $transaction->created_ip = $request->ip();
        $transaction->save();
        $transaction->tran_id = 'AT' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
        $transaction->save();

        return response()->json([
            'status' => 200,
            'message' => 'Created successfully.',
            'data' => $detail
        ], 201);
    }

    public function edit(ProjectServiceDetail $service)
    {
        if (!$service) {
            return response()->json(['status' => 404, 'message' => 'Service not found'], 404);
        }
        return response()->json($service);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_type_id' => 'required',
            'client_id' => 'required',
            'client_project_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
            'cycle_type' => 'nullable|in:1,2',
            'is_auto' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'errors' => $validator->errors()], 422);
        }

        $isAuto = $request->input('is_auto') == 1 ? 1 : 0;
        $cycleType = $isAuto ? (int) $request->input('cycle_type', 2) : null;

        $startDate = Carbon::parse($request->start_date)->format('Y-m-d');
        $endDate = Carbon::parse($request->end_date)->format('Y-m-d');

        $nextStartDate = null;
        $nextEndDate = null;

        if ($isAuto) {
            $nextStartDate = Carbon::parse($endDate)->addDay();

            if ($cycleType === 1) {
                $nextEndDate = $nextStartDate->copy()->addMonthNoOverflow()->subDay();
            } elseif ($cycleType === 2) {
                $nextEndDate = $nextStartDate->copy()->addYear()->subDay();
            }

            $nextStartDate = $nextStartDate->format('Y-m-d');
            $nextEndDate = $nextEndDate->format('Y-m-d');
        }

        $dueDate = null;

        if ($cycleType == 1) {
            $dueDate = Carbon::parse($endDate)->subWeeks(2)->format('Y-m-d');
        } elseif ($cycleType == 2) {
            $dueDate = Carbon::parse($endDate)->subMonths(3)->format('Y-m-d');
        }

        $detail = ProjectServiceDetail::find($request->codeid);
        $detail->project_service_id = $request->service_type_id;
        $detail->client_id = $request->client_id;
        $detail->client_project_id = $request->client_project_id;
        $detail->start_date = $startDate;
        $detail->end_date = $endDate;
        $detail->due_date = $dueDate;
        $detail->type = $request->type;
        $detail->amount = $request->amount;
        $detail->note = $request->note;
        $detail->is_auto = $isAuto;
        $detail->cycle_type = $request->cycle_type;
        $detail->next_start_date = $nextStartDate;
        $detail->next_end_date = $nextEndDate;
        $detail->updated_by = auth()->id();

        if ($detail->save()) {
           $service = ProjectService::find($request->service_type_id);
            $transaction = Transaction::where('project_service_detail_id', $detail->id)->first();
            if ($transaction) {
                $transaction->date = $startDate;
                $transaction->client_id = $request->client_id;
                $transaction->description = $detail->note;
                $transaction->amount = $detail->amount;
                $transaction->at_amount = $detail->amount;
                $transaction->updated_by = auth()->id();
                $transaction->updated_ip = $request->ip();
                $transaction->save();
            }

            return response()->json([
                'status' => 200,
                'message' => 'Updated successfully.',
                'data' => $detail
            ], 200);
        }
        return response()->json(['status' => 500, 'message' => 'Server error.'], 500);
    }

    public function destroy(ProjectServiceDetail $detail)
    {
        if (!$detail) {
            return response()->json(['success'=>false, 'message'=>'Detail not found'], 404);
        }

        $detail->transactions()->delete();
        
        if ($detail->delete()) {
            return response()->json(['success'=>true, 'message'=>'Detail deleted successfully']);
        }

        return response()->json(['success'=>false, 'message'=>'Failed to delete detail'], 500);
    }

    public function toggleStatus(Request $request, ProjectServiceDetail $detail)
    {
        if (!$detail) {
            return response()->json(['status'=>404, 'message'=>'Detail not found']);
        }

        $detail->status = $request->status;
        $detail->save();

        return response()->json(['status'=>200, 'message'=>'Status updated successfully']);
    }

    public function receive(Request $request)
    {
        $billIds = $request->bill_ids ?? [];
        $paymentType = $request->payment_type;
        $note = $request->note;

        foreach ($billIds as $id) {
            $serviceDetail = ProjectServiceDetail::with('serviceType')->find($id);
            if (!$serviceDetail || $serviceDetail->bill_paid) continue;

            $previousTransaction = Transaction::where('project_service_detail_id', $id)
                ->where('transaction_type', 'Due')->first();

            $transaction = new Transaction();
            $transaction->date = date('Y-m-d');
            $transaction->project_service_detail_id = $id;
            $transaction->client_id = $previousTransaction->client_id ?? $serviceDetail->client_id;
            $transaction->table_type = 'Income';
            $transaction->transaction_type = 'Received';
            $transaction->payment_type = $paymentType;
            $transaction->description = $note ?? "Due received for {$serviceDetail->serviceType->name} for service period ".
                Carbon::parse($serviceDetail->start_date)->format('d-m-Y').
                " to ".Carbon::parse($serviceDetail->end_date)->format('d-m-Y');
            $transaction->amount = $previousTransaction->amount ?? $serviceDetail->amount;
            $transaction->at_amount = $transaction->amount;
            $transaction->created_by = auth()->id();
            $transaction->created_ip = request()->ip();
            $transaction->save();

            $transaction->tran_id = 'AT' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
            $transaction->save();

            $serviceDetail->bill_paid = 1;
            $serviceDetail->save();
        }

        return response()->json(['message' => 'Received successfully.']);
    }

    public function projects(Client $client)
    {
        return response()->json($client->projects()->select('id', 'title')->get());
    }
}