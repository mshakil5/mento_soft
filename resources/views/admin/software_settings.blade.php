<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

    <li class="nav-item">
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
        </a>
    </li>

    <a href="{{ route('toggle.sidebar') }}" class="btn btn-info my-2">
        Switch to Frontend Settings <i class="fas fa-arrow-right"></i>
    </a>

    <li class="nav-item">
        <a href="{{ route('client-projects.index') }}" class="nav-link {{ Route::is('client-projects.index') ? 'active' : '' }}">
            <i class="fas fa-project-diagram nav-icon"></i>
            <p>Client Projects</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('employees.index') }}" class="nav-link {{ Route::is('employees.index') ? 'active' : '' }}">
            <i class="fas fa-users nav-icon"></i>
            <p>Employees</p>
        </a>
    </li>

    <li class="nav-item dropdown {{ Route::is('client-types.index') || Route::is('clients.index') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link dropdown-toggle {{ Route::is('client-types.index') || Route::is('clients.index') ? 'active' : '' }}">
            <i class="nav-icon fas fa-cog"></i>
            <p>
                Client <i class="fas fa-angle-left right"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{ route('clients.index') }}" class="nav-link {{ Route::is('clients.index') ? 'active' : '' }}">
                    <i class="fas fa-user nav-icon"></i>
                    <p>Clients</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('client-types.index') }}" class="nav-link {{ Route::is('client-types.index') ? 'active' : '' }}">
                    <i class="fas fa-user-tag nav-icon"></i>
                    <p>Client Types</p>
                </a>
            </li>
        </ul>
    </li>

    <li class="nav-item dropdown {{ Route::is('invoices.index') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link dropdown-toggle {{ Route::is('invoices.index') ? 'active' : '' }}">
            <i class="nav-icon fas fa-file-alt"></i>
            <p>
                Invoice <i class="fas fa-angle-left right"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{ route('invoices.index') }}" class="nav-link {{ Route::is('invoices.index') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice nav-icon"></i>
                    <p>Invoices</p>
                </a>
            </li>
        </ul>
    </li>

    <li class="nav-item" style="margin-top: 200px">
    </li>

  </ul>
</nav>