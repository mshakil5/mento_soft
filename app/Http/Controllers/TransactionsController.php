<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\ClientProject;
use App\Models\ProjectService;

class TransactionsController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $allTransactions = Transaction::with('service.client', 'service.project', 'service.serviceType', 'invoice.client')
            
                //From client, $request->received == 1
                ->when($request->received == 1, function($q) {
                    $q->where('transaction_type', 'Received');
                })
                ->when($request->client_id, function($q) use ($request) {
                  $q->where('client_id', $request->client_id);
                })
                //From project
                ->when($request->client_project_id, function($q) use ($request) {
                    $q->whereHas('projectServiceDetail', function($q2) use ($request) {
                        $q2->where('client_project_id', $request->client_project_id);
                    });
                })
                ->when($request->received == 2, function($q) {
                    $q->where('invoice_id', '');
                })

                ->where(function ($q) {
                    $q->where('transaction_type', 'Received')
                      ->orWhereHas('service', function ($sub) {
                          $sub->whereDoesntHave('transactions', function ($t) {
                              $t->where('transaction_type', 'Received');
                          });
                      });
                })
                ->latest()
                ->get();

            $dueInvoices = Invoice::with('client')
                ->when($request->client_id, fn($q) => $q->where('client_id', $request->client_id))
                ->where('status', '!=', 2)
                ->latest()
                ->get();

            $allCombined = $allTransactions->map(function ($row, $key) {
                $id = $row->id ?? $key;
                if ($row->invoice) {
                    return (object)[
                        'id' => $id,
                        'source' => 'invoice',
                        'client_name' => $row->invoice->client?->business_name ?? '-',
                        'invoice_no' => $row->invoice->invoice_number,
                        'project' => $row->invoice->details->pluck('project_name')->implode('<br>'),
                        'service' => '-',
                        'duration' => '-',
                        'payment_date' => Carbon::parse($row->date)->format('d-m-Y'),
                        'amount' => $row->amount,
                        'method' => $row->payment_type ?? '-',
                        'status' => 'Received',
                        'txn' => $row->tran_id ?? '-',
                        'note' => $row->description,
                    ];
                }

                return (object)[
                    'id' => $id,
                    'source' => 'service',
                    'client_name' => $row->service?->client?->business_name ?? '-',
                    'invoice_no' => '-',
                    'project' => $row->service?->project?->title ?? '-',
                    'service' => $row->service?->serviceType?->name ?? '-',
                    'duration' => $row->service?->start_date && $row->service?->end_date
                        ? Carbon::parse($row->service->start_date)->format('d-m-Y') . ' to ' . Carbon::parse($row->service->end_date)->format('d-m-Y')
                        : '-',
                    'payment_date' => $row->transaction_type === 'Received'
                        ? Carbon::parse($row->date)->format('d-m-Y')
                        : '-',
                    'amount' => $row->amount,
                    'method' => $row->transaction_type === 'Received' ? $row->payment_type : '-',
                    'status' => $row->transaction_type,
                    'txn' => $row->transaction_type === 'Received' ? $row->tran_id : '-',
                    'note' => $row->description,
                ];
            });

            $combined = $allCombined->concat(
                $dueInvoices->map(function ($inv, $key) {
                    $id = $inv->id ?? $key;
                    return (object)[
                        'id' => $id,
                        'source' => 'invoice',
                        'client_name' => $inv->client?->business_name ?? '-',
                        'invoice_no' => $inv->invoice_number,
                        'project' => $inv->details->pluck('project_name')->implode('<br>'),
                        'service' => '-',
                        'duration' => '-',
                        'payment_date' => '-',
                        'amount' => $inv->subtotal,
                        'method' => '-',
                        'status' => match (true) {
                            $inv->status == 1 && Carbon::parse($inv->invoice_date)->startOfDay() < Carbon::today() => 'Overdue',
                            default => 'Due',
                        },
                        'txn' => '-',
                        'note' => $inv->note ?? '-',
                    ];
                })
            )->sortByDesc('id')->values();

            return DataTables::of($combined)
                ->addIndexColumn()
                ->editColumn('amount', fn($row) => '£' . number_format($row->amount, 2))
                ->editColumn('status', function($row) {
                    $statusBadge = match ($row->status) {
                        'Received'   => '<span class="badge bg-success">Received</span>',
                        'Overdue'    => '<span class="badge bg-danger">Overdue</span>',
                        'Due'        => '<span class="badge bg-warning">Due</span>',
                        'Receivable' => '<span class="badge bg-warning">Receivable</span>',
                        default      => '<span class="badge bg-secondary">' . $row->status . '</span>'
                    };

                    $invoiceBadge = '<br><a href="' . route("transaction.invoice", $row->id) . '" class="badge bg-primary text-white mt-1" style="text-decoration:none;">Invoice</a>';

                    return $statusBadge . $invoiceBadge;
                })
                ->addColumn('txn', fn($row) => $row->status === 'Received' ? ($row->txn ?? '-') : '-')
                ->rawColumns(['status', 'project'])
                ->make(true);
        }

        $clients = Client::latest()->get();
        $clientProjects = ClientProject::latest()->get();
        $services = ProjectService::latest()->get();
        return view('admin.transactions.index', compact('clients', 'clientProjects', 'services'));
    }

    public function transactionInvoice($id)
    {
        $transaction = Transaction::with('invoice')->findOrFail($id);

        if ($transaction->invoice) {
            $invoice = Invoice::with(['client', 'details'])->findOrFail($transaction->invoice->id);
            return view('admin.invoices.show', compact('invoice'));
        }

        return redirect()->route('project-services.invoice.show', [
            'service_ids' => $transaction->project_service_detail_id
        ]);
    }

}
