@extends('admin.master')

@section('content')

<section class="content pt-3">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-10">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Accounts</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Income</th>
                                    <th class="text-center">Assets</th>
                                    <th class="text-center">Expenses</th>
                                    <th class="text-center">Liabilities</th>
                                    <th class="text-center">Equity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                  <td>
                                      @foreach($chartOfAccounts as $income)
                                          @if($income->account_head == 'Income')   
                                              <a href="{{ url('/admin/ledger/income-details/' . $income->id) }}" class="btn btn-block btn-success btn-sm">
                                                  {{ $income->account_name }}
                                              </a>
                                          @endif  
                                      @endforeach
                                  </td>
                                  <td>
                                      @foreach($chartOfAccounts as $asset)
                                          @if($asset->account_head == 'Assets')   
                                              <a href="{{ url('/admin/ledger/asset-details/' . $asset->id) }}" class="btn btn-block btn-info btn-sm">
                                                  {{ $asset->account_name }}
                                              </a>
                                          @endif  
                                      @endforeach
                                  </td>
                                  <td>
                                      @foreach($chartOfAccounts as $expense)
                                          @if($expense->account_head == 'Expenses')   
                                              <a href="{{ url('/admin/ledger/expense-details/' . $expense->id) }}" class="btn btn-block btn-danger btn-sm">
                                                  {{ $expense->account_name }}
                                              </a>
                                          @endif  
                                      @endforeach
                                  </td>
                                  <td>
                                      @foreach($chartOfAccounts as $liability)
                                          @if($liability->account_head == 'Liabilities')   
                                              <a href="{{ url('/admin/ledger/liability-details/' . $liability->id) }}" class="btn btn-block btn-warning btn-sm">
                                                  {{ $liability->account_name }}
                                              </a>
                                          @endif  
                                      @endforeach
                                  </td>
                                  <td>
                                      @foreach($chartOfAccounts as $equity)
                                          @if($equity->account_head == 'Equity')   
                                              <a href="{{ url('/admin/ledger/equity-details/' . $equity->id) }}" class="btn btn-block btn-primary btn-sm">
                                                  {{ $equity->account_name }}
                                              </a>
                                          @endif  
                                      @endforeach
                                  </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="content pt-3">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-10">
                <div class="card card-secondary collapsed-card">
                    <div class="card-header">
                        <h3 class="card-title">Clients & Employees</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Clients</th>
                                    <th class="text-center">Employees</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                  <td>
                                      @foreach($clients as $client)
                                          <a href="{{ url('admin/ledger/client-details/' . $client->id) }}" 
                                             class="btn btn-block btn-success btn-sm">
                                              {{ $client->name }}
                                          </a>
                                      @endforeach
                                  </td>
                                  <td>
                                      @foreach($employees as $employee)
                                          <a href="{{ url('admin/ledger/employee-details/' . $employee->id) }}" 
                                             class="btn btn-block btn-info btn-sm">
                                              {{ $employee->name }}
                                          </a>
                                      @endforeach
                                  </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('script')

@endsection