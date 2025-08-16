<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FinancialStatementController extends Controller
{
    public function incomeStatement()
    {
        return view('admin.income_statement.search');
    }
    
    public function incomeStatementReport(Request $request)
    {
        return view('admin.income_statement.index');
    }

    public function balanceSheet()
    {
        return view('admin.balance_sheet.search');
    }

    public function balanceSheetReport()
    {
        return view('admin.balance_sheet.index');
    }
}