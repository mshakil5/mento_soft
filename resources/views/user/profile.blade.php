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

        <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data" class="fadeInUp">
            @csrf
            <div class="row">

                <div class="col-6 form-group mb-3">
                    <input type="text" name="business_name" class="form-control"
                        placeholder="Business Name *" value="{{ old('business_name', $user->client->business_name ?? '') }}" required>
                    @error('business_name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-6 form-group mb-3">
                    <input type="text" name="primary_contact" class="form-control"
                        placeholder="Primary Contact *" value="{{ old('primary_contact', $user->client->primary_contact ?? '') }}" required>
                    @error('primary_contact') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-6 form-group mb-3">
                    <input type="email" name="email" class="form-control"
                        value="{{ $user->email }}" readonly>
                </div>

                <div class="col-6 form-group mb-3">
                    <input type="text" name="phone1" class="form-control"
                        placeholder="Phone *" value="{{ old('phone1', $user->client->phone1 ?? '') }}" required>
                    @error('phone1') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-6 form-group mb-3 d-none">
                    <input type="password" name="password" class="form-control"
                        placeholder="New Password (leave blank if not changing)">
                    @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-6 form-group mb-3 d-none">
                    <input type="password" name="password_confirmation" class="form-control"
                        placeholder="Confirm Password (leave blank if not changing)">
                    @error('password_confirmation') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-12 form-group mb-3">
                    <textarea name="address" class="form-control" rows="3" placeholder="Address">{{ old('address', $user->client->address ?? '') }}</textarea>
                    @error('address') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-6 form-group">
                    <button type="submit" id="submit-btn"
                        class="mt-4 border-0 d-block rounded-3 w-100 fs-5 text-uppercase btn-theme">
                        Update Profile
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
@endsection