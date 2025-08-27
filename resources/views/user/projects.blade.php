@extends('user.master')

@section('user-content')
<div class="row px-2">
    <div class="col-12">
        <div class="card text-light shadow-sm mb-4 form-style fadeInUp">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table text-light mb-0 align-middle table-hover table-striped">
                        <thead class="bg-secondary text-dark">
                            <tr>
                                <th>Project</th>
                                <th>Start Date</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Amount (£)</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($projects as $index => $project)
                                @php
                                    $statuses = [1=>'Planned',2=>'In Progress',3=>'Blocked',4=>'Done'];
                                    $statusClasses = [1=>'bg-secondary',2=>'bg-primary',3=>'bg-warning text-dark',4=>'bg-success'];
                                    $startDate = $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d-m-Y') : '-';
                                    $dueDate = $project->due_date ? \Carbon\Carbon::parse($project->due_date)->format('d-m-Y') : '-';
                                @endphp
                                <tr class="bg-dark border-top">
                                    <td>{{ $project->title }}</td>
                                    <td>{{ $startDate }}</td>
                                    <td>{{ $dueDate }}</td>
                                    <td>
                                        <span class="badge {{ $statusClasses[$project->status] ?? 'bg-secondary' }}">
                                            {{ $statuses[$project->status] ?? 'Unknown' }}
                                        </span>
                                    </td>
                                    <td>£{{ number_format($project->amount, 0) }}</td>
                                    <td>
                                      <button type="button" class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#projectModal-{{ $project->id }}">
                                          View
                                      </button>

                                        <div class="modal fade" id="projectModal-{{ $project->id }}" tabindex="-1" aria-labelledby="projectModalLabel-{{ $project->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content card-outline card-secondary">
                                                    <div class="modal-header border-0">
                                                        <h5 class="modal-title" id="projectModalLabel-{{ $project->id }}">{{ $project->title }}</h5>
                                                        <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p><strong>Description:</strong></p>
                                                        <p>{!! $project->description ?? 'No description available.' !!}</p>
                                                        <hr class="border-light">
                                                        <p><strong>Additional Info:</strong></p>
                                                        <p>{!! $project->additional_info ?? '-' !!}</p>
                                                    </div>
                                                    <div class="modal-footer border-0">
                                                        <button type="button" class="btn btn-sm btn-outline-light" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-3">No projects found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">
            {{ $projects->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection