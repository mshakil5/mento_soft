@extends('layouts.auth')

@section('content')
<div class="card card-outline card-secondary">
    <div class="card-header text-center">
        <h1><b>Confirm Password</b></h1>
    </div>
    <div class="card-body">
        <p class="text-center">Please confirm your password before continuing.</p>

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

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
                <button type="submit" class="btn btn-secondary btn-block">Confirm Password</button>
            </div>

            @if (Route::has('password.request'))
                <div class="text-center">
                    <a href="{{ route('password.request') }}">Forgot Your Password?</a>
                </div>
            @endif
        </form>
    </div>
</div>
@endsection