@extends('admin.master')

@section('content')
<!-- Add New Button -->
<section class="content" id="newBtnSection">
    <div class="container-fluid">
        <div class="row">
            <div class="col-2">
                <button type="button" class="btn btn-secondary my-3" id="newBtn">Add Permission</button>
            </div>
        </div>
    </div>
</section>

<!-- Form -->
<section class="content mt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-6">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Add New Permission</h3>
                    </div>
                    <div class="card-body">
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" id="codeid" name="codeid">
                            <div class="form-group">
                                <label>Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter permission name" required>
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

<!-- Table -->
<section class="content" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Permissions</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table cell-border table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Name</th>
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
        $("#newBtn").hide();
        $("#addThisFormContainer").show();
    });

    $("#FormCloseBtn").click(function(){
        $("#addThisFormContainer").hide();
        $("#newBtn").show();
        clearform();
    });

    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    let storeUrl = "{{ route('permissions.store') }}";
    let updateUrl = "{{ route('permissions.update') }}";

    $("#addBtn").click(function(){
        let isCreate = $(this).val() == 'Create';
        let ajaxUrl = isCreate ? storeUrl : updateUrl;

        $.post(ajaxUrl, {
            codeid: $("#codeid").val(),
            name: $("#name").val()
        }, function(res){
            clearform();
            success(res.message);
            reloadTable();
        }).fail(function(xhr){
            if(xhr.responseJSON && xhr.responseJSON.errors)
                error(Object.values(xhr.responseJSON.errors)[0][0]);
            else error();
        });
    });

    $("#contentContainer").on('click','.edit', function(){
        let id = $(this).data('id');
        $.get("{{ url('/admin/permissions') }}/" + id + "/edit", function(d){
            populateForm(d);
        });
    });

    function populateForm(data){
        $("#name").val(data.name);
        $("#codeid").val(data.id);
        $("#addBtn").val('Update').html('Update');
        $("#addThisFormContainer").show();
        $("#newBtn").hide();
        $("#cardTitle").text('Update Permission');
    }

    function clearform(){
        $("#createThisForm")[0].reset();
        $("#codeid").val('');
        $("#addBtn").val('Create').html('Create');
        $("#addThisFormContainer").hide();
        $("#newBtn").show();
        $("#cardTitle").text('Add New Permission');
    }

    $("#contentContainer").on('click','.delete', function(){
        if(!confirm('Are you sure you want to delete this permission?')) return;
        let id = $(this).data('id');
        $.get("{{ url('/admin/permissions') }}/" + id, function(res){
            success(res.message);
            reloadTable();
        }).fail(function(){
            error();
        });
    });

    var table = $('#example1').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('permissions.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'name', name: 'name'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });

    function reloadTable() { table.ajax.reload(null, false); }
});
</script>
@endsection