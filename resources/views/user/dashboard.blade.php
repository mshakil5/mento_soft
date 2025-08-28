@extends('user.master')

@section('user-content')
    <h1 class="h2">Welcome, {{ auth()->user()->name }}</h1>

    <div class="row mt-4">
        <div class="col-md-10 mb-4">
            <div class="card shadow-sm bg-transparent border-light text-light">
                <div class="card-body">
                  <div class="row">
                      <h2 class="card-title">Projects</h2>
                      <p class="card-text">
                          @if($plannedprojectsCount || $ongoingprojectsCount || $doneProjectsCount)
                              You have total {{ $plannedprojectsCount + $ongoingprojectsCount + $doneProjectsCount }} projects
                              (
                              @if($plannedprojectsCount) Planned: {{ $plannedprojectsCount }}@endif
                              @if($plannedprojectsCount && ($ongoingprojectsCount || $doneProjectsCount)), @endif
                              @if($ongoingprojectsCount) In Progress: {{ $ongoingprojectsCount }}@endif
                              @if($ongoingprojectsCount && $doneProjectsCount), @endif
                              @if($doneProjectsCount) Done: {{ $doneProjectsCount }}@endif
                              )
                          @endif
                      </p>
                  </div>
                  <div class="row">
                    <h2 class="card-title mt-3">Tasks</h2>
                      @if ($onGoingTasksCount > 0)
                      <p class="card-text">You have total {{ $onGoingTasksCount }} ongoing tasks</p>
                      @endif
                  </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4 d-none">
            <div class="card shadow-sm bg-transparent border-light text-light">
                <div class="card-body">
                    <h5 class="card-title">Profile</h5>
                    <a href="#" class="btn btn-theme">Edit Profile</a>
                </div>
            </div>
        </div>
    </div>
@endsection