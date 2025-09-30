<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChartOfAccount;
use App\Models\Transaction;
use App\Models\CompanyDetails;

class LedgerController extends Controller
{
    public function showLedgerAccounts()
    {
        $chartOfAccounts = ChartOfAccount::select('id', 'account_head', 'account_name','status')->where('status', 1)
        ->get();
        return view('admin.ledger.index', compact('chartOfAccounts'));
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

}
