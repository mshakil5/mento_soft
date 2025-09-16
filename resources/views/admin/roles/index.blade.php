@extends('admin.master')

@section('content')
<!-- Add New Button -->
<section class="content" id="newBtnSection">
    <div class="container-fluid">
        <div class="row">
            <div class="col-2">
                <button type="button" class="btn btn-secondary my-3" id="newBtn">Add Role</button>
            </div>
        </div>
    </div>
</section>

<!-- Form -->
<section class="content mt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-10">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Add New Role</h3>
                    </div>
                    <div class="card-body">
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" id="codeid" name="codeid">

                            <div class="form-group">
                                <label>Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter role name" required>
                            </div>

                            <div class="form-group">
                                <label>Permissions <span class="text-danger">*</span></label>

                                <div class="mb-2 row">
                                     <div class="col-6">
                                        <input type="text" id="permSearch" class="form-control form-control-sm" 
                                            placeholder="Search permission..." style="width: 250px;">
                                     </div>
                                    <div class="icheck-primary col-6">
                                        <input type="checkbox" id="checkAll">
                                        <label for="checkAll"><strong>Check All Permissions</strong></label>
                                    </div>
                                </div>

                                <div class="row">
                                    @foreach($permissions as $permission)
                                        <div class="col-md-4">
                                            <div class="icheck-primary">
                                                <input type="checkbox" 
                                                      name="permissions[]" 
                                                      value="{{ $permission->id }}" 
                                                      id="perm_{{ $permission->id }}">
                                                <label for="perm_{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
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

<!-- Table -->
<section class="content" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Roles</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table cell-border table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Name</th>
                                    <th>Permissions</th>
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

<style>
  #checkAll + label {
      background: #6c757d;
      color: #fff;
      padding: 5px 10px;
      border-radius: 5px;
  }
</style>
@endsection

@section('script')
<script>
$(document).ready(function () {

  $("#permSearch").on("keyup", function() {
      let value = $(this).val().toLowerCase();
      $("input[name='permissions[]']").each(function() {
          let label = $(this).next("label").text().toLowerCase();
          if (label.includes(value)) {
              $(this).closest(".icheck-primary").parent().show();
          } else {
              $(this).closest(".icheck-primary").parent().hide();
          }
      });
  });

  $("#checkAll").on("change", function() {
      $("input[name='permissions[]']").prop('checked', this.checked);
  });

    // Initialize Select2
    $('#permissions').select2({
        placeholder: "Select permissions",
        width: '100%'
    });

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

    var url = "{{ route('roles.store') }}";
    var upurl = "{{ route('roles.update') }}";

    // Save or Update
    $("#addBtn").click(function(){
        $.ajax({
            url: $(this).val() == 'Create' ? url : upurl,
            method: "POST",
            data: {
                codeid: $("#codeid").val(),
                name: $("#name").val(),
                permissions: $("input[name='permissions[]']:checked").map(function(){ 
                    return this.value; 
                }).get()
            },
            success: function(res) {
                clearform();
                success(res.message);
                reloadTable();
            },
            error: function(xhr) {
                        console.log("Error Response:", xhr.responseText);
                if(xhr.responseJSON && xhr.responseJSON.errors)
                    error(Object.values(xhr.responseJSON.errors)[0][0]);
                else
                    error();
            }
        });
    });

    // Edit
    $("#contentContainer").on('click','.edit', function(){
        $("#cardTitle").text('Update Role');
        let id = $(this).data('id');
        $.get("{{ url('/admin/roles') }}/" + id + "/edit", function(d){
            populateForm(d);
        });
    });

    function populateForm(data){
        $("#name").val(data.name);
        $("#codeid").val(data.id);

        $("input[name='permissions[]']").prop('checked', false);
        data.permissions.forEach(p => {
            $("#perm_" + p.id).prop('checked', true);
        });

        $("#addBtn").val('Update').html('Update');
        $("#addThisFormContainer").show(300);
        $("#newBtn").hide(100);
    }

    function clearform(){
        $('#createThisForm')[0].reset();
        $("#permissions").val([]).trigger('change'); // clear Select2
        $("#addBtn").val('Create');
        $("#addBtn").html('Create');
        $("#addThisFormContainer").slideUp(200);
        $("#newBtn").slideDown(200);
        $("#cardTitle").text('Add New Role');
        $("#permSearch").val('');
        $("input[name='permissions[]']").each(function() {
            $(this).closest(".icheck-primary").parent().show();
        });
    }

    // Delete
    $("#contentContainer").on('click','.delete', function(){
        if(!confirm('Are you sure you want to delete this role?')) return;
        let id = $(this).data('id');
        $.ajax({
            url: "{{ url('/admin/roles') }}/" + id,
            method: "DELETE",
            success: function(res) {
                success(res.message);
                reloadTable();
            },
            error: function(xhr) {
                error();
            }
        });
    });

    // DataTable
    var table = $('#example1').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('roles.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'name', name: 'name'},
            {data: 'permissions', name: 'permissions'},
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