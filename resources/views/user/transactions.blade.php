@extends('user.master')

@section('user-content')
<style>
  .custom-table-bg th,
  .custom-table-bg td,
  .custom-table-bg tbody {
      background-color: transparent !important;
  }
</style>
<div class="row px-2">
    <div class="col-12">

        <div class="card text-light shadow-sm mb-4 form-style fadeInUp border-light">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle custom-table-bg">
                        <thead>
                            <tr>
                                <th class="text-light">Project</th>
                                <th class="text-light">Service</th>
                                <th class="text-light">Duration</th>
                                <th class="text-light">Payment Date</th>
                                <th class="text-light">Amount</th>
                                <th class="text-light">Method</th>
                                <th class="text-light">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($combined as $row)
                                <tr>
                                    <td class="text-light">{!! $row['project'] !!}</td>
                                    <td class="text-light">{{ $row['service'] }}</td>
                                    <td class="text-light">{{ $row['duration'] }}</td>
                                    <td class="text-light">{{ $row['payment_date'] }}</td>
                                    <td class="text-light">£{{ number_format($row['amount'], 2) }}</td>
                                    <td class="text-light">{{ $row['method'] }}</td>
                                    <td class="text-light">
                                        @php
                                            $statusClasses = [
                                                'Received' => 'bg-success',
                                                'Overdue'  => 'bg-danger',
                                                'Due'      => 'bg-warning',
                                                'Receivable'=> 'bg-warning',
                                            ];
                                        @endphp
                                        <span class="badge {{ $statusClasses[$row['status']] ?? 'bg-secondary' }}">
                                            {{ $row['status'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-3 text-light">No transactions found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="mt-3">
            {{ $combined->links('pagination::bootstrap-5') }}
        </div>

    </div>
</div>
@endsection