@extends('user.master')

@section('user-content')
<div class="row px-2 justify-content-center">
    <div class="col-lg-12 text-light">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('user.password.update') }}" method="POST" class="form-style fadeInUp">
            @csrf
            <div class="row">

                <div class="col-12 form-group mb-3">
                    <input type="password" name="current_password" class="form-control"
                        placeholder="Current Password *" required>
                    @error('current_password') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-12 form-group mb-3">
                    <input type="password" name="password" class="form-control"
                        placeholder="New Password *" required>
                    @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-12 form-group mb-3">
                    <input type="password" name="password_confirmation" class="form-control"
                        placeholder="Confirm Password *" required>
                    @error('password_confirmation') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-6 form-group">
                    <button type="submit" id="submit-btn"
                        class="mt-4 border-0 d-block rounded-3 w-100 fs-5 text-uppercase btn-theme">
                        Change Password
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
@endsection