@extends('admin.master')

@section('content')
<section class="content pt-3">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Edit Task for</h3>
                    </div>
                    <div class="card-body">
                        <form id="editThisForm">
                            @csrf
                            <input type="hidden" id="task_id" value="{{ $task->id }}">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Project <span class="text-danger">*</span></label>
                                        <select class="form-control select2" id="client_project_id" name="client_project_id" required>
                                            <option value="">Select Project</option>
                                            @foreach($projects as $project)
                                                <option value="{{ $project->id }}" {{ $task->client_project_id == $project->id ? 'selected' : '' }}>
                                                    {{ $project->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Task <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="title" name="title" value="{{ $task->title }}" required>
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
                                                <option value="{{ $employee->id }}" {{ $task->employee_id == $employee->id ? 'selected' : '' }}>
                                                    {{ $employee->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Priority <span class="text-danger">*</span></label>
                                        <select class="form-control" id="priority" name="priority" required>
                                            <option value="high" {{ $task->priority == 'high' ? 'selected' : '' }}>High</option>
                                            <option value="medium" {{ $task->priority == 'medium' ? 'selected' : '' }}>Medium</option>
                                            <option value="low" {{ $task->priority == 'low' ? 'selected' : '' }}>Low</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Due Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="due_date" name="due_date" value="{{ $task->due_date }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Status <span class="text-danger">*</span></label>
                                        <select class="form-control" id="status" name="status" required>
                                            <option value="1" {{ $task->status==1?'selected':'' }}>To Do</option>
                                            <option value="2" {{ $task->status==2?'selected':'' }}>In Progress</option>
                                            <option value="3" {{ $task->status==3?'selected':'' }}>Done</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-1">
                                  <div class="form-group">
                                    <div class="d-flex ml-2">
                                      <div class="form-check mr-5">
                                        <input type="checkbox" id="allow_client" name="allow_client" value="1" class="form-check-input" {{ $task->allow_client ? 'checked' : '' }}>
                                        <label class="form-check-label">Visible to Client</label>
                                      </div>
                                      <div class="form-check mr-5">
                                        <input type="checkbox" id="allow_employee" name="allow_employee" value="1" class="form-check-input" {{ $task->allow_employee ? 'checked' : '' }}>
                                        <label class="form-check-label">Visible to Other Employees</label>
                                      </div>
                                      <div class="form-check">
                                        <input type="checkbox" id="is_confirmed" name="is_confirmed" value="1" class="form-check-input" {{ $task->is_confirmed ? 'checked' : '' }}>
                                        <label class="form-check-label">Confirmed By Client</label>
                                      </div>
                                    </div>
                                  </div>
                                </div>

                            </div>

                            <div class="form-group">
                                <label>Task Description <span class="text-danger">*</span></label>
                                <textarea class="form-control summernote" id="task" name="task" rows="5">{{ $task->task }}</textarea>
                            </div>
                            <div class="card-footer">
                                <button type="submit" id="updateBtn" class="btn btn-secondary">Update</button>
                                <a href="{{ url()->previous() }}" class="btn btn-default">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
<script>
$(document).ready(function() {
    $("#editThisForm").on("submit", function(e){
        e.preventDefault();
        let taskId = $("#task_id").val();
        let formData = {
            client_project_id: $("#client_project_id").val(),
            title: $("#title").val(),
            task: $("#task").val(),
            employee_id: $("#employee_id").val(),
            priority: $("#priority").val(),
            due_date: $("#due_date").val(),
            status: $("#status").val(),
            allow_client: $("#allow_client").is(':checked') ? 1 : 0,
            allow_employee: $("#allow_employee").is(':checked') ? 1 : 0,
            is_confirmed: $("#is_confirmed").is(':checked') ? 1 : 0,
            _token: "{{ csrf_token() }}"
        };

        $.ajax({
            url: `/admin/client-projects-task/${taskId}`,
            type: "POST",
            data: formData,
            success: function(res){
                success(res.message);
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
});
</script>
@endsection