@php
  $employees = \App\Models\User::where('status', 1)->where('user_type', 1)->select('id', 'name')->latest()->get();
  $projects = \App\Models\ClientProject::select('id', 'title')->latest()->get();
@endphp
<div class="modal fade" id="tasksModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form action="#" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Add Task</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">

          <div class="row">
            <div class="col-md-12">
                <label>Task <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" placeholder="" required>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
                <label>Project <span class="text-danger">*</span></label>
                <select class="form-control modal-select2" name="project_id" required>
                  <option value="">Select Project</option>
                  @foreach($projects as $project)
                    <option value="{{ $project->id }}">{{ $project->title }}</option>
                  @endforeach
                </select>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Assigned To <span class="text-danger">*</span></label>
                <select class="form-control select2" name="employee_id" required>
                  <option value="">Select Employee</option>
                  @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Priority <span class="text-danger">*</span></label>
                <select class="form-control" name="priority" required>
                  <option value="high">High</option>
                  <option value="medium" selected>Medium</option>
                  <option value="low">Low</option>
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Due Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="due_date" min="{{ date('Y-m-d') }}" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Status <span class="text-danger">*</span></label>
                <select class="form-control" name="status" required>
                  <option value="1">To Do</option>
                  <option value="2">In Progress</option>
                  <option value="3">Done</option>
                </select>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label>Task Description <span class="text-danger">*</span></label>
            <textarea class="form-control summernote" name="task" rows="5" placeholder="Enter task description"></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Save Task</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>