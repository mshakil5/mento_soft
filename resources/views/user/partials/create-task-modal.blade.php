@php
    $user = auth()->user();
    $clientProjects = \App\Models\ClientProject::where('client_id', $user->client->id)->select('id', 'title')->get();
@endphp

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
                        <select class="form-select bg-light text-dark" name="project_id" required id="projectSelect">
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