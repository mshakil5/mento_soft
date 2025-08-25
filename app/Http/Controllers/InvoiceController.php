<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Client;
use App\Models\ClientProject;
use Yajra\DataTables\Facades\DataTables;
use Validator;
use DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;
use App\Models\Transaction;
use App\Models\ClientEmailLog;
use App\Models\CompanyDetails;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Invoice::with(['client', 'details'])
                            ->withCount('emailLogs')
                            ->latest();

            if ($request->client_id) {
                $query->where('client_id', $request->client_id);
            }

            $filter = $request->status;

            $invoices = $query->get();

            if ($filter == 'due') {
                $invoices = $invoices->filter->isPending();
            } elseif ($filter == 'received') {
                $invoices = $invoices->reject->isPending();
            }

            return DataTables::of($invoices)
                ->addIndexColumn()
                ->addColumn('date', fn($row) => date('d-m-Y', strtotime($row->invoice_date)))
                ->addColumn('client_name', fn($row) => $row->client->business_name ?? 'N/A')
                ->addColumn('project', fn($row) => $row->details->pluck('project_name')->implode('<br>'))
                ->addColumn('status', function($row) {
                    $invoiceDate = Carbon::parse($row->invoice_date)->startOfDay();
                    $today = Carbon::today();

                    if ($row->status == 2) return '<span class="badge bg-success">Received</span>';
                    if ($row->status == 1 && $invoiceDate < $today) return '<span class="badge bg-danger">Overdue</span>';
                    return '<span class="badge bg-warning">Due</span>';
                })
                ->addColumn('action', function($row) {
                    $emailBtn = $row->client->email 
                        ? '<button class="btn btn-sm btn-warning send-email" 
                                data-id="'.$row->id.'" 
                                data-email="'.$row->client->email.'" 
                                title="'.($row->email_logs_count > 0 ? "{$row->email_logs_count} emails sent" : 'Send first email').'">
                            <span class="spinner-border spinner-border-sm d-none"></span>
                            Send Email' 
                            . ($row->email_logs_count > 0 ? " ({$row->email_logs_count})" : '') .
                          '</button>'
                        : '';

                    $btn = '';
                    if ($row->isPending()) {
                        $btn .= '<button class="btn btn-sm btn-success" data-toggle="modal" data-target="#receiveModal'.$row->id.'">Receive</button> ';
                        $btn .= '<button class="btn btn-sm btn-info edit" data-id="'.$row->id.'">Edit</button> ';
                        $btn .= '<button class="btn btn-sm btn-danger delete" data-id="'.$row->id.'">Delete</button> ';

                        $btn .= '
                        <div class="modal fade" id="receiveModal'.$row->id.'" tabindex="-1">
                            <div class="modal-dialog">
                                <form method="POST" action="'.route('invoices.receive', $row->id).'" class="receive-form">
                                    '.csrf_field().'
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Mark This Invoice as Received</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
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
                                            <button type="submit" class="btn btn-success">Submit</button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>';
                    }
                    $btn .= '<a href="'.route('invoices.show', $row->id).'" class="btn btn-sm btn-primary view" target="_blank">View</a> ';
                    $btn .= $emailBtn;

                    return $btn;
                })
                ->rawColumns(['action', 'status', 'project'])
                ->make(true);
        }

        return view('admin.invoices.index');
    }

    public function create()
    {
        $clients = Client::where('status', 1)->get();

        $latest = Invoice::orderBy('id', 'desc')->first();
        $invoiceNumber = $latest ? ($latest->invoice_number + 1) : 1001;

        return response()->json([
            'clients' => $clients,
            'invoice_number' => $invoiceNumber
        ]);
    }

    public function store(Request $request)
    {
        if (is_string($request->projects)) {
            $request->merge(['projects' => json_decode($request->projects, true)]);
        }

        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'invoice_date' => 'required|date',
            'vat_percent' => 'nullable|numeric',
            'discount_percent' => 'nullable|numeric',
            'description' => 'nullable|string',
            'projects' => 'required|array',
            'projects.*.client_project_id' => 'nullable|exists:client_projects,id',
            'projects.*.project_name' => 'required|string',
            'projects.*.description' => 'nullable|string',
            'projects.*.qty' => 'required|numeric|min:1',
            'projects.*.unit_price' => 'required|numeric|min:0',
            'projects.*.vat_percent' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $invoice = new Invoice();
            $invoice->invoice_number = $request->invoice_number;
            $invoice->client_id = $request->client_id;
            $invoice->invoice_date = $request->invoice_date;
            $invoice->vat_percent = $request->vat_percent ?? 0;
            $invoice->discount_percent = $request->discount_percent ?? 0;
            $invoice->description = $request->description;
            $invoice->created_by = auth()->id();
            $invoice->save();

            $subtotal = 0;
            $totalVat = 0;

            foreach ($request->projects as $project) {
                $qty = $project['qty'];
                $unitPrice = $project['unit_price'];
                $vatPercent = $project['vat_percent'] ?? 0;
                
                $totalExcVat = $qty * $unitPrice;
                $vatAmount = $totalExcVat * ($vatPercent / 100);
                $totalIncVat = $totalExcVat + $vatAmount;

                $detail = new InvoiceDetail();
                $detail->invoice_id = $invoice->id;
                $detail->client_project_id = $project['client_project_id'] ?? null;
                $detail->project_name = $project['project_name'];
                $detail->description = $project['description'] ?? null;
                $detail->qty = $qty;
                $detail->unit_price = $unitPrice;
                $detail->vat_percent = $vatPercent;
                $detail->vat_amount = $vatAmount;
                $detail->total_exc_vat = $totalExcVat;
                $detail->total_inc_vat = $totalIncVat;
                $detail->save();

                $subtotal += $totalExcVat;
                $totalVat += $vatAmount;
            }

            $discountAmount = $subtotal * ($invoice->discount_percent / 100);
            $netWithoutVat = $subtotal - $discountAmount;
            $netAmount = ($subtotal + $totalVat) - $discountAmount;

            $invoice->subtotal = $subtotal;
            $invoice->vat_amount = $totalVat;
            $invoice->discount_amount = $discountAmount;
            $invoice->net_amount = $netAmount;
            $invoice->save();

            $transaction = new Transaction();
            $transaction->date = $invoice->invoice_date;
            $transaction->invoice_id = $invoice->id;
            $transaction->client_id = $invoice->client_id;
            $transaction->table_type = 'Income';
            $transaction->transaction_type = 'Due';
            $transaction->payment_type = 'Bank';
            $transaction->description = $invoice->description;
            $transaction->amount = $netWithoutVat;
            $transaction->at_amount = $invoice->net_amount;
            $transaction->vat_amount = $invoice->vat_amount;
            $transaction->discount = $invoice->discount_amount;
            $transaction->created_by = auth()->id();
            $transaction->created_ip = request()->ip();
            $transaction->save();
            $transaction->tran_id = 'AT' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
            $transaction->save();

            if ($request->send_email == 1) {
                $this->sendInvoiceEmail($invoice);
                DB::commit();
                return response()->json([
                    'status' => 200,
                    'message' => 'Invoice created and emailed successfully.'
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Invoice created successfully.',
                'redirect' => route('invoices.show', $invoice->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Server Error: ' . $e->getMessage()
            ], 500);
        }
    }

    private function sendInvoiceEmail($invoice)
    {
        if (!$invoice->client || !$invoice->client->email) {
            throw new \Exception('Client email not found');
        }

        try {
            Mail::to($invoice->client->email)->queue(new InvoiceMail($invoice));

            ClientEmailLog::create([
                'client_id'       => $invoice->client_id,
                'invoice_id'      => $invoice->id,
                'recipient_email' => $invoice->client->email,
                'subject'         => 'Your Invoice from ' . (optional(CompanyDetails::first())->business_name ?? config('app.name')),
                'message'         => view('emails.invoice', compact('invoice'))->render(),
                'status'          => 1,
                'created_by'      => auth()->id(),
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function show($id)
    {
        $invoice = Invoice::with(['client', 'details'])->findOrFail($id);
        return view('admin.invoices.show', compact('invoice'));
    }

    public function edit($id)
    {
        $invoice = Invoice::with(['client', 'details'])->findOrFail($id);
        $clients = Client::where('status', 1)->get();
        $projects = ClientProject::where('client_id', $invoice->client_id)->get();
        
        return response()->json([
            'invoice' => $invoice,
            'clients' => $clients,
            'projects' => $projects
        ]);
    }

    public function update(Request $request)
    {
        if (is_string($request->projects)) {
            $request->merge(['projects' => json_decode($request->projects, true)]);
        }

        $validator = Validator::make($request->all(), [
            'codeid' => 'required|exists:invoices,id',
            'client_id' => 'required|exists:clients,id',
            'invoice_date' => 'required|date',
            'vat_percent' => 'nullable|numeric',
            'discount_percent' => 'nullable|numeric',
            'description' => 'nullable|string',
            'projects' => 'required|array',
            'projects.*.id' => 'nullable|exists:invoice_details,id',
            'projects.*.client_project_id' => 'nullable|exists:client_projects,id',
            'projects.*.project_name' => 'required|string',
            'projects.*.description' => 'nullable|string',
            'projects.*.qty' => 'required|numeric|min:1',
            'projects.*.unit_price' => 'required|numeric|min:0',
            'projects.*.vat_percent' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $invoice = Invoice::findOrFail($request->codeid);
            $invoice->client_id = $request->client_id;
            $invoice->invoice_date = $request->invoice_date;
            $invoice->vat_percent = $request->vat_percent ?? 0;
            $invoice->discount_percent = $request->discount_percent ?? 0;
            $invoice->description = $request->description;
            $invoice->updated_by = auth()->id();
            
            $existingIds = collect($request->projects)->pluck('id')->filter()->toArray();
            InvoiceDetail::where('invoice_id', $invoice->id)
                ->whereNotIn('id', $existingIds)
                ->delete();

            $subtotal = 0;
            $totalVat = 0;

            foreach ($request->projects as $project) {
                $qty = $project['qty'];
                $unitPrice = $project['unit_price'];
                $vatPercent = $project['vat_percent'] ?? 0;
                
                $totalExcVat = $qty * $unitPrice;
                $vatAmount = $totalExcVat * ($vatPercent / 100);
                $totalIncVat = $totalExcVat + $vatAmount;

                if (isset($project['id'])) {
                    $detail = InvoiceDetail::findOrFail($project['id']);
                } else {
                    $detail = new InvoiceDetail();
                    $detail->invoice_id = $invoice->id;
                }

                $detail->client_project_id = $project['client_project_id'] ?? null;
                $detail->project_name = $project['project_name'];
                $detail->description = $project['description'] ?? null;
                $detail->qty = $qty;
                $detail->unit_price = $unitPrice;
                $detail->vat_percent = $vatPercent;
                $detail->vat_amount = $vatAmount;
                $detail->total_exc_vat = $totalExcVat;
                $detail->total_inc_vat = $totalIncVat;
                $detail->save();

                $subtotal += $totalExcVat;
                $totalVat += $vatAmount;
            }

            $discountAmount = $subtotal * ($invoice->discount_percent / 100);
            $netWithoutVat = $subtotal - $discountAmount;
            $netAmount = ($subtotal + $totalVat) - $discountAmount;

            $invoice->subtotal = $subtotal;
            $invoice->vat_amount = $totalVat;
            $invoice->discount_amount = $discountAmount;
            $invoice->net_amount = $netAmount;
            $invoice->save();

            $transaction = Transaction::where('invoice_id', $invoice->id)->first();
            if ($transaction) {
                $transaction->date = $invoice->invoice_date;
                $transaction->client_id = $invoice->client_id;
                $transaction->description = $invoice->description;
                $transaction->amount = $netWithoutVat;
                $transaction->at_amount = $invoice->net_amount;
                $transaction->vat_amount = $invoice->vat_amount;
                $transaction->discount = $invoice->discount_amount;
                $transaction->updated_by = auth()->id();
                $transaction->updated_ip = request()->ip();
                $transaction->save();
            }

            if ($request->send_email == 1) {
                $this->sendInvoiceEmail($invoice);
                DB::commit();
                return response()->json([
                    'status' => 200,
                    'message' => 'Invoice updated and emailed successfully.'
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Invoice updated successfully.',
                'redirect' => route('invoices.show', $invoice->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Server Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $invoice = Invoice::find($id);
        
        if (!$invoice) {
            return response()->json(['success' => false, 'message' => 'Invoice not found.'], 404);
        }

        $invoice->details()->delete();
        $invoice->transactions()->delete();
        $invoice->delete();

        return response()->json(['success' => true, 'message' => 'Invoice deleted successfully.']);
    }

    public function getClientInfo($id)
    {
        $client = Client::findOrFail($id);
        return response()->json([
            'email' => $client->email,
            'phone1' => $client->phone1,
            'phone2' => $client->phone2,
            'address' => $client->address,
            'business_name' => $client->business_name
        ]);
    }

    public function getClientProjects($id)
    {
        $projects = ClientProject::where('client_id', $id)->get();
        return response()->json($projects);
    }

    public function getProjectInfo($id)
    {
        $project = ClientProject::findOrFail($id);
        return response()->json([
            'title' => $project->title,
            'description' => $project->description
        ]);
    }

    public function sendEmail($id)
    {
        $invoice = Invoice::with(['client', 'details'])->findOrFail($id);

        if (!$invoice->client || !$invoice->client->email) {
            return response()->json(['message' => 'Client email not found.'], 422);
        }

        try {
            $this->sendInvoiceEmail($invoice);

            return response()->json(['message' => 'Invoice email sent successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to send email: ' . $e->getMessage()], 500);
        }
    }

    public function receive(Request $request, Invoice $invoice)
    {
        $prevTransaction = Transaction::where('invoice_id', $invoice->id)->where('transaction_type', 'Due')->first();
        $transaction = new Transaction();
        $transaction->date = date('Y-m-d');
        $transaction->invoice_id = $invoice->id;
        $transaction->client_id = $invoice->client_id;
        $transaction->table_type = 'Income';
        $transaction->transaction_type = 'Received';
        $transaction->payment_type = $request->payment_type;
        $transaction->description = $request->note ?? 'Due Received for Invoice: ' . $invoice->invoice_number;
        $transaction->amount = $invoice->subtotal;
        $transaction->at_amount = $invoice->net_amount;
        $transaction->vat_amount = $invoice->vat_amount;
        $transaction->discount = $invoice->discount_amount;
        $transaction->created_by = auth()->id();
        $transaction->created_ip = request()->ip();
        $transaction->save();
        $transaction->tran_id = 'AT' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
        $transaction->save();

        $invoice->status = 2;
        $invoice->save();
        return back()->with('success', 'Invoice received successfully.');
    }
}