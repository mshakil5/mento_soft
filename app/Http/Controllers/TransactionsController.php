<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use App\Models\Invoice;

class TransactionsController extends Controller
{
public function index(Request $request)
    {
        if ($request->ajax()) {

            // Transactions from services (Received + Due not yet received)
            $serviceTxns = Transaction::with('service.client', 'service.project', 'service.serviceType')
                ->where(function ($q) {
                    $q->where('transaction_type', 'Received')
                        ->orWhereHas('service', function ($sub) {
                            $sub->whereDoesntHave('transactions', function ($t) {
                                $t->where('transaction_type', 'Received');
                            });
                        });
                });

            // Transactions from invoices (Due or Received)
            $invoiceTxns = Invoice::with('client')
                ->when($request->client_id, fn($q) => $q->where('client_id', $request->client_id))
                ->where('status', '!=', 2)
                ->latest();

            $all = $serviceTxns->get()->map(fn($row) => (object)[
                'source' => 'service',
                'client_name' => $row->service?->client?->business_name ?? '-',
                'invoice_no' => '-',
                'project' => $row->service?->project?->title ?? '-',
                'service' => $row->service?->serviceType?->name ?? '-',
                'duration' => $row->service?->start_date && $row->service?->end_date 
                                ? Carbon::parse($row->service->start_date)->format('d-m-Y') . ' to ' . Carbon::parse($row->service->end_date)->format('d-m-Y') 
                                : '-',
                'payment_date' => $row->transaction_type === 'Received' ? Carbon::parse($row->date)->format('d-m-Y') : '-',
                'amount' => $row->amount,
                'method' => $row->transaction_type === 'Received' ? $row->payment_type : '-',
                'status' => $row->transaction_type,
                'txn' => $row->transaction_type === 'Received' ? $row->tran_id : '-',
                'note' => $row->description,
            ]);

            $allInvoices = $invoiceTxns->get()->map(fn($inv) => (object)[
                'source' => 'invoice',
                'client_name' => $inv->client?->business_name ?? '-',
                'invoice_no' => $inv->invoice_number,
                'project' => $inv->details->pluck('project_name')->implode('<br>'),
                'service' => '-',
                'duration' => '-',
                'payment_date' => $inv->status == 2 ? Carbon::parse($inv->received_date ?? now())->format('d-m-Y') : '-',
                'amount' => $inv->subtotal,
                'method' => $inv->status == 2 ? ($inv->transactions()->first()?->payment_type ?? '-') : '-',
                'status' => match(true) {
                    $inv->status == 2 => 'Received',
                    $inv->status == 1 && Carbon::parse($inv->invoice_date)->startOfDay() < Carbon::today() => 'Overdue',
                    default => 'Due',
                },
                'txn' => $inv->status == 2 ? ($inv->transactions()->first()?->tran_id ?? '-') : '-',
                'note' => $inv->note ?? '-',
            ]);

            $combined = $all->concat($allInvoices)->sortByDesc(fn($row) => $row->payment_date)->values();

            return DataTables::of($combined)
                ->addIndexColumn()
                ->editColumn('amount', fn($row) => '£' . number_format($row->amount, 2))
                ->editColumn('status', fn($row) => match($row->status) {
                    'Received' => '<span class="badge bg-success">Received</span>',
                    'Overdue' => '<span class="badge bg-danger">Overdue</span>',
                    'Due' => '<span class="badge bg-warning">Due</span>',
                    'Receivable' => '<span class="badge bg-warning">Receivable</span>',
                    default => '<span class="badge bg-secondary">'.$row->status.'</span>'
                })
                ->addColumn('txn', fn($row) => $row->status === 'Received' ? ($row->txn ?? '-') : '-')
                ->rawColumns(['status', 'project'])
                ->make(true);
        }

        return view('admin.transactions.index');
    }

}
