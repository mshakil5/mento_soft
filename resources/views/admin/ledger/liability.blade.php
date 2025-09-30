@extends('admin.master')

@section('content')

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="page-header"><a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a></div>
        <div class="row justify-content-md-center">
            <div class="col-md-10">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">
                            @if ($accountName)
                                <h4>{{ $accountName }} Ledger</h4>
                            @else
                                <h4>Account Name Not Found</h4>
                            @endif
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="alert-container"></div>

                        
                        <div class="text-center mb-4 company-name-container">
                            <h2>{{ $companyName }}</h2>
                        
                            @if ($accountName)
                                <h4>{{ $accountName }} Ledger</h4>
                            @else
                                <h4>Account Name Not Found</h4>
                            @endif
                        </div>

                        <div class="table-responsive">
                            <table id="dataTransactionsTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Txn</th>
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th>Payment Type</th>
                                        <th>Ref</th>
                                        <th>Transaction Type</th>
                                        <th>Debit</th>
                                        <th>Credit</th>
                                        <th>Balance</th>                                
                                    </tr>
                                </thead>
                                <tbody>
        
                                    @foreach($data as $data)
                                        <tr>
                                            <td>{{ $data->tran_id }}</td>
                                            <td>{{ \Carbon\Carbon::parse($data->date)->format('d-m-Y') }}</td>
                                            <td>{{ $data->description }}</td>
                                            <td>{{ $data->payment_type }}</td>
                                            <td>{{ $data->ref }}</td>
                                            <td>{{ $data->transaction_type }}</td>  
                                            @if(in_array($data->transaction_type, ['Received']))
                                            <td>{{ number_format($data->at_amount, 0) }}</td>
                                            <td></td>
                                            <td>{{ $balance }}</td>
                                            @php
                                                $balance = $balance - $data->at_amount;
                                            @endphp
                                            @elseif(in_array($data->transaction_type, ['Payment']))
                                            <td></td>
                                            <td>{{ number_format($data->at_amount, 0) }}</td>
                                            <td>{{ $balance }}</td>
                                            @php
                                                $balance = $balance + $data->at_amount;
                                            @endphp
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection