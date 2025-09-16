<div class="modal fade" id="detailsModal-{{ $row->id }}" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel-{{ $row->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title" id="detailsModalLabel-{{ $row->id }}">{{ $row->title }}</h2>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="p-3 border rounded bg-light">
          <div class="row">
            <div class="col-6">
              <h5>Project Concept / Idea</h5>
              <p>{!! $row->description !!}</p>

              <h5>Additional Information</h5>
              <p>{!! $row->additional_info !!}</p>
              
              <h5 class="d-flex justify-content-between align-items-center">
                  Attachments
                  <button type="button" class="btn btn-sm btn-success" 
                          data-toggle="modal" 
                          data-target="#quickAddUpdateModal-{{ $row->id }}">
                      <i class="fas fa-plus"></i> Add attachment
                  </button>
              </h5>
              @if($row->recentUpdates->count())
                  <div class="d-flex flex-wrap gap-2">
                      @foreach($row->recentUpdates as $update)
                          @if($update->attachment)
                              <a href="{{ asset('images/recent-updates/'.$update->attachment) }}" 
                                download 
                                class="badge bg-light text-dark border">
                                <i class="fas fa-paperclip text-info"></i> {{ basename($update->attachment) }}
                              </a>
                          @endif
                      @endforeach
                  </div>
              @else
                  <p class="text-muted">No attachments yet.</p>
              @endif
            </div>

            <div class="col-6">
                <h5>Linked Tasks
                  @can('add task')
                  <button type="button" class="btn btn-sm btn-success float-right" 
                          data-toggle="modal" 
                          data-target="#tasksModal" 
                          data-project-id="{{ $row->id }}"
                          onclick="openTaskModal({{ $row->id }})">
                      <i class="fas fa-plus"></i> Add New Task
                  </button>
                  @endcan
                </h5>
                @if($row->tasks->count())
                    <div class="list-group">
                        @foreach($row->tasks as $task)
                            <div class="list-group-item mb-2 d-flex justify-content-between align-items-start">
                                <div>
                                    <a href="{{ route('tasks.show', $task->id) }}" class="text-dark" style="text-decoration:none;">
                                        <strong>{{ $task->title ?? '' }}</strong> - {!! $task->task ?? '' !!}
                                    </a>
                                    <div class="small text-muted mt-1">
                                        <span><strong>Due:</strong> {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d-m-Y') : '' }}</span> &middot; 
                                        <span><strong>Status:</strong> 
                                            @php
                                                $statuses = [
                                                    1 => ['label' => 'To Do'],
                                                    2 => ['label' => 'In Progress'],
                                                    3 => ['label' => 'Done']
                                                ];
                                            @endphp
                                            {{ $statuses[$task->status]['label'] ?? '' }}
                                        </span> &middot; 
                                        <span><strong>Priority:</strong> 
                                            @php
                                                $priorityColors = [
                                                    'high' => 'bg-danger',
                                                    'medium' => 'bg-warning',
                                                    'low' => 'bg-info'
                                                ];
                                            @endphp
                                            <span class="badge {{ $priorityColors[$task->priority] ?? 'bg-secondary' }}">
                                                {{ ucfirst($task->priority ?? '') }}
                                            </span>
                                        </span> &middot; 
                                        <span><strong>Assigned to:</strong> 
                                            @if($task->employee)
                                                {{ $task->employee->name }}
                                            @else
                                                <span class="text-danger font-weight-bold">
                                                    <i class="fas fa-exclamation-circle"></i> Needs Assignment
                                                </span>
                                            @endif
                                        </span>
                                        <span><strong>Created by:</strong> {{ $task->creator->name ?? '-' }}</span>
                                        
                                        @if($task->is_confirmed == 1)
                                        <span class="badge bg-success ml-2">
                                            <i class="fas fa-check-circle"></i> Confirmed by Client
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                @can('edit task')                
                                <div>
                                    <a href="{{ route('client-projects-task.edit-page', $task->id) }}" class="text-info" title="Edit Task">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                                 @endcan
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-2 text-center">
                        <a href="{{ route('client-projects.tasks', $row->id) }}" class="btn btn-sm btn-secondary">View All Tasks</a>
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

<div class="modal fade" id="quickAddUpdateModal-{{ $row->id }}" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content card-outline card-secondary">
      <div class="modal-header">
        <h5 class="modal-title">Add Attachment for "{{ $row->title }}"</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form class="ajaxForm" action="{{ route('client-projects.updates.store', $row->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="project_id" value="{{ $row->id }}">

            <div class="mb-3">
                <label class="form-label">Attachment <span class="text-danger">*</span></label>
                <input type="file" class="form-control" name="attachment" required>
            </div>

            <button type="submit" class="btn btn-success float-end">Add Attachment</button>
        </form>
      </div>
    </div>
  </div>
</div>