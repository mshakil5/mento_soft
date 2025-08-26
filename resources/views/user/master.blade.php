@extends('frontend.master')

@section('content')
<section class="default contact-section wow fadeIn"
    style="background-image: url('../images/pattern-1.svg'), linear-gradient(61deg, rgb(12, 29, 77) 46%, rgb(255, 163, 15) 94%); background-attachment: fixed;">
    <div class="container">
        <div class="row">

            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-none d-md-block sidebar vh-100 p-3">
                <div class="position-sticky">
                    <h4 class="text-light mb-4">{{ auth()->user()->name }}</h4>
                    <ul class="nav flex-column">
                        <li class="nav-item mb-2"><a class="nav-link text-light" href="#">Projects</a></li>
                        <li class="nav-item mb-2"><a class="nav-link text-light" href="#">Profile</a></li>
                        <li class="nav-item mb-2"><a class="nav-link text-light" href="#">Change Password</a></li>
                        <li class="nav-item mb-2">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="nav-link text-light">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Mobile Sidebar -->
            <div class="col-12 d-md-none mb-3">
                <button class="navbar-toggler btn btn-theme w-100" type="button" data-bs-toggle="collapse"
                    data-bs-target="#mobileSidebarCollapse">
                    <span class="iconify navbar-toggler-icon txt-ternary" data-icon="charm:menu-hamburger"></span> Menu
                </button>
                <div class="collapse mt-2" id="mobileSidebarCollapse">
                    <ul class="nav flex-column">
                        <li class="nav-item mb-2"><a class="nav-link text-light" href="#">Projects</a></li>
                        <li class="nav-item mb-2"><a class="nav-link text-light" href="#">Profile</a></li>
                        <li class="nav-item mb-2"><a class="nav-link text-light" href="#">Change Password</a></li>
                        <li class="nav-item mb-2">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="nav-link text-light">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4 text-light">
                @yield('user-content')
            </main>

        </div>
    </div>
</section>
@endsection