@extends('admin.master')

@section('content')

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="page-header">
            <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
        </div>

        <div class="row justify-content-md-center">
            <div class="col-md-10">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">
                            {{ $clientName }} Ledger
                        </h3>
                    </div>
                    <div class="card-body">

                        <div class="text-center mb-4">
                            <h2>{{ $companyName }}</h2>
                            <h4>{{ $clientName }} Ledger</h4>
                        </div>

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
                                @php $runningBalance = 0; @endphp
                                @foreach($data as $txn)
                                    <tr>
                                        <td>{{ $txn->tran_id }}</td>
                                        <td>{{ \Carbon\Carbon::parse($txn->date)->format('d-m-Y') }}</td>
                                        <td>{!! $txn->description !!}</td>
                                        <td>{{ $txn->payment_type }}</td>
                                        <td>{{ $txn->ref }}</td>
                                        <td>{{ $txn->transaction_type }}</td>

                                        @if($txn->transaction_type == 'Due')
                                            <td>{{ number_format($txn->at_amount, 0) }}</td>
                                            <td></td>
                                            @php $runningBalance -= $txn->at_amount; @endphp
                                        @elseif($txn->transaction_type == 'Received')
                                            <td></td>
                                            <td>{{ number_format($txn->at_amount, 0) }}</td>
                                            @php $runningBalance += $txn->at_amount; @endphp
                                        @endif

                                        <td>{{ number_format($runningBalance, 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection