<div class="modal fade task-modal" id="taskModal-{{ $row->id }}" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ $row->clientProject->title ?? '' }}</h5>
        <a href="{{ route('client-projects-task.edit-page', $row->id) }}" class="ml-2 text-info" title="Edit Task">
            <i class="fas fa-edit"></i>
        </a>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <div class="modal-body">
        <!-- Task Info -->
        <div class="list-group-item mb-3">
          <strong>{!! $row->task ?? '' !!}</strong>
          <div class="small text-muted mt-1">
            <span><strong>Due:</strong> {{ $row->due_date ? \Carbon\Carbon::parse($row->due_date)->format('d-m-Y') : '' }}</span> &middot;
            <span><strong>Status:</strong> {{ [1=>'To Do',2=>'In Progress',3=>'Done'][$row->status] ?? '' }}</span> &middot;
            <span><strong>Priority:</strong> 
              <span class="badge {{ ['high'=>'bg-danger','medium'=>'bg-warning','low'=>'bg-info'][$row->priority] ?? 'bg-secondary' }}">
                {{ ucfirst($row->priority ?? '') }}
              </span>
            </span> &middot;
            <span><strong>Assigned to:</strong> 
                @if($row->employee)
                    {{ $row->employee->name }}
                @else
                    <span class="text-danger font-weight-bold">
                        <i class="fas fa-exclamation-circle"></i> Needs Assignment
                    </span>
                @endif
            </span> 
            &middot;
            <span><strong>Created by:</strong> {{ $row->creator->name ?? '-' }}</span> &middot;
            @if($row->is_confirmed == 1)
            <span class="badge bg-success ml-2">
                <i class="fas fa-check-circle"></i> Confirmed by Client
            </span>
            @endif
            <span><strong>Project:</strong> {{ $row->clientProject->title ?? '' }}</span>
          </div>
        </div>

        <!-- Chat -->
        <div class="card direct-chat direct-chat-primary w-100" style="max-height:400px; overflow-y:auto;">
          <div class="card-header">
            <h3 class="card-title">Conversation</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
          </div>

          <div class="card-body">
              <div class="direct-chat-messages" id="taskModalMessages-{{ $row->id }}"></div>
          </div>

          <div class="card-footer">
            <form class="taskMessageForm" data-task-id="{{ $row->id }}">
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
          <div class="card-header">
            <h3 class="card-title">Task Edit History</h3>
            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>
          </div>

          <div class="card-body p-3" style="max-height:300px; overflow-y:auto;">
            <div id="taskModalTimeline-{{ $row->id }}">
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>