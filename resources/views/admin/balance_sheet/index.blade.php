@extends('admin.master')

@section('content')
<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        
                        <div class="row no-print">
                            <div class="col-12">
                                <button onclick="window.print()" class="fa fa-print btn btn-default float-right">Print</button>
                            </div>
                        </div>

                        <div class="text-center">
                            <h1>Branch: [Branch Name]</h1>
                            <h5>Phone: [Branch Phone]</h5>
                            <h5 style="text-align: center">PROFIT AND LOSE ACCOUNT</h5>
                            <p>[Branch Address]</p>
                            <h3>Data: [Date Range]</h3>                              
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2"></div>
                            <div class="col-md-8">
                                <table class="table table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Particulars</th>
                                            <th style="text-align: center">Amount</th>
                                            <th style="text-align: center">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th>TURN OVER SALES</th>
                                            <th></th>
                                            <th></th>
                                        </tr>

                                        <tr>
                                            <td style="padding-left: 70px;">[Income Name]</td>
                                            <td style="text-align: right">[Amount]</td>
                                            <td></td>
                                        </tr>

                                        <tr>
                                            <th>Total Income</th>
                                            <th style="text-align: right">[Total Income]</th>
                                            <th style="text-align: right"></th>
                                        </tr>

                                        <tr>
                                            <th>Gross Profit</th>
                                            <th style="text-align: right"></th>
                                            <th style="text-align: right">[Gross Profit]</th>
                                        </tr>
                                        
                                        <tr>
                                            <th>EXPENDITURE</th>
                                            <th></th>
                                            <th></th>
                                        </tr>

                                        <tr>
                                            <td style="padding-left: 70px;">[Salary Expense Name]</td>
                                            <td style="text-align: right">[Amount]</td>
                                            <td style="text-align: right"></td>
                                        </tr>

                                        <tr>
                                            <td style="padding-left: 70px;">[Expense Name]</td>
                                            <td style="text-align: right">[Amount]</td>
                                            <td style="text-align: right"></td>
                                        </tr>

                                        <tr>
                                            <td style="padding-left: 70px;">[Adjustment Expense Name]</td>
                                            <td style="text-align: right">[Amount]</td>
                                            <td></td>
                                        </tr>

                                        <tr>
                                            <td style="padding-left: 70px;">Depreciation</td>
                                            <td style="text-align: right">[Amount]</td>
                                            <td></td>
                                        </tr>

                                        <tr>
                                            <td style="padding-left: 70px;">Interest Payable</td>
                                            <td style="text-align: right">[Amount]</td>
                                            <td></td>
                                        </tr>

                                        <tr>
                                            <th>Total Expense</th>
                                            <th></th>
                                            <th style="text-align: right">[Total Expense]</th>
                                        </tr>

                                        <tr>
                                            <th>Profit/Loss Before Vat</th>
                                            <th></th>
                                            <th style="text-align: right">[Amount]</th>
                                        </tr>

                                        <tr>
                                            <th style="padding-left: 70px;">Vat Provision</th>
                                            <th style="text-align: right">[Amount]</th>
                                            <th></th>
                                        </tr>

                                        <tr>
                                            <th style="padding-left: 70px;">Tax Provision</th>
                                            <th style="text-align: right">[Amount]</th>
                                            <th></th>
                                        </tr>

                                        <tr>
                                            <th>Net Profit</th>
                                            <th></th>
                                            <th style="text-align: right">[Amount]</th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-2"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection