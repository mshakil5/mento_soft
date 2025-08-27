@extends('admin.master')

@section('content')
<!-- Main content -->
<section class="content" id="newBtnSection">
    <div class="container-fluid">
        <div class="row" id="newBtn">
          <div class="d-flex gap-2 my-3 col-4">
              <a href="{{ route('project-services.index') }}" class="btn btn-secondary mr-2">Back to Services</a>
              <button type="button" class="btn btn-secondary">Add new Service Type</button>
            </div>
        </div>
    </div>
</section>

<section class="content pt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-10">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Add new Service Type</h3>
                    </div>
                    <div class="card-body">
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" class="form-control" id="codeid" name="codeid">

                            <div class="form-group">
                                <label>Type Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>

                            <div class="form-group">
                                <label>Description</label>
                                <textarea class="form-control" id="description" name="description" rows="5" placeholder="Enter description"></textarea>
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
                        <h3 class="card-title">All Service Types</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table cell-border table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Name</th>
                                    <th>Description</th>
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

    var url = "/admin/service-type";
    var upurl = "/admin/service-type/:id";

    $("#addBtn").click(function(){
        var form_data = new FormData();
        form_data.append("name", $("#name").val());
        form_data.append("description", $("#description").val());

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
                    if (xhr.responseJSON && xhr.responseJSON.errors)
                        error(Object.values(xhr.responseJSON.errors)[0][0]);
                    else
                        error();
                }
            });
        } else {
            // Update
            form_data.append("codeid", $("#codeid").val());
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
                    if (xhr.responseJSON && xhr.responseJSON.errors)
                        error(Object.values(xhr.responseJSON.errors)[0][0]);
                    else
                        error();
                }
            });
        }
    });

    // Edit
    $("#contentContainer").on('click','.edit', function(){
        $("#cardTitle").text('Update this service type');
        var codeid = $(this).data('id');
        var info_url = "/admin/service-type/" + codeid + "/edit";
        $.get(info_url, {}, function(d){
            populateForm(d);
        });
    });

    function populateForm(data){
        $("#name").val(data.name);
        $("#description").val(data.description);
        $("#codeid").val(data.id);

        $("#addBtn").val('Update');
        $("#addBtn").html('Update');
        $("#addThisFormContainer").show(300);
        $("#newBtn").hide(100);
    }

    function clearform(){
        $('#createThisForm')[0].reset();
        $("#addBtn").val('Create');
        $("#addBtn").html('Create');
        $("#addThisFormContainer").slideUp(200);
        $("#newBtn").slideDown(200);
        $("#cardTitle").text('Add new Service Type');
    }

    // Status toggle
    $(document).on('change', '.toggle-status', function() {
        var service_id = $(this).data('id');
        var status = $(this).prop('checked') ? 1 : 0;
        var toggleUrl = "/admin/service-type/" + service_id + "/toggle-status";

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
        if(!confirm('Are you sure you want to delete this service type?')) return;
        var codeid = $(this).data('id');
        var info_url = "/admin/service-type/" + codeid;
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
            url: "{{ route('service-type.index') }}" + window.location.search,
            type: "GET",
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'name', name: 'name'},
            {data: 'description', name: 'description', orderable: false, searchable: false},
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