@extends('frontend.master')

@section('content')
<section class="default contact-section wow fadeIn"
    style="background-image: url('../images/pattern-1.svg'), linear-gradient(61deg, rgb(12, 29, 77) 46%, rgb(255, 163, 15) 94%); background-attachment: fixed;">
    <div class="container">
        <div class="row">

            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-3 d-none d-md-block sidebar vh-100 p-3">
                <div class="position-sticky">
                    <ul class="nav flex-column mt-3">
                        <li class="nav-item mb-2"><a class="nav-link text-light {{ request()->routeIs('user.dashboard') ? 'active' : '' }}" href="{{ route('user.dashboard') }}">Dashboard</a></li>
                        <li class="nav-item mb-2"><a class="nav-link text-light {{ request()->routeIs('user.projects') ? 'active' : '' }}" href="{{ route('user.projects') }}">Projects</a></li>
                        <li class="nav-item mb-2"><a class="nav-link text-light {{ request()->routeIs('user.tasks') ? 'active' : '' }}" href="{{ route('user.tasks') }}">Tasks</a></li>
                        <li class="nav-item mb-2"><a class="nav-link text-light {{ request()->routeIs('user.profile') ? 'active' : '' }}" href="{{ route('user.profile') }}">Profile</a></li>
                        <li class="nav-item mb-2"><a class="nav-link text-light {{ request()->routeIs('user.password') ? 'active' : '' }}" href="{{ route('user.password') }}">Change Password</a></li>
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
                        <li class="nav-item mb-2"><a class="nav-link text-light" href="#">Dashboard</a></li>
                        <li class="nav-item mb-2"><a class="nav-link text-light" href="#">Projects</a></li>
                        <li class="nav-item mb-2"><a class="nav-link text-light" href="#">Tasks</a></li>
                        <li class="nav-item mb-2"><a class="nav-link text-light {{ request()->routeIs('user.profile') ? 'active' : '' }}" href="{{ route('user.profile') }}">Profile</a></li>
                        <li class="nav-item mb-2"><a class="nav-link text-light {{ request()->routeIs('user.password') ? 'active' : '' }}" href="{{ route('user.password') }}">Change Password</a></li>
                        <li class="nav-item mb-2">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="nav-link text-light">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <main class="col-md-9 ms-sm-auto col-lg-9 px-md-4 py-4 text-light">
                @yield('user-content')
            </main>

        </div>
    </div>
</section>
<style>
  .nav-link.active {
      font-weight: bold;
      color: #FF6D33 !important;
  }
</style>

@endsection