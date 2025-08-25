<div class="modal fade" id="taskModal-{{ $row->id }}" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ $row->clientProject->title ?? '' }}</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="list-group-item mb-2">
          <strong>{!! $row->task ?? '' !!}</strong>

          <div class="small text-muted mt-1">
            <span>
              <strong>Due:</strong> 
              {{ $row->due_date ? \Carbon\Carbon::parse($row->due_date)->format('d-m-Y') : '' }}
            </span> &middot; 
            <span>
              <strong>Status:</strong> 
              @php
                $statuses = [
                    1 => ['label' => 'To Do'],
                    2 => ['label' => 'In Progress'],
                    3 => ['label' => 'Done']
                ];
              @endphp
              {{ $statuses[$row->status]['label'] ?? '' }}
            </span> &middot; 
            <span>
              <strong>Priority:</strong> 
              @php
                $priorityColors = [
                    'high' => 'bg-danger',
                    'medium' => 'bg-warning',
                    'low' => 'bg-info'
                ];
              @endphp
              <span class="badge {{ $priorityColors[$row->priority] ?? 'bg-secondary' }}">
                {{ ucfirst($row->priority ?? '') }}
              </span>
            </span> &middot; 
            <span>
              <strong>Assigned to:</strong> {{ $row->employee->name ?? '' }}
            </span> &middot;
            <span>
              <strong>Project:</strong> {{ $row->clientProject->title ?? '' }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>