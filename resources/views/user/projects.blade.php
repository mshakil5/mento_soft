@extends('user.master')

@section('user-content')

<style>
  .custom-table-bg th,
  .custom-table-bg td,
  .custom-table-bg tbody {
      background-color: transparent !important;
  }
</style>

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
        <div class="card text-light shadow-sm mb-4 form-style fadeInUp border-light">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle custom-table-bg">
                        <thead>
                            <tr>
                                <th class="text-light">Project</th>
                                <th class="text-light">Start Date</th>
                                <th class="text-light">Due Date</th>
                                <th class="text-light">Status</th>
                                <th class="text-light">Amount (£)</th>
                                <th class="text-light">Action</th>
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
                                <tr class="border-top">
                                    <td class="text-light">{{ $project->title }}</td>
                                    <td class="text-light">{{ $startDate }}</td>
                                    <td class="text-light">{{ $dueDate }}</td>
                                    <td class="text-light">
                                        <span class="badge {{ $statusClasses[$project->status] ?? 'bg-secondary' }}">
                                            {{ $statuses[$project->status] ?? 'Unknown' }}
                                        </span>
                                    </td>
                                    <td class="text-light">£{{ number_format($project->amount, 0) }}</td>
                                    <td class="text-light">
                                      <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#projectModal-{{ $project->id }}">
                                          View
                                      </button>

                                      <div class="modal fade" id="projectModal-{{ $project->id }}" tabindex="-1" aria-labelledby="projectModalLabel-{{ $project->id }}" aria-hidden="true">
                                          <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                              <div class="modal-content">
                                                  <div class="modal-header">
                                                      <h5 class="modal-title" id="projectModalLabel-{{ $project->id }}">{{ $project->title }}</h5>
                                                      <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
                                                  </div>

                                                  <div class="modal-body">
                                                      <div class="p-3 border rounded bg-light">

                                                          <div class="row">
                                                              <div class="col-12">
                                                                  <h5>Project Concept / Idea</h5>
                                                                  <p>{!! $project->description ?? '-' !!}</p>

                                                                  <h5>Additional Information</h5>
                                                                  <p>{!! $project->additional_info ?? '-' !!}</p>

                                                                  <h5>Attachments</h5>
                                                                  @if($project->recentUpdates->count())
                                                                      <div class="d-flex flex-wrap gap-2">
                                                                          @foreach($project->recentUpdates as $update)
                                                                              @if($update->attachment)
                                                                                  <a href="{{ asset('images/recent-updates/'.$update->attachment) }}" download class="badge bg-light text-dark border">
                                                                                      <i class="fas fa-paperclip text-info"></i> {{ basename($update->attachment) }}
                                                                                  </a>
                                                                              @endif
                                                                          @endforeach
                                                                      </div>
                                                                  @endif
                                                              </div>
                                                              @if($project->services->count())
                                                              <div class="col-6 d-none">
                                                                  <h5>Paid Services</h5>
                                                                  @if($project->services->count())
                                                                    <table class="table table-striped table-bordered text-light">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Service</th>
                                                                                <th>Start Date</th>
                                                                                <th>Amount</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach($project->services as $service)
                                                                                <tr>
                                                                                    <td>{{ $service->serviceType->name ?? '-' }} ({{ $service->type == 1 ? 'In House' : 'Third Party' }})</td>
                                                                                    <td>{{ $service->start_date ? date('d M Y', strtotime($service->start_date)) : '-' }}</td>
                                                                                    <td>{{ number_format($service->amount, 2) }}</td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                @endif
                                                              </div>
                                                              @endif

                                                              <div class="col-6 d-none">
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
                                              </div>
                                          </div>
                                      </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-3 text-light">No projects found.</td>
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