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
            <div class="d-flex justify-content-end my-2">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createTaskModal">
                    + New Task
                </button>
            </div>

            <ul class="nav nav-tabs" id="taskTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="todo-tab" data-bs-toggle="tab" data-bs-target="#todo" type="button" role="tab">To Do</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="progress-tab" data-bs-toggle="tab" data-bs-target="#progress" type="button" role="tab">In Progress</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="confirmed-tab" data-bs-toggle="tab" data-bs-target="#confirmed" type="button" role="tab">Confirmed</button>
                </li>
            </ul>

            <div class="tab-content mt-2">
                @php
                    $todos = $tasks->where('status', 1);
                    $inProgress = $tasks->where('status', 2);
                    $confirmed = $tasks->where('status', 3);
                @endphp

                <!-- To Do -->
                <div class="tab-pane fade show active" id="todo" role="tabpanel">
                    @include('user.partials.task_table', ['tasks' => $todos, 'showEdit' => true])
                </div>

                <!-- In Progress -->
                <div class="tab-pane fade" id="progress" role="tabpanel">
                    @include('user.partials.task_table', ['tasks' => $inProgress, 'showEdit' => false])
                </div>

                <!-- Confirmed -->
                <div class="tab-pane fade" id="confirmed" role="tabpanel">
                    @include('user.partials.task_table', ['tasks' => $confirmed, 'showEdit' => false])
                </div>
            </div>
        </div>

        {{ $tasks->links('pagination::bootstrap-5') }}
    </div>
</div>

@foreach($tasks as $task)
    <div class="modal fade task-modal" id="taskModal-{{ $task->id }}" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content card-outline card-secondary">
          <div class="modal-header">
            <h5 class="modal-title">{{ $task->clientProject->title ?? '' }}</h5>
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="list-group-item mb-3">
                <strong>Task Created:</strong> {{ $task->created_at ? date('d F Y', strtotime($task->created_at)) : '' }}
            </div>
            <div class="list-group-item mb-3">
                <strong>Task:</strong> {{ $task->title ?? '' }}
            </div>
            <div class="list-group-item mb-3">
                <strong>{!! $task->task ?? '' !!}</strong>
            </div>

            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Conversation</h5>
                    <span class="badge bg-light text-dark">Active</span>
                </div>

                <div class="card-body bg-light overflow-auto" style="max-height: 350px;">
                    <div class="direct-chat-messages" id="taskModalMessages-{{ $task->id }}"></div>
                </div>

                <div class="card-footer">
                    <form class="taskMessageForm d-flex" data-task-id="{{ $task->id }}">
                        <div class="input-group">
                            <input type="text" name="message" class="form-control" placeholder="Type a message..." required>
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-paper-plane"></i> Send
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

          </div>
        </div>
      </div>
    </div>

    @if($task->status == 1)
        <div class="modal fade" id="taskEditModal-{{ $task->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content card-outline card-secondary">
                    <div class="modal-header text-center w-100">
                        <h5 class="modal-title w-100" id="projectModalLabel-{{ $task->id }}">Edit Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('tasks.update', $task->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                              <label class="form-label">Task <span class="text-danger">*</span></label>
                              <input type="text" class="form-control bg-light text-dark" name="title" value="{{ $task->title }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea id="description-{{ $task->id }}" name="description">{{ $task->task }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-success float-end">Update Task</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

@endsection

@section('script')
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