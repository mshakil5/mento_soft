@extends('layouts.auth')

@section('content')
<div class="card card-outline card-secondary">
    <div class="card-header text-center">
        <h1><b>Login</b></h1>
    </div>
    <div class="card-body">
        <p class="login-box-msg">Sign in to start your session</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="input-group mb-3">
                <input id="email" type="email"
                       class="form-control @error('email') is-invalid @enderror"
                       name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
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
                       name="password" required autocomplete="current-password"
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

            <div class="social-auth-links text-center mt-2 mb-3">
                <button type="submit" class="btn btn-secondary btn-block">Sign In</button>
            </div>

            <div class="text-center">
                <a href="{{ route('password.request') }}">Forgot Your Password?</a>
            </div>
        </form>
    </div>
</div>
@endsection