@extends('admin.master')

@section('content')
<section class="content pt-3">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h5>{{ $task->clientProject->title ?? '' }}</h5>
                @can('edit tasks')
                <a href="{{ route('client-projects-task.edit-page', $task->id) }}" class="ml-2 text-info" title="Edit Task">
                    <i class="fas fa-edit"></i>
                </a>
                @endcan
            </div>

            <div class="card-body">
                <div class="list-group-item mb-3">
                    <strong>{!! $task->task ?? '' !!}</strong>
                    <div class="small text-muted mt-1">
                        <span><strong>Due:</strong> {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d-m-Y') : '' }}</span> &middot;
                        <span><strong>Status:</strong> {{ [1=>'To Do',2=>'In Progress',3=>'Done'][$task->status] ?? '' }}</span> &middot;
                        <span><strong>Priority:</strong> 
                            <span class="badge {{ ['high'=>'bg-danger','medium'=>'bg-warning','low'=>'bg-info'][$task->priority] ?? 'bg-secondary' }}">
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
                        </span> &middot;
                        <span><strong>Created by:</strong> {{ $task->creator->name ?? '-' }}</span> &middot;
                        @if($task->is_confirmed == 1)
                            <span class="badge bg-success ml-2">
                                <i class="fas fa-check-circle"></i> Confirmed by Client
                            </span>
                        @endif
                        <span><strong>Project:</strong> {{ $task->clientProject->title ?? '' }}</span>
                    </div>
                </div>

                <div class="card direct-chat direct-chat-primary w-100" style="max-height:400px; overflow-y:auto;">
                    <div class="card-header"><h3 class="card-title">Conversation</h3></div>
                    <div class="card-body">
                        <div class="direct-chat-messages" id="taskMessages">
                            {!! view('admin.client-projects.partials.task_messages', ['messages' => $task->messages->sortBy('created_at')])->render() !!}
                        </div>
                    </div>
                    <div class="card-footer">
                        <form id="taskMessageForm">
                            <div class="input-group">
                                <input type="text" name="message" placeholder="Type Message ..." class="form-control" required>
                                <span class="input-group-append">
                                    <button type="submit" class="btn btn-primary">Send</button>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card direct-chat direct-chat-secondary w-100 mt-3">
                    <div class="card-header"><h3 class="card-title">Task Edit History</h3></div>
                    <div class="card-body p-3">
                        @include('admin.client-projects.partials.task_timeline', ['task' => $task])
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Back
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
<script>
  $(document).ready(function () {
      var csrfToken = $('meta[name="csrf-token"]').attr('content');
      var taskId = {{ $task->id }};

      $('#taskMessageForm').on('submit', function(e){
          e.preventDefault();
          var message = $(this).find('input[name="message"]').val().trim();
          if (!message) return;

          $.post('/admin/tasks/' + taskId + '/messages', { message: message, _token: csrfToken }, function(res) {
              $('#taskMessages').html(res.html);
              $('#taskMessageForm input[name="message"]').val('');
              var chat = $('#taskMessages');
              chat.scrollTop(chat[0].scrollHeight);
          });
      });
  });
</script>
@endsection