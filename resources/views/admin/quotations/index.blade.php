@extends('admin.master')

@section('content')
<!-- Main content -->
<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">All Quotation Requests</h3>
                    </div>
                    <div class="card-body">
                        <table id="quotationsTable" class="table cell-border table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">Quotation Request Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>First Name:</strong> <span id="view-first-name"></span></p>
                        <p><strong>Last Name:</strong> <span id="view-last-name"></span></p>
                        <p><strong>Email:</strong> <span id="view-email"></span></p>
                        <p><strong>Phone:</strong> <span id="view-phone"></span></p>
                        <p><strong>Company:</strong> <span id="view-company"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Website:</strong> <span id="view-website"></span></p>
                        <p><strong>Timeline:</strong> <span id="view-timeline"></span></p>
                        <p><strong>Date:</strong> <span id="view-date"></span></p>
                        <p><strong>Status:</strong> <span id="view-status" class="badge badge-success"></span></p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <p><strong>Project Description:</strong></p>
                        <div class="border p-3" id="view-dream-description"></div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <p><strong>Requested Features:</strong></p>
                        <div class="border p-3" id="view-features"></div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <p><strong>Additional Information:</strong></p>
                        <div class="border p-3" id="view-additional-info"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    $(document).ready(function () {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        
        var url = "{{ route('quotations.index') }}";

        // Initialize DataTable
        var table = $('#quotationsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: url,
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'date', name: 'date'},
                {data: 'full_name', name: 'full_name'},
                {data: 'email', name: 'email'},
                {data: 'phone', name: 'phone'},
                {data: 'status', name: 'status', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            responsive: true,
            lengthChange: false,
            autoWidth: false,
        });

        // View button click
        $("#contentContainer").on('click', '.view', function(){
            var id = $(this).data('id');
            var view_url = "{{ url('admin/quotations') }}/" + id;
            
            $.get(view_url, function(data) {
                $('#view-first-name').text(data.first_name);
                $('#view-last-name').text(data.last_name);
                $('#view-email').text(data.email);
                $('#view-phone').text(data.phone || '');
                $('#view-company').text(data.company || '');
                $('#view-business-type').text(data.business_type || '');
                $('#view-website').text(data.website || '');
                $('#view-timeline').text(data.timeline || '');
                $('#view-dream-description').text(data.dream_description || '');
                $('#view-additional-info').text(data.additional_info || '');
                $('#view-date').text(data.formatted_created_at);
                
                // Format features array
                if(data.features && data.features.length > 0) {
                    var featuresHtml = '';
                    try {
                        var featuresArray = typeof data.features === 'string' ? JSON.parse(data.features) : data.features;
                        featuresHtml = featuresArray.map(feature => '- ' + feature).join('<br>');
                    } catch(e) {
                        featuresHtml = 'Invalid features format';
                    }
                    $('#view-features').html(featuresHtml);
                } else {
                    $('#view-features').text('');
                }
                
                // Set status badge
                var statusBadge = $('#view-status');
                statusBadge.text(data.status ? 'Read' : 'Unread');
                statusBadge.removeClass('badge-success badge-warning');
                statusBadge.addClass(data.status ? 'badge-success' : 'badge-warning');
                
                $('#viewModal').modal('show');
            });
        });

        // Status toggle
        $(document).on('change', '.toggle-status', function() {
            var quotation_id = $(this).data('id');
            var status = $(this).prop('checked') ? 1 : 0;

            $.ajax({
                url: "{{ route('quotations.status') }}",
                method: "POST",
                data: {
                    quotation_id: quotation_id,
                    status: status,
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    toastr.success(res.message);
                    table.ajax.reload(null, false);
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    toastr.error('Failed to update status');
                    // Revert the toggle if failed
                    $(this).prop('checked', !status);
                }.bind(this)
            });
        });

        // Delete button
        $("#contentContainer").on('click', '.delete', function(){
            if(!confirm('Are you sure you want to delete this quotation request?')) return;
            var id = $(this).data('id');
            
            $.ajax({
                url: "{{ url('admin/quotations') }}/" + id,
                method: "DELETE",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    toastr.success(res.message);
                    table.ajax.reload(null, false);
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    if (xhr.responseJSON && xhr.responseJSON.message)
                        toastr.error(xhr.responseJSON.message);
                    else
                        toastr.error('Failed to delete quotation');
                }
            });
        });
    });
</script>
@endsection