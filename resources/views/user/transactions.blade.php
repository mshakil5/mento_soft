@extends('user.master')

@section('user-content')

<div class="row px-2">
    <div class="col-12">

        <div class="card text-light shadow-sm mb-4 form-style border-light">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle" id="transactionsTable">
                        <thead>
                            <tr>
                                <th>Invoice No.</th>
                                <th>Project</th>
                                <th>Service</th>
                                <th>Duration</th>
                                <th>Payment Date</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Txn</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('script')
<script>
$(function () {
    $('#transactionsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('user.transactions') }}",
        columns: [
            { data: 'invoice_no', name: 'invoice_no' },
            { data: 'project', name: 'project' },
            { data: 'service', name: 'service' },
            { data: 'duration', name: 'duration' },
            { data: 'payment_date', name: 'payment_date' },
            { data: 'amount', name: 'amount', orderable: false, searchable: false },
            { data: 'method', name: 'method' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'txn', name: 'txn' },
            { data: 'note', name: 'note', orderable: false, searchable: false },
        ],
        responsive: true,
        lengthChange: false,
        autoWidth: false,
    });
});
</script>
@endsection
