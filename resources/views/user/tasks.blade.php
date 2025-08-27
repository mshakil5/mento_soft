@extends('user.master')

@section('user-content')
<div class="row px-2">
    <div class="col-12">
        <div class="modal fade" id="createTaskModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content card-outline card-secondary">
                    <div class="modal-header">
                        <h5 class="modal-title">Create New Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('tasks.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Project <span class="text-danger">*</span></label>
                                <select class="form-select bg-light text-dark" name="project_id" required>
                                    <option value="">-- Select Project --</option>
                                    @foreach($clientProjects as $project)
                                        <option value="{{ $project->id }}">{{ $project->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Task <span class="text-danger">*</span></label>
                                <textarea id="new-task-description" name="task" class="form-control bg-light text-dark"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Priority <span class="text-danger">*</span></label>
                                <select class="form-select bg-light text-dark" name="priority" required>
                                    <option value="high">High</option>
                                    <option value="medium">Medium</option>
                                    <option value="low">Low</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Due Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control bg-light text-dark" name="due_date" required min="{{ date('Y-m-d') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select bg-light text-dark" name="status">
                                    <option value="1">To Do</option>
                                    <option value="2">In Progress</option>
                                    <option value="3">Done</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success float-end">Create Task</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
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
            <div class="d-flex justify-content-end mb-3">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createTaskModal">
                    + New Task
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table text-light mb-0 align-middle">
                        <thead class="bg-secondary text-dark">
                            <tr>
                                <th>Project</th>
                                <th>Task</th>
                                <th>Priority</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Task</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tasks as $index => $task)
                                <tr class="bg-dark border-top">
                                    <td>{{ $task->clientProject->title ?? '' }}</td>
                                    <td>{!! $task->task ?? '' !!}</td>
                                    <td>{{ ucfirst($task->priority) }}</td>
                                    <td>{{ $task->employee->name ?? '' }}</td>
                                    <td>
                                        @php
                                            $statusLabels = [
                                                1 => 'To Do',
                                                2 => 'In Progress',
                                                3 => 'Done',
                                            ];
                                            $statusClasses = [
                                                1 => 'bg-warning text-dark',
                                                2 => 'bg-primary',
                                                3 => 'bg-success',
                                            ];
                                        @endphp
                                        <span class="badge {{ $statusClasses[$task->status] ?? '' }}">
                                            {{ $statusLabels[$task->status] ?? '' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#taskModal-{{ $task->id }}">
                                            View
                                        </button>
                                        <div class="modal fade task-modal" id="taskModal-{{ $task->id }}" tabindex="-1" aria-hidden="true">
                                          <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content card-outline card-secondary">
                                              <div class="modal-header">
                                                <h5 class="modal-title">{{ $task->clientProject->title ?? '' }}</h5>
                                                <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal"></button>
                                              </div>

                                              <div class="modal-body">
                                                <div class="list-group-item mb-3">
                                                    <strong>{!! $task->task ?? 'N/A' !!}</strong>
                                                    <div class="small text-muted mt-1">
                                                        <span><strong>Due:</strong> {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d-m-Y') : 'N/A' }}</span> &middot;
                                                        <span><strong>Status:</strong>
                                                            <span class="badge 
                                                                {{ $task->status == 1 ? 'bg-secondary' : '' }}
                                                                {{ $task->status == 2 ? 'bg-primary' : '' }}
                                                                {{ $task->status == 3 ? 'bg-success' : '' }}">
                                                                {{ [1=>'To Do',2=>'In Progress',3=>'Done'][$task->status] ?? 'N/A' }}
                                                            </span>
                                                        </span> &middot;
                                                        <span><strong>Priority:</strong> 
                                                            <span class="badge 
                                                                {{ $task->priority == 'high' ? 'bg-danger' : '' }}
                                                                {{ $task->priority == 'medium' ? 'bg-warning text-dark' : '' }}
                                                                {{ $task->priority == 'low' ? 'bg-info text-dark' : '' }}">
                                                                {{ ucfirst($task->priority ?? 'N/A') }}
                                                            </span>
                                                        </span> &middot;
                                                        <span><strong>Project:</strong> {{ $task->clientProject->title ?? 'N/A' }}</span>
                                                    </div>
                                                </div>

                                                <!-- Chat -->
                                                <div class="card direct-chat direct-chat-primary w-100" style="max-height:400px; overflow-y:auto;">
                                                  <div class="card-header">
                                                    <h3 class="card-title">Conversation</h3>
                                                  </div>

                                                  <div class="card-body">
                                                      <div class="direct-chat-messages" id="taskModalMessages-{{ $task->id }}"></div>
                                                  </div>

                                                  <div class="card-footer p-2">
                                                      <form class="taskMessageForm d-flex" data-task-id="{{ $task->id }}">
                                                          <input type="text" name="message" class="form-control me-2" placeholder="Type a message..." required>
                                                          <button type="submit" class="btn btn-success">Send</button>
                                                      </form>
                                                  </div>

                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                        <button type="button" class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#taskEditModal-{{ $task->id }}">
                                            Edit
                                        </button>
                                        <div class="modal fade" id="taskEditModal-{{ $task->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content card-outline card-secondary">
                                                    <div class="modal-header text-center w-100">
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ route('tasks.update', $task->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="mb-3">
                                                                <label class="form-label">Priority <span class="text-danger">*</span></label>
                                                                <select class="form-select bg-light text-dark" name="priority">
                                                                    <option value="high" {{ $task->priority=='high' ? 'selected' : '' }}>High</option>
                                                                    <option value="medium" {{ $task->priority=='medium' ? 'selected' : '' }}>Medium</option>
                                                                    <option value="low" {{ $task->priority=='low' ? 'selected' : '' }}>Low</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Due Date <span class="text-danger">*</span></label>
                                                                <input type="date" class="form-control bg-light text-dark" name="due_date" value="{{ $task->due_date ?? '' }}" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                                                <select class="form-select bg-light text-dark" name="status">
                                                                    <option value="1" {{ $task->status==1 ? 'selected' : '' }}>To Do</option>
                                                                    <option value="2" {{ $task->status==2 ? 'selected' : '' }}>In Progress</option>
                                                                    <option value="3" {{ $task->status==3 ? 'selected' : '' }}>Done</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Task <span class="text-danger">*</span></label>
                                                                <textarea id="description-{{ $task->id }}" name="description">{{ $task->task }}</textarea>
                                                            </div>
                                                            <button type="submit" class="btn btn-success float-end">Update Task</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">No tasks found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $tasks->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

@endsection
@section('script')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>
<script>
  ClassicEditor.create(document.querySelector('#new-task-description'))
    .catch(error => console.error(error));

  document.querySelectorAll('[id^="description-"]').forEach((el) => {
      ClassicEditor.create(el).catch(error => console.error(error));
  });
</script>

<script>
    $(document).on('click', '[data-bs-target^="#taskModal-"]', function() {
        let modalId = $(this).data('bs-target');
        let taskId = modalId.split('-')[1];
        
        $.get('/user/tasks/' + taskId + '/messages', function(res) {
            $('#taskModalMessages-' + taskId).html(res.html);
            let chat = $('#taskModalMessages-' + taskId);
            chat.scrollTop(chat[0].scrollHeight);
        });
    });

    $(document).on('submit', '.taskMessageForm', function(e) {
        e.preventDefault();
        let form = $(this);
        let taskId = form.data('task-id');
        let message = form.find('input[name="message"]').val().trim();

        if (!message) return;

        $.post('/user/tasks/' + taskId + '/messages', {
            message: message,
            _token: '{{ csrf_token() }}'
        }, function(res) {
            form.find('input[name="message"]').val('');
            $('#taskModalMessages-' + taskId).html(res.html);
            let chat = $('#taskModalMessages-' + taskId);
            chat.scrollTop(chat[0].scrollHeight);
        });
    });
</script>
@endsection