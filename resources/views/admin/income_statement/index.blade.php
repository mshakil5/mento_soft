@extends('admin.master')

@section('content')
<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
      <div class="row">
          <div class="col-md-12">
              <div class="card">
                  <div>
                      <div class="row no-print">
                          <div class="col-12">
                              <button onclick="window.print()" class="fa fa-print btn btn-default float-right">Print</button>
                          </div>
                      </div>
                      
                      <div class="text-center">
                          <h1>Branch: [Branch Name]</h1>
                          <h5>Phone: [Branch Phone]</h5>
                          <h5 style="text-align: center">Balance Sheet</h5>
                          <p>[Branch Address]</p>
                          <h3 style='margin-top:-15px;'>As at [Date]</h3>
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
                                          <th>Amount</th>
                                          <th>Amount</th>
                                          <th>Amount</th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                      <tr>
                                          <th>(1) Fixed Asset</th>
                                          <th></th>
                                          <th></th>
                                          <th></th>
                                      </tr>
                                      
                                      <tr>
                                          <td style="padding-left: 70px;">[Asset Name]</td>
                                          <td></td>
                                          <td style="text-align: right">[Amount]</td>
                                          <td></td>
                                      </tr>
                                      
                                      <tr>
                                          <th>Net Fixed Asset</th>
                                          <th></th>
                                          <th></th>
                                          <th style="text-align: right">[Total Fixed Asset]</th>
                                      </tr>
                                      
                                      <tr>
                                          <th>(2) Current Asset</th>
                                          <th></th>
                                          <th></th>
                                          <th></th>
                                      </tr>
                                      
                                      <tr>
                                          <td style="padding-left: 70px;">Cash in Hand</td>
                                          <td style="text-align: right">[Amount]</td>
                                          <td></td>
                                          <td></td>
                                      </tr>
                                      
                                      <tr>
                                          <td style="padding-left: 70px;">Cash at Bank</td>
                                          <td style="text-align: right">[Amount]</td>
                                          <td></td>
                                          <td></td>
                                      </tr>
                                      
                                      <tr>
                                          <td style="padding-left: 70px;">[Current Asset Name]</td>
                                          <td style="text-align: right">[Amount]</td>
                                          <td></td>
                                          <td></td>
                                      </tr>
                                      
                                      <tr>
                                          <td style="padding-left: 50px;">Account Receivables</td>
                                          <td style="text-align: right">[Amount]</td>
                                          <td></td>
                                          <td></td>
                                      </tr>
                                      
                                      <tr>
                                          <th>Total Current Asset</th>
                                          <th></th>
                                          <th style="text-align: right">[Total Current Asset]</th>
                                          <th></th>
                                      </tr>
                                      
                                      <tr>
                                          <th>(3) Short Term Liabilities</th>
                                          <th></th>
                                          <th></th>
                                          <th></th>
                                      </tr>
                                      
                                      <tr>
                                          <td style="padding-left: 50px;">Accounts Payable</td>
                                          <td style="text-align: right">[Amount]</td>
                                          <td></td>
                                          <td></td>
                                      </tr>
                                      
                                      <tr>
                                          <td style="padding-left: 50px;">Dividend Payable</td>
                                          <td style="text-align: right">[Amount]</td>
                                          <td></td>
                                          <td></td>
                                      </tr>
                                      
                                      <tr>
                                          <td style="padding-left: 50px;">Employee Payable</td>
                                          <td style="text-align: right">[Amount]</td>
                                          <td></td>
                                          <td></td>
                                      </tr>
                                      
                                      <tr>
                                          <td style="padding-left: 50px;">Vat Payable</td>
                                          <td style="text-align: right">[Amount]</td>
                                          <td></td>
                                          <td></td>
                                      </tr>
                                      
                                      <tr>
                                          <td style="padding-left: 50px;">Tax Payable</td>
                                          <td style="text-align: right">[Amount]</td>
                                          <td></td>
                                          <td></td>
                                      </tr>
                                      
                                      <tr>
                                          <th>Total Current Liabilities</th>
                                          <th></th>
                                          <th style="text-align: right">[Total Current Liabilities]</th>
                                          <th></th>
                                      </tr>
                                      
                                      <tr>
                                          <th>(4) Net Current Assets:(2 less3 =)</th>
                                          <th></th>
                                          <th></th>
                                          <th style="text-align: right">[Net Current Assets]</th>
                                      </tr>
                                      
                                      <tr>
                                          <th>(5) Gross Assets- Sub Total( 1 + 4 =)</th>
                                          <th></th>
                                          <th></th>
                                          <th style="text-align: right">[Gross Assets]</th>
                                      </tr>
                                      
                                      <tr>
                                          <th>(6) Long Term Liabilities</th>
                                          <th></th>
                                          <th></th>
                                          <th></th>
                                      </tr>
                                      
                                      <tr>
                                          <td style="padding-left: 70px;">[Liability Name]</td>
                                          <td style="text-align: right">[Amount]</td>
                                          <td></td>
                                          <td></td>
                                      </tr>
                                      
                                      <tr>
                                          <td>Total Long-term Liabilities</td>
                                          <td></td>
                                          <td style="text-align: right">[Total Long Term Liabilities]</td>
                                          <td></td>
                                      </tr>
                                      
                                      <tr>
                                          <th>Net Assets (5 LESS 6)</th>
                                          <th></th>
                                          <th></th>
                                          <th style="text-align: right">[Net Assets]</th>
                                      </tr>
                                      
                                      <tr>
                                          <th>Stockholders Equity</th>
                                          <th></th>
                                          <th></th>
                                          <th></th>
                                      </tr>
                                      
                                      <tr>
                                          <td style="padding-left: 70px;">Equity Capital</td>
                                          <td></td>
                                          <td style="text-align: right">[Amount]</td>
                                          <td></td>
                                      </tr>
                                      
                                      <tr>
                                          <td style="padding-left: 50px;">Add: Share Premium</td>
                                          <td></td>
                                          <td style="text-align: right">[Amount]</td>
                                          <td></td>
                                      </tr>
                                      
                                      <tr>
                                          <td style="padding-left: 50px;">Retained Earning</td>
                                          <td style="text-align: right">[Amount]</td>
                                          <td></td>
                                          <td></td>
                                      </tr>
                                      
                                      <tr>
                                          <td style="padding-left: 70px;">Reserve Fund</td>
                                          <td style="text-align: right">[Amount]</td>
                                          <td></td>
                                          <td></td>
                                      </tr>
                                      
                                      <tr>
                                          <td></td>
                                          <td></td>
                                          <td style="text-align: right">[Amount]</td>
                                          <td></td>
                                      </tr>
                                      
                                      <tr>
                                          <td style="padding-left: 70px;">Withdraw</td>
                                          <td></td>
                                          <td style="text-align: right">[Amount]</td>
                                          <td></td>
                                      </tr>
                                      
                                      <tr>
                                          <th>Total Stockholders Equity</th>
                                          <th></th>
                                          <th></th>
                                          <th style="text-align: right">[Total Stockholders Equity]</th>
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
  </div>
</section>
@endsection