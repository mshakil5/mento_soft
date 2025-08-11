@extends('admin.master')

@section('content')
<!-- Main content -->
<section class="content" id="newBtnSection">
    <div class="container-fluid">
        <div class="row">
            <div class="d-flex gap-2 my-3">
              <a href="{{ url()->previous() }}" class="btn btn-secondary mr-2">Back to Projects</a>
              <button type="button" class="btn btn-secondary" id="newBtn">Add new Update</button>
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
                        <h3 class="card-title" id="cardTitle">Add new Update for {{ $project->title }}</h3>
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
                                <textarea class="form-control summernote" id="description" name="description" rows="5" placeholder="Enter description" required></textarea>
                            </div>

                            <div class="form-group">
                                <label>Attachment</label>
                                <input type="file" class="form-control-file" id="attachment" name="attachment">
                                <small class="form-text text-muted">Max file size: 10MB</small>
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
                        <h3 class="card-title">Recent Updates for Project: {{ $project->title }}</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table cell-border table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Date</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Attachment</th>
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
        
        var url = "/admin/client-projects/{{ $project->id }}/updates";
        var upurl = "/admin/client-projects-update/:id";

        $("#addBtn").click(function(){
            var form_data = new FormData();
            form_data.append("client_project_id", "{{ $project->id }}");
            form_data.append("title", $("#title").val());
            form_data.append("description", $("#description").val());
            
            if ($('#attachment')[0].files[0]) {
                form_data.append("attachment", $('#attachment')[0].files[0]);
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
            $("#cardTitle").text('Update this record');
            codeid = $(this).data('id');
            info_url = "/admin/client-projects-update/"+codeid+"/edit";
            $.get(info_url,{},function(d){
                populateForm(d);
            });
        });

        function populateForm(data){
            $("#title").val(data.title);
            $("#description").summernote('code', data.description);
            $("#codeid").val(data.id);

            $("#addBtn").val('Update');
            $("#addBtn").html('Update');
            $("#addThisFormContainer").show(300);
            $("#newBtn").hide(100);
        }
        
        function clearform(){
            $('#createThisForm')[0].reset();
            $('.summernote').summernote('reset');
            $("#addBtn").val('Create');
            $("#addBtn").html('Create');
            $("#addThisFormContainer").slideUp(200);
            $("#newBtn").slideDown(200);
            $("#cardTitle").text('Add new Update for {{ $project->title }}');
            $('#attachment').val('');
        }

        //Delete
        $("#contentContainer").on('click','.delete', function(){
            if(!confirm('Are you sure you want to delete this update?')) return;
            codeid = $(this).data('id');
            var info_url = "/admin/client-projects-update/" + codeid;
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
                url: "{{ route('client-projects.updates', $project->id) }}",
                type: "GET",
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'date', name: 'created_at'},
                {data: 'title', name: 'title'},
                {data: 'description', name: 'description'},
                {data: 'attachment', name: 'attachment', orderable: false, searchable: false},
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