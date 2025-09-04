@php
  $clients = \App\Models\Client::where('status', 1)->select('id', 'business_name')->latest()->get();
  $employees = \App\Models\User::where('status', 1)->where('user_type', 1)->select('id', 'name')->get();
@endphp

<div class="modal fade ajaxModal" id="createProjectModal" tabindex="-1" role="dialog" aria-labelledby="createProjectLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createProjectLabel">Create New Project</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
      </div>
      <div class="modal-body">
        <form class="ajaxForm" action="{{ route('admin.client-projects.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Client <span class="text-danger">*</span></label>
                <select class="form-control select2" name="client_id" required>
                  <option value="">Select Client</option>
                  @foreach($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->business_name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Project Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="title" placeholder="Enter project title" required>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 d-none">
              <div class="form-group">
                <label>Domain</label>
                <input type="text" class="form-control" name="domain" placeholder="Enter domain">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Project URL</label>
                <input type="url" class="form-control" name="project_url" placeholder="Enter project URL">
              </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Project Manager <span class="text-danger">*</span></label>
                    <select name="project_manager" class="form-control select2" required>
                        <option value="">Select Project Manager</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6 d-none">
              <div class="form-group">
                <label>Tech Stack</label>
                <input type="text" class="form-control" name="tech_stack" placeholder="PHP,Laravel,MySQL">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Status <span class="text-danger">*</span></label>
                <select class="form-control" name="status" required>
                  <option value="1">Planned</option>
                  <option value="2">In Progress</option>
                  <option value="3">Blocked</option>
                  <option value="4">Done</option>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Start Date</label>
                <input type="date" class="form-control" name="start_date" value="{{ date('Y-m-d') }}">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Due Date</label>
                <input type="date" class="form-control" name="due_date">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Amount(£)</label>
                <input type="text" class="form-control" name="amount" placeholder="Enter amount">
              </div>
            </div>
          </div>

          <div class="form-group">
            <label>Project Concept / Idea</label>
            <textarea class="form-control summernote" name="description" rows="4" placeholder="Enter project description"></textarea>
          </div>

          <div class="form-group">
            <label>Additional Info</label>
            <textarea class="form-control summernote" name="additional_info" rows="4" placeholder="Enter additional information"></textarea>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save Project</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>