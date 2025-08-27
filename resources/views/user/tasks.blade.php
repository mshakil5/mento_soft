@extends('user.master')

@section('user-content')
<div class="row px-2">
    <div class="col-12">
        <div class="card text-light shadow-sm mb-4 form-style fadeInUp">
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
                                        <div class="modal fade" id="taskModal-{{ $task->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                <div class="modal-content card-outline card-secondary">
                                                    <div class="modal-header">
                                                        <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p><strong>Task:</strong> {!! $task->task ?? '' !!}</p>
                                                        <p><strong>Due Date:</strong> {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d-m-Y') : '' }}</p>
                                                        <p><strong>Status:</strong> {{ [1=>'To Do',2=>'In Progress',3=>'Done'][$task->status] ?? '' }}</p>
                                                        <p><strong>Priority:</strong> 
                                                            <span class="badge {{ ['high'=>'bg-danger','medium'=>'bg-warning','low'=>'bg-info'][$task->priority] ?? 'bg-secondary' }}">
                                                                {{ ucfirst($task->priority ?? '') }}
                                                            </span>
                                                        </p>
                                                        <p><strong>Assigned to:</strong> {{ $task->employee->name ?? '' }}</p>
                                                        <p><strong>Project:</strong> {{ $task->clientProject->title ?? '' }}</p>
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
                                                                <label class="form-label">Priority</label>
                                                                <select class="form-select bg-light text-dark" name="priority">
                                                                    <option value="high" {{ $task->priority=='high' ? 'selected' : '' }}>High</option>
                                                                    <option value="medium" {{ $task->priority=='medium' ? 'selected' : '' }}>Medium</option>
                                                                    <option value="low" {{ $task->priority=='low' ? 'selected' : '' }}>Low</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Due Date</label>
                                                                <input type="date" class="form-control bg-light text-dark" name="due_date" value="{{ $task->due_date ?? '' }}">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Status</label>
                                                                <select class="form-select bg-light text-dark" name="status">
                                                                    <option value="1" {{ $task->status==1 ? 'selected' : '' }}>To Do</option>
                                                                    <option value="2" {{ $task->status==2 ? 'selected' : '' }}>In Progress</option>
                                                                    <option value="3" {{ $task->status==3 ? 'selected' : '' }}>Done</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Task</label>
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
  ClassicEditor
    .create(document.querySelector('#description-{{ $task->id }}'))
    .catch(error => console.error(error));
</script>
@endsection