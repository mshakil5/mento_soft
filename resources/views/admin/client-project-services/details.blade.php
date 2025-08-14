@extends('admin.master')

@section('content')
<section class="content" id="newBtnSection">
    <div class="container-fluid">
        <div class="row">
            <div class="d-flex gap-2 my-3">
              <a href="{{ url()->previous() }}" class="btn btn-secondary mr-2">Back to Services</a>
              <button type="button" class="btn btn-secondary" id="newBtn">Add new Service Detail</button>
            </div>
        </div>
    </div>
</section>

<section class="content mt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-8">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Add new Detail for {{ $service->name }}</h3>
                    </div>
                    <div class="card-body">
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" id="codeid" name="codeid">
                            <input type="hidden" name="project_service_id" value="{{ $service->id }}">

                            <div class="row">
                              <div class="col-md-6">
                                  <div class="form-group">
                                      <label>Start Date <span class="text-danger">*</span></label>
                                      <input type="date" class="form-control" id="start_date" name="start_date" required>
                                  </div>
                              </div>
                              <div class="col-md-6">
                                  <div class="form-group">
                                      <label>End Date <span class="text-danger">*</span></label>
                                      <input type="date" class="form-control" id="end_date" name="end_date" required>
                                  </div>
                              </div>                
                            </div>

                            <div class="row">
                              <div class="col-md-6">
                                  <div class="form-control">
                                      <input type="checkbox" class="form-control-input" id="is_auto" name="is_auto" value="1">
                                      <label>Auto Renewal</label>
                                  </div>
                              </div>

                              <div class="col-md-6" id="cycle_type_container" style="display:none;">
                                  <div class="form-group">
                                        <select class="form-control" name="cycle_type" id="cycle_type">
                                            <option value="1">Monthly</option>
                                            <option value="2">Yearly</option>
                                        </select>
                                  </div>
                              </div>
                            </div>

                            <div class="form-group mt-3">
                                <label>Amount <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                            </div>

                            <div class="form-group">
                                <label>Note</label>
                                <textarea class="form-control" id="note" name="note" rows="3" placeholder="Optional note"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <button type="submit" id="addBtn" class="btn btn-secondary" value="Create">Create</button>
                        <button type="submit" id="FormCloseBtn" class="btn btn-default">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="content" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Details for Service: {{ $service->name }}</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table cell-border table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Next Renewal</th>
                                    <th>Amount</th>
                                    <th>Note</th>
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
@endsection

@section('script')
<script>
$(document).ready(function () {
    $("#addThisFormContainer").hide();

    $("#newBtn").click(function(){
        clearform();
        $("#newBtn").hide(100);
        $("#addThisFormContainer").show(300);
    });

    $("#FormCloseBtn").click(function(){
        $("#addThisFormContainer").hide(200);
        $("#newBtn").show(100);
        clearform();
    });

    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    var url = "/admin/client-project-services/{{ $service->id }}/details";
    var upurl = "/admin/client-project-service-detail/:id";

    $("#addBtn").click(function(){
        var form_data = new FormData();
        form_data.append("project_service_id", "{{ $service->id }}");
        form_data.append("start_date", $("#start_date").val());
        form_data.append("end_date", $("#end_date").val());
        form_data.append("amount", $("#amount").val());
        form_data.append("note", $("#note").val());
        form_data.append("is_auto", $("#is_auto").is(":checked") ? 1 : null);
        if ($("#is_auto").is(":checked")) {
            form_data.append("is_auto", 1);
        }

        if($(this).val() == 'Create') {
            // Create
            $.ajax({
                url: url,
                method: "POST",
                contentType: false,
                processData: false,
                data: form_data,
                success: function(res) {
                    clearform();
                    success(res.message);
                    pageTop();
                    reloadTable();
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    pageTop();
                    if(xhr.responseJSON && xhr.responseJSON.errors)
                        error(Object.values(xhr.responseJSON.errors)[0][0]);
                    else
                        error();
                }
            });
        } else {
            // Update
            form_data.append("codeid", $("#codeid").val());
            if ($("#is_auto").is(":checked")) {
                form_data.append("is_auto", 1);
                form_data.append("cycle_type", $("#cycle_type").val());
            } else {
                form_data.append("is_auto", 0);
                form_data.append("cycle_type", "");
            }
            var updateUrl = upurl.replace(':id', $("#codeid").val());

            $.ajax({
                url: updateUrl,
                method: "POST",
                contentType: false,
                processData: false,
                data: form_data,
                success: function(res) {
                    clearform();
                    success(res.message);
                    pageTop();
                    reloadTable();
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    pageTop();
                    if(xhr.responseJSON && xhr.responseJSON.errors)
                        error(Object.values(xhr.responseJSON.errors)[0][0]);
                    else
                        error();
                }
            });
        }
    });

    // Edit
    $("#contentContainer").on('click','.edit', function(){
        $("#cardTitle").text('Update this detail');
        var codeid = $(this).data('id');
        var info_url = "/admin/client-project-service-detail/" + codeid + "/edit";
        $.get(info_url, {}, function(d){
            populateForm(d);
        });
    });

    function populateForm(data){
        $("#start_date").val(data.start_date);
        $("#end_date").val(data.end_date);
        $("#amount").val(data.amount);
        $("#note").val(data.note);
        $("#codeid").val(data.id);
        if(data.is_auto == 1){
            $("#is_auto").prop('checked', true);
            $("#cycle_type_container").show();
            $("#cycle_type").val(data.cycle_type); // Set selected option
        } else {
            $("#is_auto").prop('checked', false);
            $("#cycle_type_container").hide();
            $("#cycle_type").val(''); // Clear selection
        }

        $("#addBtn").val('Update');
        $("#addBtn").html('Update');
        $("#addThisFormContainer").show(300);
        $("#newBtn").hide(100);
    }

    function clearform(){
        $('#createThisForm')[0].reset();
        $("#is_auto").prop('checked', false);
        $("#cycle_type_container").hide();
        $("#cycle_type").val('');
        $("#addBtn").val('Create');
        $("#addBtn").html('Create');
        $("#addThisFormContainer").slideUp(200);
        $("#newBtn").slideDown(200);
        $("#cardTitle").text('Add new Detail for {{ $service->name }}');
    }

    // Status toggle
    $(document).on('change', '.toggle-status', function() {
        var detail_id = $(this).data('id');
        var status = $(this).prop('checked') ? 1 : 0;
        var toggleUrl = "/admin/client-project-service-detail/" + detail_id + "/toggle-status";

        $.ajax({
            url: toggleUrl,
            method: "POST",
            data: {
                status: status,
                _token: "{{ csrf_token() }}"
            },
            success: function(res) {
                success(res.message);
                reloadTable();
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                error('Failed to update status');
            }
        });
    });

    // Delete
    $("#contentContainer").on('click','.delete', function(){
        if(!confirm('Are you sure you want to delete this detail?')) return;
        var codeid = $(this).data('id');
        var info_url = "/admin/client-project-service-detail/" + codeid;
        $.ajax({
            url: info_url,
            method: "DELETE",
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(res) {
                clearform();
                success(res.message);
                pageTop();
                reloadTable();
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                pageTop();
                if(xhr.responseJSON && xhr.responseJSON.errors)
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
            url: "{{ route('client-project-services.details', $service->id) }}" + window.location.search,
            type: "GET",
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'start_date', name: 'start_date'},
            {data: 'end_date', name: 'end_date'},
            {data: 'next_renewal', name: 'next_renewal'},
            {data: 'amount', name: 'amount', orderable: false, searchable: false},
            {data: 'note', name: 'note', orderable: false, searchable: false},
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

    $('#is_auto').change(function() {
        if ($(this).is(':checked')) {
            $('#cycle_type_container').show();
        } else {
            $('#cycle_type_container').hide();
        }
    });

    $(document).on('submit', '.receive-form', function(e) {
        e.preventDefault();
        if (!confirm('Mark as received?')) return;

        let form = $(this);
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success(res) {
                success(res.message ?? 'Received successfully!');
                form.closest('.modal').modal('hide');
                reloadTable();
            },
            error() {
                error('Something went wrong.');
            }
        });
    });

});
</script>
@endsection