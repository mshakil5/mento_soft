@extends('user.master')

@section('user-content')
    <h1 class="h2">Welcome, {{ auth()->user()->name }}</h1>

    <div class="row mt-4">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm bg-transparent border-light text-light">
                <div class="card-body">
                    <h5 class="card-title">Projects</h5>
                    <p class="card-text">View and manage your projects.</p>
                    <a href="#" class="btn btn-theme">Go to Projects</a>
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