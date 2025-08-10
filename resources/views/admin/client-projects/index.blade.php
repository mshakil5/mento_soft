@extends('admin.master')

@section('content')
<!-- Main content -->
<section class="content" id="newBtnSection">
    <div class="container-fluid">
        <div class="row">
            <div class="col-2">
                  @if(request()->client_id)
                    <a href="{{ url()->previous() }}" class="btn btn-secondary my-3">Back</a>
                  @endif
                <button type="button" class="btn btn-secondary my-3" id="newBtn">Add new</button>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="taskModal" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel" aria-hidden="true" data-project-id="">
  <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="taskModalLabel">Task List</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">

        <button class="btn btn-sm btn-success mb-3" id="showAddTaskBtn">+ Add an Item</button>

        <div class="progress mb-2">
          <div class="progress-bar" id="projectProgressBar" role="progressbar" style="width: 0%;">
            0% Complete
          </div>
        </div>


        <div class="mb-3" id="addTaskArea" style="display: none;">
          <textarea class="form-control mb-2" placeholder="Add an item" id="taskText"></textarea>
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <button class="btn btn-primary btn-sm" id="saveTaskBtn">Add</button>
              <button class="btn btn-link btn-sm" id="cancelAddTaskBtn">Cancel</button>
            </div>
            <div class="d-flex gap-2">
              <select id="assignEmployee" class="form-control select2" style="width: 180px;">
                  @foreach($employees as $emp)
                      <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                  @endforeach
              </select>

              <input type="date" id="dueDateInput" class="form-control" style="width: 200px; margin-left: 10px;" value="{{ date('Y-m-d') }}" placeholder="Due Date">
            </div>
          </div>
        </div>

        <hr>

        <div style="max-height: 400px; overflow-y: auto;" class="px-2 mx-1">
            <div id="taskList"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<section class="content mt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-10">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Add new Client Project</h3>
                    </div>
                    <div class="card-body">
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" class="form-control" id="codeid" name="codeid">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Client <span class="text-danger">*</span></label>
                                        <select class="form-control select2" id="client_id" name="client_id" required>
                                            <option value="">Select Client</option>
                                            @foreach($clients as $client)
                                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Project Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="title" name="title" placeholder="Enter project title" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Domain</label>
                                        <input type="text" class="form-control" id="domain" name="domain" placeholder="Enter domain">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Project URL</label>
                                        <input type="url" class="form-control" id="project_url" name="project_url" placeholder="Enter project URL">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tech Stack (comma separated)</label>
                                        <input type="text" class="form-control" id="tech_stack" name="tech_stack" placeholder="e.g. PHP,Laravel,MySQL">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Status <span class="text-danger">*</span></label>
                                        <select class="form-control" id="status" name="status" required>
                                            <option value="1">Pending</option>
                                            <option value="2">In Progress</option>
                                            <option value="3">Completed</option>
                                            <option value="4">On Hold</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Start Date</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>End Date</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Description</label>
                                <textarea class="form-control summernote" id="description" name="description" rows="5" placeholder="Enter project description"></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label>Additional Info</label>
                                <textarea class="form-control summernote" id="additional_info" name="additional_info" rows="5" placeholder="Enter additional information"></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Project Image (800x600 recommended)</label>
                                        <input type="file" class="form-control-file" id="image" name="image" accept="image/*">
                                        <img id="preview-image" src="#" alt="" style="max-width: 300px; width: 100%; height: auto; margin-top: 20px;">
                                    </div>
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

<section class="content" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Client Projects</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table cell-border table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Date</th>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Client</th>
                                    <th>Domain</th>
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
        
        var url = "{{URL::to('/admin/client-projects')}}";
        var upurl = "{{URL::to('/admin/client-projects/update')}}";

        $("#addBtn").click(function(){
            var form_data = new FormData();
            form_data.append("client_id", $("#client_id").val());
            form_data.append("title", $("#title").val());
            form_data.append("domain", $("#domain").val());
            form_data.append("project_url", $("#project_url").val());
            form_data.append("tech_stack", $("#tech_stack").val());
            form_data.append("description", $("#description").val());
            form_data.append("additional_info", $("#additional_info").val());
            form_data.append("start_date", $("#start_date").val());
            form_data.append("end_date", $("#end_date").val());
            form_data.append("status", $("#status").val());

            // Handle image upload
            var imageInput = document.getElementById('image');
            if(imageInput.files && imageInput.files[0]) {
                form_data.append("image", imageInput.files[0]);
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
                
                $.ajax({
                    url: upurl,
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
            $("#cardTitle").text('Update this project');
            codeid = $(this).data('id');
            info_url = url + '/'+codeid+'/edit';
            $.get(info_url,{},function(d){
                populateForm(d);
            });
        });

        function populateForm(data){
            pageTop();
            $("#client_id").val(data.client_id).trigger('change');
            $("#title").val(data.title);
            $("#domain").val(data.domain);
            $("#project_url").val(data.project_url);
            $("#tech_stack").val(data.tech_stack);
            $("#description").summernote('code', data.description);
            $("#additional_info").summernote('code', data.additional_info);
            $("#start_date").val(data.start_date);
            $("#end_date").val(data.end_date);
            $("#status").val(data.status);
            $("#codeid").val(data.id);
            
            // Set preview image
            if (data.image) {
                $("#preview-image").attr("src", '/images/client-projects/' + data.image).show();
            }

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
            $('#preview-image').attr('src', '#');
            $('.select2').val('').trigger('change');
            $("#cardTitle").text('Add new client project');
        }
        
        previewImage('#image', '#preview-image');

        //Delete
        $("#contentContainer").on('click','.delete', function(){
            if(!confirm('Are you sure you want to delete this project?')) return;
            codeid = $(this).data('id');
            info_url = url + '/'+codeid;
            $.ajax({
                url: info_url,
                method: "GET",
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

        // Status change handler
        $(document).on('click', '.status-change', function(e) {
            e.preventDefault();
            var project_id = $(this).data('id');
            var status = $(this).data('status');
            
            $.ajax({
                url: "{{ route('client-projects.status') }}",
                method: "POST",
                data: {
                    project_id: project_id,
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

        var table = $('#example1').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('client-projects.index') }}" + window.location.search,
                type: "GET",
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'date', name: 'date'},
                {data: 'image', name: 'image', orderable: false, searchable: false},
                {data: 'title', name: 'title'},
                {data: 'client_name', name: 'client_name'},
                {data: 'domain', name: 'domain'},
                {data: 'status', name: 'status', orderable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            responsive: true,
            lengthChange: false,
            autoWidth: false,
        });

        function reloadTable() {
          table.ajax.reload(null, false);
        }

        function loadProjectTasks(projectId) {
            let url = "{{ route('project-tasks.by-project', ':id') }}".replace(':id', projectId);

            $.ajax({
                url: url,
                method: 'GET',
                success: function(response) {
                    $('#taskList').html(response.html);
                    $('#taskModalLabel').text(response.project_name || 'Task List');
                    let progress = response.progress || 0;
                    let progressBar = $('#projectProgressBar');

                    progressBar
                        .css('width', progress + '%')
                        .removeClass('bg-success bg-warning bg-danger')
                        .addClass(progress === 100 ? 'bg-success' : progress < 20 ? 'bg-danger' : 'bg-warning')
                        .text(progress + '% Complete');
                    initSelect2();
                    $('#taskModal').modal('show');
                    $('#taskModal').attr('data-project-id', projectId);
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText);
                    error('Failed to load tasks');
                }
            });
        }

        $(document).on('click', '.tasks-btn', function () {
            let projectId = $(this).data('id');
            loadProjectTasks(projectId);
        });

        $(document).on('change', '.employee-select, .due-date-input', function () {
            let $taskItem = $(this).closest('[data-task-id]');
            let taskId = $taskItem.data('task-id');
            let employeeId = $('.employee-select[data-task-id="'+taskId+'"]').val();
            let dueDate = $('.due-date-input[data-task-id="'+taskId+'"]').val();

            updateTask(taskId, { employee_id: employeeId, due_date: dueDate });
        });

        $(document).on('blur', '.task-text', function () {
            let taskId = $(this).closest('[data-task-id]').data('task-id');
            let taskText = $(this).text().trim();

            if (taskText.length === 0) {
                error('Task text cannot be empty');
                return;
            }

            updateTask(taskId, { task: taskText });
        });

        function updateTask(taskId, data) {
            data._token = "{{ csrf_token() }}";
            data.task_id = taskId;

            $.ajax({
                url: "{{ route('project-tasks.update') }}",
                method: 'POST',
                data: data,
                success: function(res) {
                    success(res.message);
                    let projectId = $('#taskModal').attr('data-project-id');
                    loadProjectTasks(projectId); 
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    if (xhr.responseJSON && xhr.responseJSON.errors)
                        error(Object.values(xhr.responseJSON.errors)[0]);
                    else
                        error('Update failed!');
                }
            });
        }

        $('#addTaskArea').hide();

        $(document).on('click', '#showAddTaskBtn', function () {
            $('#addTaskArea').slideDown();
            $('#showAddTaskBtn').slideUp();
        });

        $(document).on('click', '#cancelAddTaskBtn', function () {
            $('#addTaskArea').slideUp();
            $('#showAddTaskBtn').slideDown();
        });

        $(document).on('click', '#saveTaskBtn', function () {
            let projectId = $('#taskModal').attr('data-project-id');
            let taskText = $('#taskText').val().trim();
            let employeeId = $('#assignEmployee').val();
            let dueDate = $('#dueDateInput').val();

            if (!taskText) return error('Task cannot be empty');

            $.ajax({
                url: "{{ route('project-tasks.store') }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    project_id: projectId,
                    task: taskText,
                    employee_id: employeeId,
                    due_date: dueDate
                },
                success: function(res) {
                    success(res.message);
                    $('#taskText').val('');
                    $('#addTaskArea').slideUp();
                    $('#showAddTaskBtn').slideDown();
                    loadProjectTasks(projectId);
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    pageTop();
                    if (xhr.responseJSON && xhr.responseJSON.errors)
                        error(Object.values(xhr.responseJSON.errors)[0]);
                    else
                        error('Something went wrong!');
                }
            });
        });

        $(document).on('click', '.delete-task-btn', function() {
            let $taskRow = $(this).closest('[data-task-id]');
            let taskId = $taskRow.data('task-id');
            let projectId = $('#taskModal').attr('data-project-id');

            if (!taskId) return;

            if (confirm('Are you sure you want to delete this task?')) {
                let url = "{{ route('project-tasks.destroy', ':id') }}".replace(':id', taskId);

                $.ajax({
                    url: url,
                    method: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        success(res.message || 'Task deleted');
                        loadProjectTasks(projectId);
                    },
                    error: function(xhr) {
                        error('Delete failed!');
                    }
                });
            }
        });

        $(document).on('change', '.toggle-task-status', function () {
            let taskId = $(this).data('task-id');

            $.ajax({
                url: "{{ route('project-tasks.toggle-status') }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    task_id: taskId
                },
                success: function (res) {
                    success(res.message || 'Status toggled');
                    let projectId = $('#taskModal').attr('data-project-id');
                    loadProjectTasks(projectId);
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    error('Status update failed!');
                }
            });
        });

        $('#taskModal').on('hidden.bs.modal', function () {
            $(this).removeAttr('data-project-id');
            $('#taskList').empty();
            $('#taskText').val('');
            $('#assignEmployee').val(null).trigger('change');
            $('#dueDateInput').val('');
            $('#addTaskArea').hide();
            $('#showAddTaskBtn').show();
            reloadTable();
        });

        function initSelect2() {
            $('#taskModal .select2').select2({
                dropdownParent: $('#taskModal')
            });

            $('.select2').not('#taskModal .select2').select2({
                width: '100%'
            });
        }
    });
</script>
@endsection