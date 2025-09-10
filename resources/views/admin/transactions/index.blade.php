@extends('admin.master')

@section('content')
<section class="content pt-3">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">All Transactions</h3>
                    </div>
                    <div class="card-body">
                        <table id="transactionsTable" class="table cell-border table-striped">
                            <thead>
                                <tr>
                                    <th>Client</th>
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
</section>
@endsection

@section('script')
<script>
$(document).ready(function () {
    $('#transactionsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('transactions.index') }}",
            type: "GET",
            error: function (xhr) {
                console.error(xhr.responseText);
            }
        },
        columns: [
            { data: 'client_name', name: 'client_name' },
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