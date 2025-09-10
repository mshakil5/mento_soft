<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

    @role('admin')
     {{-- Admin Only --}}
    @endrole

    @can('switch')
    <a href="{{ route('toggle.sidebar') }}" class="btn btn-info my-2">
        Switch to Frontend Settings <i class="fas fa-arrow-right"></i>
    </a>
    @endcan

    <li class="nav-item">
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
        </a>
    </li>

    @canany(['tasks', 'add task', 'all tasks'])
    <li class="nav-item">
        <a href="{{ route('tasks.index') }}" class="nav-link {{ Route::is('tasks.index') || Route::is('tasks.all') ? 'active' : '' }}">
            <i class="fas fa-tasks nav-icon"></i>
            <p>Tasks</p>
        </a>
    </li>
    @endcanany

    @canany(['add client', 'mail client', 'edit client'])
    <li class="nav-item">
        <a href="{{ route('clients.index') }}" class="nav-link {{ Route::is('clients.index') ? 'active' : '' }}">
            <i class="fas fa-user nav-icon"></i>
            <p>Clients</p>
        </a>
    </li>
    @endcanany

    @canany(['add project', 'edit project'])
    <li class="nav-item">
        <a href="{{ route('client-projects.index') }}" class="nav-link {{ Route::is('client-projects.index') || Route::is('client-projects.tasks') || Route::is('client-projects.updates') || Route::is('client-projects.services') || Route::is('client-project-services.details') ? 'active' : '' }}">
            <i class="fas fa-project-diagram nav-icon"></i>
            <p>Projects</p>
        </a>
    </li>
    @endcanany

    @canany(['add service', 'receive service', 'edit service'])
    <li class="nav-item">
        <a href="{{ route('project-services.index') }}" class="nav-link {{ Route::is('project-services.index') ? 'active' : '' }}">
            <i class="fas fa-concierge-bell nav-icon"></i>
            <p>Services</p>
        </a>
    </li>
    @endcanany

    @canany(['add invoice', 'receive invoice', 'edit invoice', 'mail invoice'])
    <li class="nav-item">
        <a href="{{ route('invoices.index') }}" class="nav-link {{ request()->routeIs('invoices.index') ? 'active' : '' }}">
            <i class="fas fa-file-alt nav-icon"></i>
            <p>Invoices</p>
        </a>
    </li>
    @endcanany

    <li class="nav-item">
        <a href="{{ route('transactions.index') }}" class="nav-link">
            <i class="fas fa-exchange-alt nav-icon"></i>
            <p>Transactions</p>
        </a>
    </li>

    @can('reports')
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="fas fa-chart-line nav-icon"></i>
            <p>Reports</p>
        </a>
    </li>
    @endcan

    <li class="nav-header">QUICK ACTIONS</li>

    @can('add client')
    <button class="btn btn-success mb-2" data-toggle="modal" data-target="#quickClientModal">
        <i class="fas fa-plus"></i> New Client
    </button>
    @endcan

    @can('add project')
    <button class="btn btn-success mb-2" data-toggle="modal" data-target="#createProjectModal">
        <i class="fas fa-plus"></i> New Project
    </button>
    @endcan

    @can('add invoice')
    <button class="btn btn-success mb-2" id="createInvoiceBtn">
        <i class="fas fa-plus"></i> Create Invoice
    </button>
    @endcan

    <script>
        document.getElementById('createInvoiceBtn').addEventListener('click', function() {
            localStorage.setItem('autoclickNewBtn', '1');
            window.location.href = "{{ route('invoices.index') }}";
        });
    </script>

    @can('reports')
    <button id="toggleAccounting" class="btn btn-info my-2">
        Show Accounting
    </button>
    @endcan

    <div id="accountingWrapper" style="display: none;">
    <li class="nav-item">
        <a href="{{ route('employees.index') }}" class="nav-link {{ Route::is('employees.index') ? 'active' : '' }}">
            <i class="fas fa-users nav-icon"></i>
            <p>Employees</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('admin.addchartofaccount') }}" class="nav-link {{ Route::is('admin.addchartofaccount') ? 'active' : '' }}">
            <i class="fas fa-chart-line nav-icon"></i>
            <p>Chart Of Accounts</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('admin.income') }}" class="nav-link {{ Route::is('admin.income') ? 'active' : '' }}">
            <i class="fas fa-dollar-sign nav-icon"></i>
            <p>Income</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('admin.expense') }}" class="nav-link {{ Route::is('admin.expense') ? 'active' : '' }}">
            <i class="fas fa-money-bill-wave nav-icon"></i>
            <p>Expense</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('admin.asset') }}" class="nav-link {{ Route::is('admin.asset') ? 'active' : '' }}">
            <i class="fas fa-warehouse nav-icon"></i>
            <p>Assets</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('admin.liabilities') }}" class="nav-link {{ Route::is('admin.liabilities') ? 'active' : '' }}">
            <i class="fas fa-hand-holding-usd nav-icon"></i>
            <p>Liabilities</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('admin.equity') }}" class="nav-link {{ Route::is('admin.equity') ? 'active' : '' }}">
            <i class="fas fa-balance-scale nav-icon"></i>
            <p>Equity</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('admin.equityholders') }}" class="nav-link {{ Route::is('admin.equityholders') ? 'active' : '' }}">
            <i class="fas fa-users nav-icon"></i>
            <p>Equity Holders</p>
        </a>
    </li>


    <li class="nav-item dropdown {{ request()->routeIs('cashbook') || request()->routeIs('bankbook') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ request()->routeIs('cashbook') || request()->routeIs('bankbook') ? 'active' : '' }}">
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
                    <i class="fas fa-list nav-icon"></i>
                    <p>Bank Book</p>
                </a>
            </li>
        </ul>
    </li>

    <li class="nav-item dropdown {{ request()->routeIs('income-statement') || request()->routeIs('balance-sheet') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ request()->routeIs('income-statement') || request()->routeIs('balance-sheet') ? 'active' : '' }}">
            <i class="nav-icon fas fa-chart-line"></i>
            <p>
                Financial Statement <i class="fas fa-angle-left right"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{ route('income-statement') }}" class="nav-link {{ request()->routeIs('income-statement') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice nav-icon"></i>
                    <p>Income Statement</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('balance-sheet') }}" class="nav-link {{ request()->routeIs('balance-sheet') ? 'active' : '' }}">
                    <i class="fas fa-file-alt nav-icon"></i>
                    <p>Balance Sheet</p>
                </a>
            </li>
        </ul>
    </li>

    </div>

    <li class="nav-item" style="margin-top: 200px">
    </li>

  </ul>
</nav>