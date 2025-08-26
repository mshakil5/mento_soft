@extends('frontend.master')

@section('content')

<section class="default contact-section wow fadeIn"
    style="background-image: url(&quot;../images/pattern-1.svg&quot;),linear-gradient(61deg, rgb(12, 29, 77) 46%, rgb(255, 163, 15) 94%); background-attachment: fixed;">
    <div class="container">
        <div class="row px-2 justify-content-center">
            <div class="col-lg-6 text-light">
                <div class="text-center mb-4">
                    <h2 class="text-light text-uppercase fw-bold title-font wow bounce mb-0 secTtile">
                       Login
                    </h2>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('client.login') }}" method="POST" class="form-style fadeInUp">
                    @csrf
                    <div class="row">

                        <div class="col-12 form-group mb-3">
                            <input type="email" name="email" class="form-control"
                                   placeholder="Email *" value="{{ old('email') }}" required>
                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-12 form-group mb-3">
                            <input type="password" name="password" class="form-control"
                                   placeholder="Password *" required>
                            @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-12 form-group mb-3 d-flex justify-content-between align-items-center d-none">
                            <div>
                                <input type="checkbox" name="remember" id="remember">
                                <label for="remember" class="text-white">Remember Me</label>
                            </div>
                            <a href="{{ route('password.request') }}" class="text-light text-decoration-underline">Forgot Password?</a>
                        </div>

                        <div class="col-12 form-group">
                            <button type="submit" id="submit-btn"
                                    class="mt-4 border-0 d-block rounded-3 w-100 fs-5 text-uppercase btn-theme">
                                Log In
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection