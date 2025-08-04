@extends('layouts.auth')

@section('content')
<div class="card card-outline card-secondary">
    <div class="card-header text-center">
        <h1><b>Register</b></h1>
    </div>
    <div class="card-body">
        <p class="login-box-msg">Register a new membership</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="input-group mb-3">
                <input id="name" type="text"
                       class="form-control @error('name') is-invalid @enderror"
                       name="name" value="{{ old('name') }}"
                       required autocomplete="name" autofocus
                       placeholder="Full Name">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-user"></span>
                    </div>
                </div>
            </div>
            @error('name')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

            <div class="input-group mb-3">
                <input id="email" type="email"
                       class="form-control @error('email') is-invalid @enderror"
                       name="email" value="{{ old('email') }}"
                       required autocomplete="email"
                       placeholder="Email">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-envelope"></span>
                    </div>
                </div>
            </div>
            @error('email')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

            <div class="input-group mb-3">
                <input id="password" type="password"
                       class="form-control @error('password') is-invalid @enderror"
                       name="password" required autocomplete="new-password"
                       placeholder="Password">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                    </div>
                </div>
            </div>
            @error('password')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

            <div class="input-group mb-3">
                <input id="password-confirm" type="password"
                       class="form-control"
                       name="password_confirmation" required
                       autocomplete="new-password"
                       placeholder="Confirm Password">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                    </div>
                </div>
            </div>

            <div class="social-auth-links text-center mt-2 mb-3">
                <button type="submit" class="btn btn-secondary btn-block">Register</button>
            </div>

            <div class="text-center">
                <a href="{{ route('login') }}" class="text-center">Already have an account? Login</a>
            </div>
        </form>
    </div>
</div>
@endsection