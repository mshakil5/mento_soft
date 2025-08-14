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
        <a href="{{ route('client-projects.index') }}" class="nav-link {{ Route::is('client-projects.index') || Route::is('client-projects.tasks') || Route::is('client-projects.updates') || Route::is('client-projects.services') || Route::is('client-project-services.details') ? 'active' : '' }}">
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

    <li class="nav-item">
        <a href="{{ route('clients.index') }}" class="nav-link {{ Route::is('clients.index') ? 'active' : '' }}">
            <i class="fas fa-user nav-icon"></i>
            <p>Clients</p>
        </a>
    </li>

    <li class="nav-item dropdown {{ request()->routeIs('invoices.*') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link dropdown-toggle {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-file-alt"></i>
            <p>
                Invoice <i class="fas fa-angle-left right"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{ route('invoices.index') }}" class="nav-link {{ request()->routeIs('invoices.index') ? 'active' : '' }}">
                    <i class="fas fa-list nav-icon"></i>
                    <p>All Invoices</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('invoices.due') }}" class="nav-link {{ request()->routeIs('invoices.due') ? 'active' : '' }}">
                    <i class="fas fa-hourglass-half nav-icon"></i>
                    <p>Due Invoices</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('invoices.received') }}" class="nav-link {{ request()->routeIs('invoices.received') ? 'active' : '' }}">
                    <i class="fas fa-check-circle nav-icon"></i>
                    <p>Received Invoices</p>
                </a>
            </li>
        </ul>
    </li>

    <li class="nav-item">
        <a href="{{ route('admin.addchartofaccount') }}" class="nav-link {{ Route::is('admin.addchartofaccount') ? 'active' : '' }}">
            <i class="fas fa-chart-line nav-icon"></i>
            <p>Chart Of Accounts</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('admin.income') }}" class="nav-link {{ Route::is('admin.income') ? 'active' : '' }}">
            <i class="fas fa-chart-line nav-icon"></i>
            <p>Income</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('admin.expense') }}" class="nav-link {{ Route::is('admin.expense') ? 'active' : '' }}">
            <i class="fas fa-chart-line nav-icon"></i>
            <p>Expense</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('admin.asset') }}" class="nav-link {{ Route::is('admin.asset') ? 'active' : '' }}">
            <i class="fas fa-chart-line nav-icon"></i>
            <p>Assets</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('admin.liabilities') }}" class="nav-link {{ Route::is('admin.liabilities') ? 'active' : '' }}">
            <i class="fas fa-chart-line nav-icon"></i>
            <p>Liabilities</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('admin.equity') }}" class="nav-link {{ Route::is('admin.equity') ? 'active' : '' }}">
            <i class="fas fa-chart-line nav-icon"></i>
            <p>Equity</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('admin.equityholders') }}" class="nav-link {{ Route::is('admin.equityholders') ? 'active' : '' }}">
            <i class="fas fa-chart-line nav-icon"></i>
            <p>Equity Holders</p>
        </a>
    </li>

    <li class="nav-item dropdown {{ request()->is('admin/cash-book') || request()->is('admin/bank-book')  ? 'menu-open' : '' }}">

        <a href="#" class="nav-link dropdown-toggle {{ request()->is('admin/cash-book') || request()->is('admin/bank-book') ? 'active' : '' }}">
            <i class="nav-icon fas fa-warehouse"></i>
            <p>
                Day Book <i class="fas fa-angle-left right"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{ route('cashbook') }}" class="nav-link {{ request()->routeIs('cashbook') ? 'active' : '' }}">
                    <i class="fas fa-list nav-icon"></i>
                    <p>Cash Book</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('bankbook') }}" class="nav-link {{ request()->routeIs('bankbook') ? 'active' : '' }}">
                    <i class="fas fa-plus nav-icon"></i>
                    <p>Bank Book</p>
                </a>
            </li>
        </ul>
    </li>

    <li class="nav-item" style="margin-top: 200px">
    </li>

  </ul>
</nav>