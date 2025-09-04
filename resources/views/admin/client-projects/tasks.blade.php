@extends('admin.master')

@section('content')
<!-- Main content -->
<section class="content" id="newBtnSection">
    <div class="container-fluid">
        <div class="row">
            <div class="col-2">
              <a href="{{ url()->previous() }}" class="btn btn-secondary my-3">Back</a>
              <button type="button" class="btn btn-secondary" id="newBtn">Add new Task</button>
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
                        <h3 class="card-title" id="cardTitle">Add new Task for {{ $project->title }}</h3>
                    </div>
                    <div class="card-body">
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" class="form-control" id="codeid" name="codeid">
                            <input type="hidden" name="client_project_id" value="{{ $project->id }}">
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Task <span class="text-danger">*</span></label>
                                        <input type="text" name="title" class="form-control" id="title">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Assigned To <span class="text-danger">*</span></label>
                                        <select class="form-control select2" id="employee_id" name="employee_id" required>
                                            <option value="">Select Employee</option>
                                            @foreach($employees as $employee)
                                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Priority <span class="text-danger">*</span></label>
                                        <select class="form-control" id="priority" name="priority" required>
                                            <option value="high">High</option>
                                            <option value="medium" selected>Medium</option>
                                            <option value="low">Low</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Due Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="due_date" name="due_date" min="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Status <span class="text-danger">*</span></label>
                                        <select class="form-control" id="status" name="status" required>
                                            <option value="1">To Do</option>
                                            <option value="2">In Progress</option>
                                            <option value="3">Done</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Task Description <span class="text-danger">*</span></label>
                                <textarea class="form-control summernote" id="task" name="task" rows="5" placeholder="Enter task description"></textarea>
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
                        <h3 class="card-title">Tasks for Project: {{ $project->title }}</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table cell-border table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Date</th>
                                    <th>Task</th>
                                    <th>Assigned To</th>
                                    <th>Priority</th>
                                    <th>Due Date</th>
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
        
        var url = "/admin/client-projects/{{ $project->id }}/tasks";
        var upurl = "/admin/client-projects-task/:id";

        $("#addBtn").click(function(){
            var form_data = new FormData();
            form_data.append("client_project_id", "{{ $project->id }}");
            form_data.append("title", $("#title").val());
            form_data.append("task", $("#task").val());
            form_data.append("employee_id", $("#employee_id").val());
            form_data.append("priority", $("#priority").val());
            form_data.append("due_date", $("#due_date").val());
            form_data.append("status", $("#status").val());

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
            $("#cardTitle").text('Update this task');
            codeid = $(this).data('id');
            info_url = "/admin/client-projects-task/"+codeid+"/edit";
            $.get(info_url,{},function(d){
                populateForm(d);
            });
        });

        function populateForm(data){
            $("#employee_id").val(data.employee_id).trigger('change');
            $("#title").val(data.title);
            $("#task").summernote('code', data.task);
            $("#priority").val(data.priority);
            $("#status").val(data.status);
            $("#due_date").val(data.due_date);
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
            $('.summernote').summernote('reset');
            $("#employee_id").val(null).trigger('change');
            $("#cardTitle").text('Add new Task for {{ $project->title }}');
        }

        $(document).on('click', '.status-change', function(e) {
            e.preventDefault();

            var taskId = $(this).data('id');
            var status = $(this).data('status');
            var toggleUrl = "/admin/client-projects-task/" + taskId + "/toggle-status";
            
            $.ajax({
                url: toggleUrl,
                method: "POST",
                data: {
                    task_id: taskId,
                    status: status,
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    success(res.message);
                    reloadTable();
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    error('Failed to update task status.');
                }
            });
        });

        //Delete
        $("#contentContainer").on('click','.delete', function(){
            if(!confirm('Are you sure you want to delete this task?')) return;
            codeid = $(this).data('id');
            var info_url = "/admin/client-projects-task/" + codeid;
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
                url: "{{ route('client-projects.tasks', $project->id) }}" + window.location.search,
                type: "GET",
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'date', name: 'date'},
                {data: 'title', name: 'title'},
                {data: 'employee_name', name: 'employee_name'},
                {data: 'priority', name: 'priority', orderable: false, searchable: false},
                {data: 'due_date', name: 'due_date'},
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