@php
    $user = auth()->user();
    $clientProjects = \App\Models\ClientProject::where('client_id', $user->client->id)->select('id', 'title')->get();
@endphp

<div class="modal fade" id="createTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
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
                        <label class="form-label">Task Title <span class="text-danger">*</span></label>
                        <input class="form-control bg-light text-dark" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Task Description <span class="text-danger">*</span></label>
                        <textarea id="new-task-description" name="task" class="form-control bg-light text-dark"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success float-end">Create Task</button>
                </form>
            </div>
        </div>
    </div>
</div>