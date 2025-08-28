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
                                <th>Approved</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tasks as $index => $task)
                                <tr class="bg-dark border-top">
                                    <td>{{ $task->clientProject->title ?? '' }}</td>
                                    <td>{!! $task->task ?? '' !!}</td>
                                    <td>
                                        @if($task->status == 3)
                                            <form action="{{ route('tasks.confirm', $task->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="is_confirmed" value="1" id="confirm-{{ $task->id }}"
                                                        {{ $task->is_confirmed ? 'checked' : '' }} onchange="this.form.submit()">
                                                    <label class="form-check-label" for="confirm-{{ $task->id }}">
                                                        {{ $task->is_confirmed ? 'Approved' : 'No' }}
                                                    </label>
                                                </div>
                                            </form>
                                        @else
                                            <span class="text-muted">Not Completed Yet</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary position-relative" data-bs-toggle="modal" data-bs-target="#taskModal-{{ $task->id }}">
                                            View
                                            @if($task->unread_messages_count > 0)
                                                <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle p-1">
                                                    {{ $task->unread_messages_count }}
                                                </span>
                                            @endif
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
                                        <button type="button" class="btn btn-sm btn-warning position-relative" data-bs-toggle="modal" data-bs-target="#taskEditModal-{{ $task->id }}">
                                            Edit
                                        </button>
                                        <div class="modal fade" id="taskEditModal-{{ $task->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered  modal-lg">
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