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

            // Invoice Receivables
            $invoiceReceivables = Transaction::with('invoice.details.clientProject', 'invoice.client')
                ->whereHas('invoice')
                ->where('transaction_type', 'Due')
                ->whereDoesntHave('invoice.transactions', function ($q) {
                    $q->where('transaction_type', 'Received');
                })
                ->get();

            // Invoice Received
            $invoiceReceived = Transaction::with('invoice.details.clientProject', 'invoice.client')
                ->whereHas('invoice')
                ->where('transaction_type', 'Received')
                ->get();

            // Service Receivables
            $serviceReceivables = Transaction::with(['projectServiceDetail.project', 'projectServiceDetail.serviceType', 'projectServiceDetail.client'])
                ->where('transaction_type', 'Due')
                ->whereHas('projectServiceDetail')
                ->whereDoesntHave('projectServiceDetail.transactions', function ($q) {
                    $q->where('transaction_type', 'Received');
                })
                ->get();

            // Service Received
            $serviceReceived = Transaction::with(['projectServiceDetail.project', 'projectServiceDetail.serviceType', 'projectServiceDetail.client'])
                ->where('transaction_type', 'Received')
                ->whereHas('projectServiceDetail')
                ->get();

            $allTransactions = $invoiceReceivables
                ->merge($invoiceReceived)
                ->merge($serviceReceivables)
                ->merge($serviceReceived)
                ->sortByDesc('id')
                ->values();

            if ($request->client_id) {
                $allTransactions = $allTransactions->filter(function ($transaction) use ($request) {
                    return $transaction->client_id == $request->client_id;
                })->values();
            }

            if ($request->status == 'Due') {
                $allTransactions = $allTransactions->filter(function ($transaction) {
                    return $transaction->transaction_type === 'Due';
                })->values();
            }

            if ($request->status == 'Received') {
                $allTransactions = $allTransactions->filter(function ($transaction) {
                    return $transaction->transaction_type === 'Received';
                })->values();
            }

            if ($request->project_id) {
                $allTransactions = $allTransactions->filter(function ($transaction) use ($request) {
                    if (!empty($transaction->invoice)) {
                        return $transaction->invoice->details->contains(function ($detail) use ($request) {
                            return $detail->client_project_id == $request->project_id;
                        });
                    }

                    if (!empty($transaction->projectServiceDetail)) {
                        return $transaction->projectServiceDetail->client_project_id == $request->project_id;
                    }

                    return false;
                })->values();
            }

            $data = $allTransactions->map(function ($row, $key) {
                $isInvoice = !empty($row->invoice);
                $clientName = $isInvoice ? $row->invoice->client?->business_name ?? '-' : $row->projectServiceDetail?->client?->business_name ?? '-';
                $invoiceNo = $isInvoice ? $row->invoice->invoice_number : '-';
                $project = $isInvoice
                    ? $row->invoice->details->pluck('project_name')->implode('<br>')
                    : $row->projectServiceDetail?->project?->title ?? '-';
                $service = $isInvoice ? '-' : $row->projectServiceDetail?->serviceType?->name ?? '-';
                $duration = $isInvoice ? '-' 
                    : ($row->projectServiceDetail?->start_date && $row->projectServiceDetail?->end_date
                        ? Carbon::parse($row->projectServiceDetail->start_date)->format('d-m-Y') . ' to ' . Carbon::parse($row->projectServiceDetail->end_date)->format('d-m-Y')
                        : '-');
                $paymentDate = $row->transaction_type === 'Received' ? Carbon::parse($row->date)->format('d-m-Y') : '-';
                $method = $row->transaction_type === 'Received' ? $row->payment_type : '-';

                $status = 'Due';
                if ($row->transaction_type === 'Received') {
                    $status = 'Received';
                } elseif ($row->transaction_type === 'Due' && Carbon::parse($row->date)->startOfDay() < Carbon::today()) {
                    $status = 'Overdue';
                }

                $statusBadge = match($status) {
                    'Received' => '<span class="badge bg-success">Received</span>',
                    'Overdue'  => '<span class="badge bg-danger">Overdue</span>',
                    'Due'      => '<span class="badge bg-warning">Due</span>',
                    default    => '<span class="badge bg-secondary">' . $status . '</span>',
                };

                $invoiceLink = '<br><a href="' . route("transaction.invoice", $row->id) . '" target="_blank" class="badge bg-primary text-white mt-1" style="text-decoration:none;">Invoice</a>' ;

                return [
                    'client_name' => $clientName,
                    'invoice_no'  => $invoiceNo,
                    'project'     => $project,
                    'service'     => $service,
                    'duration'    => $duration,
                    'payment_date'=> $paymentDate,
                    'amount'      => '£' . number_format($row->amount, 0),
                    'method'      => $method,
                    'status'      => $statusBadge . $invoiceLink,
                    'txn'         => $row->tran_id ?? '-',
                    'note'        => $row->transaction_type === 'Received' ? $row->description : '-',
                ];
            });

            return DataTables::of($data)
                ->rawColumns(['status', 'project'])
                ->make(true);
        }

        return view('admin.transactions.index');
    }

    public function transactionInvoice($id)
    {
        $transaction = Transaction::with('invoice')->findOrFail($id);

        if ($transaction->invoice_id !== null) {
            $invoice = Invoice::with(['client', 'details'])->findOrFail($transaction->invoice->id);
            return view('admin.invoices.show', compact('invoice'));
        }

        return redirect()->route('project-services.invoice.show', [
            'service_ids' => $transaction->project_service_detail_id
        ]);
    }

}
