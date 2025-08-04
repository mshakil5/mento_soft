@extends('layouts.auth')

@section('content')
<div class="card card-outline card-secondary">
    <div class="card-header text-center">
        <h1><b>Verify Your Email Address</b></h1>
    </div>
    <div class="card-body">
        @if (session('resent'))
            <div class="alert alert-success" role="alert">
                A fresh verification link has been sent to your email address.
            </div>
        @endif

        <p>Before proceeding, please check your email for a verification link.</p>
        <p>If you did not receive the email,</p>

        <form method="POST" action="{{ route('verification.resend') }}">
            @csrf
            <button type="submit" class="btn btn-secondary btn-block">Click here to request another</button>
        </form>
    </div>
</div>
@endsection