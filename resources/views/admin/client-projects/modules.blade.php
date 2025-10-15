@extends('admin.master')

@section('content')
<section class="content" id="newBtnSection">
    <div class="container-fluid">
        <div class="row">
            <div class="col-4 my-3">
              <a href="{{ url()->previous() }}" class="btn btn-secondary">Back to Projects</a>
              <button type="button" class="btn btn-secondary" id="newBtn">Add new Module</button>
            </div>
        </div>
    </div>
</section>

<section class="content mt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-10">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Add new Module for {{ $project->project_name }}</h3>
                    </div>
                    <div class="card-body">
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" class="form-control" id="codeid" name="codeid">
                            <input type="hidden" name="client_project_id" value="{{ $project->id }}">

                            <div class="form-group">
                                <label>Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" placeholder="Enter title" required>
                            </div>

                            <div class="form-group">
                                <label>Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="description" name="description" rows="5" placeholder="Enter description" required></textarea>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Start Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Estimated End Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="estimated_end_date" name="estimated_end_date" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Status <span class="text-danger">*</span></label>
                                <select class="form-control" id="status" name="status">
                                    <option value="1">To Do</option>
                                    <option value="2">In Progress</option>
                                    <option value="3">Done</option>
                                </select>
                            </div>

                        </form>
                    </div>
                    <div class="card-footer">
                        <button type="submit" id="addBtn" class="btn btn-secondary" value="Create">Create</button>
                        <button type="button" id="FormCloseBtn" class="btn btn-default">Cancel</button>
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
                        <h3 class="card-title">Modules for Project: {{ $project->project_name }}</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Start Date</th>
                                    <th>Estimated End Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
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
    
    var url = "/admin/client-projects/{{ $project->id }}/modules";
    var upurl = "/admin/client-projects-module/:id";

    $("#addBtn").click(function(){
        var form_data = new FormData();
        form_data.append("client_project_id", "{{ $project->id }}");
        form_data.append("title", $("#title").val());
        form_data.append("description", $("#description").val());
        form_data.append("start_date", $("#start_date").val());
        form_data.append("estimated_end_date", $("#estimated_end_date").val());
        form_data.append("status", $("#status").val());

        if($(this).val() == 'Create') {
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
            form_data.append("codeid", $("#codeid").val());
            var updateUrl = upurl.replace(':id', $("#codeid").val());
            $.ajax({
                url: updateUrl,
                type: "POST",
                dataType: 'json',
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

    //Edit
    $("#contentContainer").on('click','.edit', function(){
        $("#cardTitle").text('Update this Module');
        codeid = $(this).data('id');
        info_url = "/admin/client-projects-module/"+codeid+"/edit";
        $.get(info_url,{},function(d){
            $("#title").val(d.title);
            $("#description").val(d.description);
            $("#start_date").val(d.start_date);
            $("#estimated_end_date").val(d.estimated_end_date);
            $("#status").val(d.status);
            $("#codeid").val(d.id);
            $("#addBtn").val('Update').html('Update');
            $("#addThisFormContainer").show(300);
            $("#newBtn").hide(100);
        });
    });

    // Status change
    $("#contentContainer").on('click','.status-change', function(e){
        e.preventDefault();
        let id = $(this).data('id');
        let status = $(this).data('status');

        $.post("/admin/client-projects-module/" + id + "/toggle-status", {
            _token: "{{ csrf_token() }}",
            status: status
        }, function(res){
            table.ajax.reload();
            toastr.success(res.message);
        }).fail(function(xhr){
            console.error(xhr.responseText);
            toastr.error('Failed to update status.');
        });
    });

    //Delete
    $("#contentContainer").on('click','.delete', function(){
        if(!confirm('Are you sure you want to delete this module?')) return;
        codeid = $(this).data('id');
        var info_url = "/admin/client-projects-module/" + codeid;
        $.ajax({
            url: info_url,
            method: "DELETE",
            data: {_token: "{{ csrf_token() }}"},
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
        ajax: "{{ route('client-projects.modules', $project->id) }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'title', name: 'title'},
            {data: 'description', name: 'description'},
            {data: 'start_date', name: 'start_date'},
            {data: 'end_date', name: 'end_date'},
            {data: 'status', name: 'status', orderable:false, searchable:false},
            {data: 'action', name: 'action', orderable:false, searchable:false},
        ],
        responsive: true,
        lengthChange: false,
        autoWidth: false,
    });

    function reloadTable() { table.ajax.reload(null, false); }

    function clearform(){
        $('#createThisForm')[0].reset();
        $("#addBtn").val('Create').html('Create');
        $("#addThisFormContainer").slideUp(200);
        $("#newBtn").slideDown(200);
        $("#cardTitle").text('Add new Module for {{ $project->project_name }}');
        $("#codeid").val('');
    }
});
</script>
@endsection