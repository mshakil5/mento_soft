<div class="table-responsive my-2">
    <table class="table mb-0 align-middle custom-table-bg">
        <thead>
            <tr>
                <th class="text-light">Date</th>
                <th class="text-light">Project</th>
                <th class="text-light">Task</th>
                <th class="text-light">Approved</th>
                <th class="text-light text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tasks as $task)
            <tr class="border-top">
                <td class="text-light">{{ $task->created_at ? date('d F Y', strtotime($task->created_at)) : '' }}</td>
                <td class="text-light">{{ $task->clientProject->title ?? '' }}</td>
                <td class="text-light">{{ $task->title ?? '' }}</td>
                <td class="text-light">
                    @if($task->status == 3)
                      <form action="{{ route('tasks.confirm', $task->id) }}" method="POST">
                          @csrf
                          @method('PUT')
                          <div class="form-check form-switch">
                              <input class="form-check-input" type="checkbox" name="is_confirmed" value="1" id="confirm-{{ $task->id }}"
                                  {{ $task->is_confirmed ? 'checked' : '' }} onchange="this.form.submit()">
                              <label class="form-check-label" for="confirm-{{ $task->id }}">
                                  {{ $task->is_confirmed ? 'Yes' : 'No' }}
                              </label>
                          </div>
                      </form>
                      @elseif($task->status == 2)
                          <span>In Progress</span>
                      @elseif($task->status == 1)
                          <span>To Do</span>
                    @endif
                </td>
                <td class="text-light text-center">
                    <button type="button" class="btn btn-sm btn-primary position-relative" data-bs-toggle="modal" data-bs-target="#taskModal-{{ $task->id }}">
                        View
                        @if($task->unread_messages_count > 0)
                            <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle p-1">
                                {{ $task->unread_messages_count }}
                            </span>
                        @endif
                    </button>

                    @if(!empty($showEdit))
                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#taskEditModal-{{ $task->id }}">
                            Edit
                        </button>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center py-3 text-light">No tasks found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>