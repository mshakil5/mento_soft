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
                ->addColumn('start_date', fn($row) => $row->start_date ? Carbon::parse($row->start_date)->format('d-m-Y') : 'N/A')
                ->addColumn('end_date', fn($row) => $row->end_date ? Carbon::parse($row->end_date)->format('d-m-Y') : 'N/A')
                ->addColumn('next_renewal', function($row) {
                    if ($row->is_auto == 1 && $row->next_start_date && $row->next_end_date) {
                        return Carbon::parse($row->next_start_date)->format('d-m-Y') 
                            . ' - ' 
                            . Carbon::parse($row->next_end_date)->format('d-m-Y');
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
                        $btn .= '<form method="POST" action="'.route('project-service-details.receive', $row->id).'" style="display:inline;" class="receive-form">'
                              . csrf_field()
                              . '<button type="submit" class="btn btn-sm btn-success receive-btn">Receive</button>'
                              . '</form> ';
                    } else {
                        $btn .= '<button type="submit" class="btn btn-sm btn-success receive-btn disabled">Received</button>'
                              . '</form> ';
                    }
                    $btn .= '<button class="btn btn-sm btn-info edit" data-id="'.$row->id.'">Edit</button> ';
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
            'start_date' => 'required',
            'end_date' => 'required|after_or_equal:start_date',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status'=>422, 'errors'=>$validator->errors()], 422);
        }

        $isAuto = $request->has('is_auto') ? 1 : 0;
        $cycleType = $isAuto ? (int) $request->input('cycle_type', 2) : null;

        if ($isAuto) {
            $startDate = Carbon::parse($request->start_date);
            if ($cycleType === 1) { // Monthly
                $nextStartDate = $startDate->copy()->addMonthNoOverflow();
                $nextEndDate = $nextStartDate->copy()->endOfMonth();
            } else { // Yearly
                $nextStartDate = $startDate->copy()->addYear();
                $nextEndDate = $nextStartDate->copy()->endOfYear();
            }
        } else {
            $nextStartDate = null;
            $nextEndDate = null;
        }

        $detail = ProjectServiceDetail::create([
            'project_service_id' => $service->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
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
        $transaction->date = $request->start_date;
        $transaction->project_service_detail_id = $detail->id;
        $clientId = $detail->projectService?->clientProject?->client_id;
        $transaction->client_id = $clientId;
        $transaction->table_type = 'Assets';
        $transaction->transaction_type = 'Due';
        $transaction->payment_type = 'Bank';
        $transaction->description = $detail->note;
        $transaction->amount = $detail->amount;
        $transaction->at_amount = $detail->amount;
        $transaction->created_by = auth()->id();
        $transaction->created_ip = request()->ip();
        $transaction->save();
        $transaction->tran_id = 'AT' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
        $transaction->save();

        return response()->json(['status'=>200, 'message'=>'Service detail created successfully.', 'data'=>$detail], 201);
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
            'start_date' => 'required',
            'end_date' => 'required|after_or_equal:start_date',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'errors' => $validator->errors()], 422);
        }

        $isAuto = $request->has('is_auto') ? 1 : 0;
        $cycleType = $isAuto ? (int) $request->input('cycle_type', 2) : null;

        if ($isAuto) {
            $startDate = Carbon::parse($request->start_date);
            if ($cycleType === 1) { // Monthly
                $nextStartDate = $startDate->copy()->addMonthNoOverflow();
                $nextEndDate = $nextStartDate->copy()->endOfMonth();
            } else { // Yearly
                $nextStartDate = $startDate->copy()->addYear();
                $nextEndDate = $nextStartDate->copy()->endOfYear();
            }
        } else {
            $nextStartDate = null;
            $nextEndDate = null;
        }

        $detail->start_date = $request->start_date;
        $detail->end_date = $request->end_date;
        $detail->amount = $request->amount;
        $detail->note = $request->note;
        $detail->is_auto = $isAuto;
        $detail->cycle_type = $cycleType;
        $detail->next_start_date = $nextStartDate;
        $detail->next_end_date = $nextEndDate;
        $detail->updated_by = auth()->id();

        if ($detail->save()) {
            $transaction = Transaction::where('project_service_detail_id', $detail->id)->first();
            if ($transaction) {
                $transaction->date = $request->start_date;
                $transaction->client_id = $detail->projectService?->clientProject?->client_id;
                $transaction->description = $detail->note;
                $transaction->amount = $detail->amount;
                $transaction->at_amount = $detail->amount;
                $transaction->updated_by = auth()->id();
                $transaction->updated_ip = request()->ip();
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

    public function receive($id)
    {
        $previousTransaction = Transaction::where('project_service_detail_id', $id)->where('transaction_type', 'Due')->first();
        $transaction = new Transaction();
        $transaction->date = date('Y-m-d');
        $transaction->project_service_detail_id = $id;
        $transaction->client_id = $previousTransaction->client_id;
        $transaction->table_type = 'Assets';
        $transaction->transaction_type = 'Received';
        $transaction->payment_type = 'Bank';
        $transaction->description = 'Due Received';
        $transaction->amount = $previousTransaction->amount;
        $transaction->at_amount = $previousTransaction->amount;
        $transaction->created_by = auth()->id();
        $transaction->created_ip = request()->ip();
        $transaction->save();
        $transaction->tran_id = 'AT' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
        $transaction->save();

        return back()->with('success', 'Received successfully.');
    }

}