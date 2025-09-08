@extends('admin.master')

@section('content')
<section class="content pt-3">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Sent Emails</h3>
                    </div>
                    <div class="card-body">
                        <table id="emailsTable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Recipient</th>
                                    <th>Subject</th>
                                    <th>Body</th>
                                    <th>Attachment</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="card-footer text-left">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
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
    $('#emailsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('client.email.logs', ['client_id' => request('client_id')]) }}",
            type: 'GET',
            error: function (xhr) { console.error(xhr.responseText); },
        },
        columns: [
            { data: 'created_at', name: 'created_at' },
            { data: 'recipient', name: 'recipient' },
            { data: 'subject', name: 'subject' },
            { data: 'body', name: 'body' },
            { data: 'attachment', name: 'attachment', orderable: false, searchable: false },
        ],
        order: [[0, 'desc']],
        responsive: true,
        lengthChange: false,
        autoWidth: false,
    });
});
</script>
@endsection