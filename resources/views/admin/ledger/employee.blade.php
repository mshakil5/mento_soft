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
                            @if ($employeeName)
                                <h4>{{ $employeeName }} Ledger</h4>
                            @else
                                <h4>Employee Not Found</h4>
                            @endif
                        </h3>
                    </div>

                    <div class="card-body">
                        <div id="alert-container"></div>

                        <div class="text-center mb-4 company-name-container">
                            <h2>{{ $companyName }}</h2>
                            @if ($employeeName)
                                <h4>{{ $employeeName }} Ledger</h4>
                            @else
                                <h4>Employee Not Found</h4>
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
                                    @php $runningBalance = 0; @endphp
                                    @foreach($data as $txn)
                                        @php
                                            $debit = 0;
                                            $credit = 0;

                                            // Define which types are Debits and which are Credits
                                            // Logic: 'Current' (Salary due), 'Prepaid' (Advance) are usually Debits to the expense
                                            // But if this is a Ledger for the employee's perspective:
                                            if(in_array($txn->transaction_type, ['Current', 'Prepaid', 'Due Adjust'])) {
                                                $credit = $txn->at_amount;
                                                $runningBalance -= $credit; // Subtracting when paid
                                            } elseif(in_array($txn->transaction_type, ['Received', 'Payment'])) {
                                                $debit = $txn->at_amount;
                                                $runningBalance += $debit; // Adding to the employee's balance
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $txn->tran_id }}</td>
                                            <td>{{ \Carbon\Carbon::parse($txn->date)->format('d-m-Y') }}</td>
                                            <td>{{$txn->chartOfAccount->account_name ?? ''}} <br>
                                                {!! $txn->description !!}
                                            </td>
                                            <td>{{ $txn->payment_type }}</td>
                                            <td>{{ $txn->ref }}</td>
                                            <td>
                                                <span class="badge badge-info">{{ $txn->transaction_type }}</span>
                                            </td>
                                            
                                            <td class="text-right text-danger">
                                                {{ $debit > 0 ? number_format($debit, 2) : '-' }}
                                            </td>
                                            
                                            <td class="text-right text-success">
                                                {{ $credit > 0 ? number_format($credit, 2) : '-' }}
                                            </td>

                                            <td class="text-right font-weight-bold">
                                                {{ number_format($runningBalance, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-md-center pt-2">
            <div class="col-md-10">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">
                            @if ($employeeName)
                                <h4>{{ $employeeName }} Ledger</h4>
                            @else
                                <h4>Employee Not Found</h4>
                            @endif
                        </h3>
                    </div>

                    <div class="card-body">
                        <div id="alert-container"></div>

                        <div class="text-center mb-4 company-name-container">
                            <h2>{{ $companyName }}</h2>
                            @if ($employeeName)
                                <h4>{{ $employeeName }} Loan Ledger</h4>
                            @else
                                <h4>Employee Not Found</h4>
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
                                    @php $runningBalance = $loanBalance; @endphp
                                    @foreach($assets as $txn)
                                        @php
                                            $debit = 0;
                                            $credit = 0;
                                            if(in_array($txn->transaction_type, ['Current', 'Prepaid', 'Payment'])) {
                                                $credit = $txn->at_amount;
                                            } elseif(in_array($txn->transaction_type, ['Received'])) {
                                                $debit = $txn->at_amount;
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $txn->tran_id }}</td>
                                            <td>{{ \Carbon\Carbon::parse($txn->date)->format('d-m-Y') }}</td>
                                            <td>{{$txn->chartOfAccount->account_name ?? ''}} <br>
                                                {!! $txn->description !!}
                                            </td>
                                            <td>{{ $txn->payment_type }}</td>
                                            <td>{{ $txn->ref }}</td>
                                            <td>
                                                <span class="badge badge-info">{{ $txn->transaction_type }}</span>
                                            </td>
                                            
                                            <td class="text-right text-danger">
                                                {{ $debit > 0 ? number_format($debit, 2) : '-' }}
                                            </td>
                                            
                                            <td class="text-right text-success">
                                                {{ $credit > 0 ? number_format($credit, 2) : '-' }}
                                            </td>

                                            <td class="text-right font-weight-bold">
                                                {{ number_format($runningBalance, 2) }}
                                            </td>
                                        </tr>

                                        @php
                                            if(in_array($txn->transaction_type, ['Current', 'Prepaid', 'Payment'])) {
                                                $runningBalance -= $credit; 
                                            } elseif(in_array($txn->transaction_type, ['Received'])) {
                                                $runningBalance += $debit;
                                            }
                                        @endphp
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