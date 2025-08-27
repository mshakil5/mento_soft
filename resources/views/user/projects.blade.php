@extends('user.master')

@section('user-content')
<div class="row px-2">
    <div class="col-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        <div class="card text-light shadow-sm mb-4 form-style fadeInUp">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table text-light mb-0 align-middle table-hover table-striped">
                        <thead class="bg-secondary text-dark">
                            <tr>
                                <th>Project</th>
                                <th>Start Date</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Amount (£)</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($projects as $index => $project)
                                @php
                                    $statuses = [1=>'Planned',2=>'In Progress',3=>'Blocked',4=>'Done'];
                                    $statusClasses = [1=>'bg-secondary',2=>'bg-primary',3=>'bg-warning text-dark',4=>'bg-success'];
                                    $startDate = $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d-m-Y') : '-';
                                    $dueDate = $project->due_date ? \Carbon\Carbon::parse($project->due_date)->format('d-m-Y') : '-';
                                @endphp
                                <tr class="bg-dark border-top">
                                    <td>{{ $project->title }}</td>
                                    <td>{{ $startDate }}</td>
                                    <td>{{ $dueDate }}</td>
                                    <td>
                                        <span class="badge {{ $statusClasses[$project->status] ?? 'bg-secondary' }}">
                                            {{ $statuses[$project->status] ?? 'Unknown' }}
                                        </span>
                                    </td>
                                    <td>£{{ number_format($project->amount, 0) }}</td>
                                    <td>
                                      <button type="button" class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#projectModal-{{ $project->id }}">
                                          View
                                      </button>

                                      <div class="modal fade" id="projectModal-{{ $project->id }}" tabindex="-1" aria-labelledby="projectModalLabel-{{ $project->id }}" aria-hidden="true">
                                          <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                              <div class="modal-content">
                                                  <div class="modal-header">
                                                      <h5 class="modal-title" id="projectModalLabel-{{ $project->id }}">{{ $project->title }}</h5>
                                                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                  </div>

                                                  <div class="modal-body">
                                                      <div class="p-3 border rounded bg-light">

                                                          <div class="row">
                                                              <div class="col-6">
                                                                  <h6>Project Concept / Idea</h6>
                                                                  <p>{!! $project->description ?? 'No description available.' !!}</p>

                                                                  <h6>Additional Information</h6>
                                                                  <p>{!! $project->additional_info ?? '-' !!}</p>

                                                                  <h6>Attachments / Updates</h6>
                                                                  @if($project->recentUpdates->count())
                                                                      <div class="d-flex flex-wrap gap-2">
                                                                          @foreach($project->recentUpdates as $update)
                                                                              @if($update->attachment)
                                                                                  <a href="{{ asset('images/recent-updates/'.$update->attachment) }}" download class="badge bg-light text-dark border">
                                                                                      <i class="fas fa-paperclip text-info"></i> {{ $update->title ?? 'Untitled' }}
                                                                                  </a>
                                                                              @else
                                                                                  <span class="badge bg-secondary">
                                                                                      <i class="fas fa-paperclip"></i> {{ $update->title ?? 'Untitled' }}
                                                                                  </span>
                                                                              @endif
                                                                          @endforeach
                                                                      </div>
                                                                  @else
                                                                      <p class="text-muted">No updates yet.</p>
                                                                  @endif
                                                              </div>

                                                              <div class="col-6">
                                                                  <div class="d-flex justify-content-between align-items-center mb-2">
                                                                      <h6 class="mb-0">Linked Tasks</h6>
                                                                      <button type="button" class="btn btn-sm btn-success" 
                                                                              data-bs-toggle="modal" 
                                                                              data-bs-target="#createTaskModal" 
                                                                              data-project-id="{{ $project->id }}">
                                                                          + New Task
                                                                      </button>
                                                                  </div>

                                                                  @if($project->tasks->count())
                                                                      <div class="list-group">
                                                                          @foreach($project->tasks as $task)
                                                                              <div class="list-group-item mb-2">
                                                                                  <strong>{!! $task->task !!}</strong>
                                                                                  <div class="small text-muted mt-1">
                                                                                      <span><strong>Due:</strong> {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d-m-Y') : '-' }}</span> &middot;
                                                                                      <span><strong>Status:</strong> {{ [1=>'To Do',2=>'In Progress',3=>'Done'][$task->status] ?? '-' }}</span> &middot;
                                                                                      <span><strong>Priority:</strong>
                                                                                          <span class="badge {{ ['high'=>'bg-danger','medium'=>'bg-warning','low'=>'bg-info'][$task->priority] ?? 'bg-secondary' }}">
                                                                                              {{ ucfirst($task->priority) }}
                                                                                          </span>
                                                                                      </span> &middot;
                                                                                  </div>
                                                                              </div>
                                                                          @endforeach
                                                                      </div>
                                                                  @else
                                                                      <p class="text-muted">No tasks yet.</p>
                                                                  @endif
                                                              </div>
                                                          </div>

                                                      </div>
                                                  </div>

                                                  <div class="modal-footer">
                                                      <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                                                  </div>

                                              </div>
                                          </div>
                                      </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-3">No projects found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">
            {{ $projects->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
  var createTaskModal = document.getElementById('createTaskModal');
  $('#createTaskModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget);
      var projectId = button.data('project-id');
      console.log(projectId);
      if (projectId) {
          $('#projectSelect').val(projectId);
      } else {
          $('#projectSelect').val('');
      }
  });
</script>
@endsection