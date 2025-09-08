@extends('admin.master')

@section('content')
<!-- Main content -->
<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">All Contact Messages</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table cell-border table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Subject</th>
                                    <th>Product</th>
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
                <h5 class="modal-title" id="viewModalLabel">Contact Message Details</h5>
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
                    </div>
                    <div class="col-md-6">
                        <p><strong>Subject:</strong> <span id="view-subject"></span></p>
                        <p><strong>Date:</strong> <span id="view-date"></span></p>
                        <p><strong>Status:</strong> <span id="view-status" class="badge badge-success"></span></p>
                        <p><strong>Product:</strong> <span id="product"></span></p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <p><strong>Message:</strong></p>
                        <div class="border p-3" id="view-message"></div>
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
        
        var url = "{{URL::to('/admin/contacts')}}";

        // View button click
        $("#contentContainer").on('click','.view', function(){
            var id = $(this).data('id');
            var view_url = url + '/' + id;
            
            $.get(view_url, function(data) {
                $('#view-first-name').text(data.first_name);
                $('#view-last-name').text(data.last_name);
                $('#view-email').text(data.email);
                $('#view-phone').text(data.phone || '');
                $('#view-subject').text(data.subject);
                $('#view-message').text(data.message);
                $('#view-date').text(data.formatted_created_at);
                $('#product').text(data.product_title);
                
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
            var contact_id = $(this).data('id');
            var status = $(this).prop('checked') ? 1 : 0;

            $.ajax({
                url: '/admin/contacts/status',
                method: "POST",
                data: {
                    contact_id: contact_id,
                    status: status,
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                  success(res.message);
                  reloadTable();
                },
                error: function(xhr, status, error) {
                  console.error(xhr.responseText);
                  error('Failed to update status');
                }
            });
        });

        //Delete
        $("#contentContainer").on('click','.delete', function(){
            if(!confirm('Are you sure you want to delete this contact message?')) return;
            var id = $(this).data('id');
            var delete_url = url + '/' + id + '/delete';
            
            $.ajax({
                url: delete_url,
                method: "GET",
                success: function(res) {
                  success(res.message);
                  reloadTable();
                },
                error: function(xhr) {
                  console.error(xhr.responseText);
                  if (xhr.responseJSON && xhr.responseJSON.errors)
                    error(Object.values(xhr.responseJSON.errors)[0][0]);
                  else
                    error();
                }
            });
        });

        var table = $('#example1').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('contacts.index') }}",
                type: "GET",
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'date', name: 'date'},
                {data: 'full_name', name: 'full_name'},
                {data: 'email', name: 'email'},
                {data: 'subject', name: 'subject'},
                {data: 'product', name: 'product'},
                {data: 'status', name: 'status', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            responsive: true,
            lengthChange: false,
            autoWidth: false,
        });

        function reloadTable() {
          table.ajax.reload(null, false);
        }
    });
</script>
@endsection