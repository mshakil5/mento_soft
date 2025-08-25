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
              <h3>Project Concept / Idea</h3>
              <p>{!! $row->description !!}</p>

              <h3>Additional Information</h3>
              <p>{!! $row->additional_info !!}</p>
              
              <h3>Attachments</h3>
              @if($row->recentUpdates->count())
                  <div class="d-flex flex-wrap gap-2">
                      @foreach($row->recentUpdates as $update)
                          @if($update->attachment)
                              <a href="{{ asset('images/recent-updates/'.$update->attachment) }}" 
                                download 
                                class="badge bg-light text-dark border">
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
                <h3>Linked Tasks
                  <button type="button" class="btn btn-sm btn-success float-right" 
                          data-toggle="modal" 
                          data-target="#tasksModal" 
                          data-project-id="{{ $row->id }}"
                          onclick="openTaskModal({{ $row->id }})">
                      <i class="fas fa-plus"></i> Add New Task
                  </button>
                </h3>
                @if($row->tasks->count())
                    <div class="list-group">
                        @foreach($row->tasks as $task)
                            <div class="list-group-item mb-2">
                                <strong>{!! $task->task ?? '' !!}</strong>

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
                                    <span><strong>Assigned to:</strong> {{ $task->employee->name ?? '' }}</span>
                                </div>
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