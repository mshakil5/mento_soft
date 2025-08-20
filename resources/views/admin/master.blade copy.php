<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale())}}">

    @php
        $company = \App\Models\CompanyDetails::select('fav_icon', 'company_name')->first();
    @endphp

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>{{ $company->company_name ?? '' }}</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" href="{{ asset('images/company/' . $company->fav_icon) }}">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.css" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('resources/admin_new/style.css') }}">

    </head>
    <body>
        <div class="layout">
            <aside class="sidebar">
                <div class="brand">
                    <div class="logo"></div>
                    <h1>{{ $company->company_name ?? '' }}</h1>
                </div>
                <div class="search" role="search">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path stroke="currentColor" stroke-width="2" d="m21 21-4.3-4.3M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" /></svg>
                    <input id="globalSearch" placeholder="Search clients, projects, services…" />
                </div>
                <div class="mini">NAVIGATION</div>
                <nav class="nav" id="sidebarNav">
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}"><span class="label">Dashboard</span></a>
                    <a href="{{ route('allservice') }}" class="{{ request()->routeIs('allservice*') ? 'active' : '' }}"><span class="label">Services</span></a>
                </nav>
                <div class="mini">QUICK ACTIONS</div>
                <div style="display: grid; gap: 8px;">
                    <button class="btn" data-action="new-client">+ New Client</button>
                    <button class="btn" data-action="new-project">+ New Project</button>
                    <button class="btn" data-action="new-invoice">+ Create Invoice</button>
                </div>
            </aside>

            <main class="main">
                <div class="topbar">
                    <div>
                        <h2 style="margin: 0;">Workspace</h2>
                    </div>
                    <div>
                        <button class="btn" id="exportCsv">Export CSV</button>
                        <button class="btn ghost" data-action="settings">Settings</button>
                        <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn ghost">Logout</button>
                        </form>
                    </div>
                </div>
                @yield('content')
            </main>
        </div>

      <script src="{{ asset('resources/admin/js/jquery.min.js')}}"></script>
      
      <script src="{{ asset('resources/admin/datatables/jquery.dataTables.min.js')}}"></script>
      <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css">

      <script src="{{ asset('resources/admin/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
      <script src="https://cdn.datatables.net/responsive/3.0.2/js/responsive.bootstrap5.min.js"></script>

      <script src="{{ asset('resources/admin/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
      <script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js"></script>
      <script src="{{ asset('resources/admin/datatables/jszip/jszip.min.js')}}"></script>
      <script src="{{ asset('resources/admin/datatables/pdfmake/pdfmake.min.js')}}"></script>
      <script src="{{ asset('resources/admin/datatables/pdfmake/vfs_fonts.js')}}"></script>
      <script src="{{ asset('resources/admin/datatables-buttons/js/buttons.html5.min.js')}}"></script>
      <script src="{{ asset('resources/admin/datatables-buttons/js/buttons.print.min.js')}}"></script>
      <script src="{{ asset('resources/admin/datatables-buttons/js/buttons.colVis.min.js')}}"></script>

      <script src="{{ asset('resources/admin/select2/select2.min.js')}}"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.js"></script>
      <script src="{{ asset('resources/admin/toastr/toastr.min.js')}}"></script>
      <script src="{{ asset('resources/frontend/js/iconify.min.js') }}"></script>

      <script src="{{ asset('resources/admin/js/app.js')}}"></script>
      
        @yield('script')
    </body>
</html>