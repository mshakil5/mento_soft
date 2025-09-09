<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProjectServiceDetail;
use App\Models\ProjectService;
use Yajra\DataTables\Facades\DataTables;
use Validator;
use Illuminate\Support\Carbon;
use App\Models\Transaction;

class ProjectServiceDetailController extends Controller
{
    public function index(ProjectService $service, Request $request)
    {
        if ($request->ajax()) {
            $data = ProjectServiceDetail::where('project_service_id', $service->id)->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('start_date', fn($row) => $row->start_date ? Carbon::parse($row->start_date)->format('d-m-Y') : '')
                ->addColumn('end_date', fn($row) => $row->end_date ? Carbon::parse($row->end_date)->format('d-m-Y') : '')
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
                ->addColumn('amount', fn($row) => number_format($row->amount, 2))
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
                    if ($row->isPending()) {

                        $btn .= '<button class="btn btn-sm btn-success" data-toggle="modal" data-target="#receiveModal'.$row->id.'">Receive</button> ';
                        $btn .= '<button class="btn btn-sm btn-info edit" data-id="'.$row->id.'">Edit</button> ';

                        $btn .= '
                        <div class="modal fade" id="receiveModal'.$row->id.'" tabindex="-1" role="dialog" aria-hidden="true">
                          <div class="modal-dialog">
                            <form method="POST" action="'.route('project-service-details.receive', $row->id).'" class="receive-form">
                              '.csrf_field().'
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title">Receive this payment</h5>
                                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                  <div class="mb-3">
                                    <label>Payment Type <span class="text-danger"> *</span></label>
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
                                  <button type="submit" class="btn btn-success">Submit</button>
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                              </div>
                            </form>
                          </div>
                        </div>
                        ';
                    } else {
                        $btn .= '<button class="btn btn-sm btn-success" disabled>Receive</button> ';
                        $btn .= '<button class="btn btn-sm btn-info edit" data-id="'.$row->id.'">Edit</button> ';
                    }

                    $btn .= '<button class="btn btn-sm btn-danger delete" data-id="'.$row->id.'">Delete</button>';
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('admin.client-project-services.details', compact('service'));
    }

    public function store(ProjectService $service, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|after_or_equal:start_date',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
            'cycle_type' => 'nullable|in:1,2',
            'is_auto' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['status'=>422, 'errors'=>$validator->errors()], 422);
        }

        $isAuto = $request->input('is_auto') == 1 ? 1 : 0;
        $cycleType = $request->input('cycle_type');

        $startDate = Carbon::parse($request->start_date)->format('Y-m-d');
        $endDate = Carbon::parse($request->end_date)->format('Y-m-d');

        $nextStartDate = null;
        $nextEndDate = null;

        if ($isAuto) {
            $nextStartDate = Carbon::parse($endDate)->addDay()->format('Y-m-d');

            if ($cycleType == 1) {
                $nextEndDate = Carbon::parse($nextStartDate)->addMonthNoOverflow()->subDay()->format('Y-m-d');
            } elseif ($cycleType == 2) {
                $nextEndDate = Carbon::parse($nextStartDate)->addYear()->subDay()->format('Y-m-d');
            }
        }

        $detail = ProjectServiceDetail::create([
            'project_service_id' => $service->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'amount' => $request->amount,
            'note' => $request->note,
            'status' => true,
            'is_auto' => $isAuto,
            'cycle_type' => $cycleType,
            'next_start_date' => $nextStartDate,
            'next_end_date' => $nextEndDate,
            'created_by' => auth()->id(),
        ]);

        $transaction = new Transaction();
        $transaction->date = $startDate;
        $transaction->project_service_detail_id = $detail->id;
        $transaction->client_id = $detail->projectService?->clientProject?->client_id;
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
            'message' => 'Service detail created successfully.',
            'data' => $detail
        ], 201);
    }

    public function edit(ProjectServiceDetail $detail)
    {
        if (!$detail) {
            return response()->json(['status'=>404, 'message'=>'Detail not found'], 404);
        }
        return response()->json($detail);
    }

    public function update(Request $request, ProjectServiceDetail $detail)
    {
        $validator = Validator::make($request->all(), [
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

        $detail->start_date = $startDate;
        $detail->end_date = $endDate;
        $detail->amount = $request->amount;
        $detail->note = $request->note;
        $detail->is_auto = $isAuto;
        $detail->cycle_type = $request->cycle_type;
        $detail->next_start_date = $nextStartDate;
        $detail->next_end_date = $nextEndDate;
        $detail->updated_by = auth()->id();

        if ($detail->save()) {
            $transaction = Transaction::where('project_service_detail_id', $detail->id)->first();
            if ($transaction) {
                $transaction->date = $startDate;
                $transaction->client_id = $detail->projectService?->clientProject?->client_id;
                $transaction->description = $detail->note;
                $transaction->amount = $detail->amount;
                $transaction->at_amount = $detail->amount;
                $transaction->updated_by = auth()->id();
                $transaction->updated_ip = $request->ip();
                $transaction->save();
            }

            return response()->json(['status' => 200, 'message' => 'Service detail updated successfully.']);
        }

        return response()->json(['status' => 500, 'message' => 'Server error'], 500);
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

    public function receive(Request $request, $id)
    {
        $serviceDetail = ProjectServiceDetail::with('projectService')->find($id);
        $previousTransaction = Transaction::where('project_service_detail_id', $id)->where('transaction_type', 'Due')->first();
        $transaction = new Transaction();
        $transaction->date = date('Y-m-d');
        $transaction->project_service_detail_id = $id;
        $transaction->client_id = $previousTransaction->client_id;
        $transaction->table_type = 'Income';
        $transaction->transaction_type = 'Received';
        $transaction->payment_type = $request->payment_type;
        $transaction->description = $request->note ?? "Due payment for {$serviceDetail->projectService->name} for service period ".\Carbon\Carbon::parse($serviceDetail->start_date)->format('d-m-Y')." to ".\Carbon\Carbon::parse($serviceDetail->end_date)->format('d-m-Y');
        $transaction->amount = $previousTransaction->amount;
        $transaction->at_amount = $previousTransaction->amount;
        $transaction->created_by = auth()->id();
        $transaction->created_ip = request()->ip();
        $transaction->save();
        $transaction->tran_id = 'AT' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
        $transaction->save();

        $detail = ProjectServiceDetail::find($id);
        $detail->bill_paid = 1;
        $detail->save();

        return back()->with('success', 'Received successfully.');
    }

}