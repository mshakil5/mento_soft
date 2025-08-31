<!-- Sidebar -->
<nav class="col-md-2 col-lg-2 d-none d-md-block sidebar vh-100 p-3">
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
</div>