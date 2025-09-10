@extends('user.master')

@section('user-content')
<style>
  .custom-table-bg th,
  .custom-table-bg td,
  .custom-table-bg tbody {
      background-color: transparent !important;
  }
  .modal-body table {
      width: 100%;
  }
</style>

<div class="row px-2">
    <div class="col-12">
        <div class="card text-light shadow-sm mb-4 form-style fadeInUp border-light">
            <form method="GET" action="{{ route('user.services') }}" class="row mb-3">
                <div class="col-3">
                    <select name="project" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Select Project --</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ request('project') == $project->id ? 'selected' : '' }}>
                                {{ $project->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>

            <div class="table-responsive my-2">
                <table class="table mb-0 align-middle custom-table-bg">
                    <thead>
                        <tr>
                            <th class="text-light">Service</th>      
                            <th class="text-light">Project</th>
                            <th class="text-light">Due Date</th>
                            <th class="text-center text-light">Amount (£)</th>
                            <th class="text-center text-light">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $service)
                            @php $modalId = 'serviceModal_' . $service->id; @endphp
                            <tr>
                                <td class="text-light">{{ $service->serviceType?->name ?? '-' }}</td>
                                <td class="text-light">{{ $service->project?->title ?? '-' }}</td>
                                <td class="text-light">
                                    @if($service->start_date)
                                        @php
                                            $date = \Carbon\Carbon::parse($service->start_date);

                                            if ($service->cycle_type == 1) {
                                                $date->addMonthNoOverflow();
                                            } elseif ($service->cycle_type == 2) {
                                                $date->addYear();
                                            } else {
                                                $date->addDay();
                                            }
                                        @endphp
                                        {{ $date->format('d-m-Y') }}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="text-center text-light">
                                    @if($service->type == 1)
                                        @php
                                            $unpaidBills = \App\Models\ProjectServiceDetail::where('project_service_id', $service->project_service_id)
                                                ->where('client_id', $service->client_id)
                                                ->where('client_project_id', $service->client_project_id)
                                                ->where('type', 1)
                                                ->where('bill_paid', '!=', 1)
                                                ->where('amount', $service->amount)
                                                ->where('cycle_type', $service->cycle_type)
                                                ->where('is_auto', $service->is_auto)
                                                ->get();
                                            $totalAmount = $unpaidBills->sum('amount');
                                            $count = $unpaidBills->count();
                                            $cycleText = $service->cycle_type == 1 ? 'month' : 'year';
                                        @endphp
                                        £{{ number_format($service->amount,0) }} x {{ $count }} {{ $cycleText }} = £{{ number_format($totalAmount,0) }}
                                    @else
                                        £{{ number_format($service->amount,0) }}
                                    @endif
                                </td>
                                <td class="text-center text-light">
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#{{ $modalId }}">
                                        View
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-light">No services found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Move all modals here, outside table -->
            @foreach($services as $service)
                @php $modalId = 'serviceModal_' . $service->id; @endphp
                <div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header bg-info text-white">
                                <h5 class="modal-title">{{ $service->project?->title ?? '-' }} - {{ $service->serviceType?->name }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                @php
                                    $bills = \App\Models\ProjectServiceDetail::with(['transaction'=>fn($q)=>$q->where('transaction_type','Received'),'client','serviceType','project'])
                                        ->where('project_service_id', $service->project_service_id)
                                        ->where('client_id', $service->client_id)
                                        ->where('client_project_id', $service->client_project_id)
                                        ->where('amount', $service->amount)
                                        ->where('cycle_type', $service->cycle_type)
                                        ->where('is_auto', $service->is_auto)
                                        ->latest()
                                        ->get();
                                @endphp
                                <table class="table table-bordered table-hover" style="font-size: 12px;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Service</th>
                                            <th>Duration</th>
                                            <th>Payment Date</th>
                                            <th>Amount</th>
                                            <th>Method</th>
                                            <th>Status</th>
                                            <th>Invoice</th>
                                            <th>Note</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($bills as $index => $bill)
                                            @php
                                                $duration = $bill->start_date && $bill->end_date
                                                    ? \Carbon\Carbon::parse($bill->start_date)->format('d-m-y') . ' to ' . \Carbon\Carbon::parse($bill->end_date)->format('d-m-y')
                                                    : '-';
                                                $paymentDate = $bill->transaction?->date ? \Carbon\Carbon::parse($bill->transaction->date)->format('d-m-y') : '-';
                                                $method = $bill->transaction?->payment_type ?? '-';
                                                $txn = $bill->transaction?->tran_id ?? '-';
                                                $note = $bill->transaction?->description ?? '-';
                                                $status = $bill->bill_paid ? 'Paid' : ($bill->due_date && \Carbon\Carbon::parse($bill->due_date)->lt(now()) ? 'Overdue' : 'Pending');
                                            @endphp
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $bill->serviceType?->name }}</td>
                                                <td>{{ $duration }}</td>
                                                <td>{{ $paymentDate }}</td>
                                                <td>£{{ number_format($bill->amount, 0) }}</td>
                                                <td>{{ $method }}</td>
                                                <td>
                                                    @if($status === 'Paid')
                                                        <span class="badge bg-success">{{ $status }}</span>
                                                    @elseif($status === 'Overdue')
                                                        <span class="badge bg-danger">{{ $status }}</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark">{{ $status }}</span>
                                                    @endif
  
                                                    @if($bill->renewal)
                                                        <br>
                                                        <small class="text-info fst-italic">
                                                            Renewed: {{ \Carbon\Carbon::parse($bill->renewal->date)->format('j F Y') }}
                                                            {{ $bill->renewal->note ? '- ' . $bill->renewal->note : '' }}
                                                        </small>
                                                    @endif
                                                </td>
                                                <td>
                                                  <a href="{{ route('invoice.download', $bill->id) }}" class="btn btn-sm btn-secondary" target="_blank">
                                                      Download
                                                  </a>
                                                </td>
                                                <td>{{ $note }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</div>
@endsection