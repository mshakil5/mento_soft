<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChartOfAccount;
use App\Models\Transaction;
use App\Models\CompanyDetails;
use App\Models\User;
use App\Models\Client;
use App\Models\ProjectService;
use App\Models\ClientProject;
use App\Models\ProjectServiceDetail;

class LedgerController extends Controller
{
    public function showLedgerAccounts()
    {
        $chartOfAccounts = ChartOfAccount::select('id', 'account_head', 'account_name','status')->where('status', 1)
        ->get();
        $employees = User::where('user_type', 1)->where('status', 1)->latest()->get();
        $clients = Client::where('status', 1)->latest()->get();
        $projects = ClientProject::latest()->get();
        $services = Transaction::query()
          ->whereNotNull('project_service_detail_id')
          ->with(['projectServiceDetail.serviceType'])
          ->get()
          ->groupBy(fn($txn) => $txn->projectServiceDetail->serviceType->id ?? 0);
        return view('admin.ledger.index', compact('chartOfAccounts', 'employees', 'clients', 'projects', 'services'));
    }

    public function income($id, Request $request)
    {
        $data = Transaction::where('chart_of_account_id', $id)->whereIn('transaction_type', ['Current', 'Advance Adjust', 'Refund'])->get();
        $totalDrAmount = Transaction::where('chart_of_account_id', $id)->whereIn('transaction_type', ['Refund'])->sum('at_amount');
        $totalCrAmount = Transaction::where('chart_of_account_id', $id)->whereIn('transaction_type', ['Current', 'Advance Adjust'])->sum('at_amount');
        $balance =  $totalCrAmount - $totalDrAmount;
        $accountName = ChartOfAccount::where('id', $id)->first()->account_name;
        $companyName = CompanyDetails::select('company_name')->first()->company_name;
        return view('admin.ledger.income', compact('data', 'balance','accountName','companyName'));
    }

    public function asset($id, Request $request)
    {
        $data = Transaction::where('chart_of_account_id', $id)->get();
        $totalDrAmount = Transaction::where('chart_of_account_id', $id)->whereIn('transaction_type', ['Purchase', 'Payment'])->sum('at_amount');
        $totalCrAmount = Transaction::where('chart_of_account_id', $id)->whereIn('transaction_type', ['Sold', 'Deprication', 'Received'])->sum('at_amount');
        $balance = $totalDrAmount - $totalCrAmount;
        $accountName = ChartOfAccount::where('id', $id)->first()->account_name;
        $companyName = CompanyDetails::select('company_name')->first()->company_name;
        return view('admin.ledger.asset', compact('data', 'balance','accountName','companyName'));
    }

    public function expense($id, Request $request)
    {
        $data = Transaction::where('chart_of_account_id', $id)->whereIn('transaction_type', ['Current', 'Prepaid', 'Due Adjust'])->get();
        $totalDrAmount = Transaction::where('chart_of_account_id', $id)->whereIn('transaction_type', ['Current', 'Prepaid', 'Due Adjust'])->sum('at_amount');
        $balance = $totalDrAmount;
        $accountName = ChartOfAccount::where('id', $id)->first()->account_name;
        $companyName = CompanyDetails::select('company_name')->first()->company_name;
        return view('admin.ledger.expense', compact('data', 'balance','accountName','companyName'));
    }

    public function liability($id, Request $request)
    {
        $data = Transaction::where('chart_of_account_id', $id)->get();
        $totalDrAmount = Transaction::where('chart_of_account_id', $id)->whereIn('transaction_type', ['Received'])->sum('at_amount');
        $totalCrAmount = Transaction::where('chart_of_account_id', $id)->whereIn('transaction_type', ['Payment'])->sum('at_amount');
        $balance = $totalDrAmount - $totalCrAmount;
        $accountName = ChartOfAccount::where('id', $id)->first()->account_name;
        $companyName = CompanyDetails::select('company_name')->first()->company_name;
        return view('admin.ledger.liability', compact('data', 'balance','accountName','companyName'));
    }

    public function equity($id, Request $request)
    {
        $data = Transaction::where('chart_of_account_id', $id)->get();
        $totalDrAmount = Transaction::where('chart_of_account_id', $id)->whereIn('transaction_type', ['Payment'])->sum('at_amount');
        $totalCrAmount = Transaction::where('chart_of_account_id', $id)->whereIn('transaction_type', ['Received'])->sum('at_amount');
        $balance =  $totalCrAmount - $totalDrAmount;
        $accountName = ChartOfAccount::where('id', $id)->first()->account_name;
        $companyName = CompanyDetails::select('company_name')->first()->company_name;
        return view('admin.ledger.equity', compact('data', 'balance','accountName','companyName'));
    }

    public function client($id, Request $request)
    {
        $data = Transaction::where('client_id', $id)
            ->orderBy('date', 'asc')
            ->get();

        $client = Client::find($id);
        $clientName = $client?->name ?? 'Client Not Found';
        $companyName = CompanyDetails::select('company_name')->first()->company_name ?? '';

        return view('admin.ledger.client', compact('data', 'clientName', 'companyName'));
    }

    public function employee($id, Request $request)
    {
        $data = Transaction::where('employee_id', $id)
                ->whereIn('transaction_type', ['Current', 'Prepaid', 'Due Adjust', 'Received', 'Payment'])
                ->select([
                    'id', 
                    'chart_of_account_id', 
                    'tran_id', 
                    'date', 
                    'description', 
                    'payment_type', 
                    'ref', 
                    'transaction_type', 
                    'at_amount'
                ])
                ->where('table_type', 'Expenses')
                ->orderBy('id', 'asc')
                ->get();

        $employee = User::find($id);
        $employeeName = $employee?->name ?? 'Employee Not Found';
        $companyName = CompanyDetails::select('company_name')->first()->company_name ?? '';

        $assets = Transaction::where('employee_id', $id)
                ->whereIn('transaction_type', ['Current', 'Prepaid', 'Due Adjust', 'Received', 'Payment'])
                ->select([
                    'id', 
                    'chart_of_account_id', 
                    'tran_id', 
                    'date', 
                    'description', 
                    'payment_type', 
                    'ref', 
                    'transaction_type', 
                    'at_amount'
                ])
                ->where('table_type', 'Assets')
                ->orderBy('id', 'DESC')
                ->get();

                $totalPaidLoanAmout = $assets->whereIn('transaction_type', ['Payment'])->sum('at_amount');
                $totalRcvLoanAmout = $assets->whereIn('transaction_type', ['Received'])->sum('at_amount');
                $loanBalance = $totalPaidLoanAmout - $totalRcvLoanAmout;

                // dd($loanBalance);

        return view('admin.ledger.employee', compact('data', 'employeeName', 'companyName','assets','loanBalance'));
    }

    public function project($id, Request $request)
    {
        $data = Transaction::where('client_project_id', $id)
            ->orderBy('date', 'asc')
            ->get();

        $project = ClientProject::find($id);
        $projectName = $project?->title ?? 'Project Not Found';
        $clientName = $project?->client?->name ?? 'Client Not Found';

        $companyName = CompanyDetails::select('company_name')->first()->company_name ?? '';

        return view('admin.ledger.project', compact('data', 'projectName', 'clientName', 'companyName'));
    }

    public function service($serviceId)
    {
        $transactions = Transaction::whereHas('projectServiceDetail', function($q) use ($serviceId) {
            $q->where('project_service_id', $serviceId);
        })
        ->with(['projectServiceDetail.serviceType', 'projectServiceDetail.project', 'projectServiceDetail.client'])
        ->orderBy('date', 'asc')
        ->get();

        $service = ProjectService::find($serviceId);
        if (!$service) {
            abort(404, 'Service not found');
        }

        $serviceName = $service->name ?? 'Service';
        $companyName = CompanyDetails::select('company_name')->first()->company_name ?? '';

        return view('admin.ledger.service', compact('transactions', 'serviceName', 'companyName'));
    }

}
