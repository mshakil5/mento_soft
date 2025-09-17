@extends('admin.master')

@section('content')

@can('dashboard-content')
<section class="content">
  <div class="container-fluid">

    <div class="row pt-3">
      @canany(['tasks', 'add task', 'all tasks'])
      <div class="col-md-3 col-sm-6 col-12">
        <a href="{{ route('tasks.all', ['status' => 1, 'employee_id' => auth()->id()]) }}" class="text-dark">
        <div class="info-box shadow-lg">
          <span class="info-box-icon bg-danger"><i class="far fa-clock"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Your Next Task</span>
            <span class="info-box-number">{{ number_format($newTaskCount, 0) }}</span>
          </div>
        </div>
        </a>
      </div>
      <div class="col-md-3 col-sm-6 col-12">
        <a href="{{ route('tasks.all', ['status' => 1]) }}" class="text-dark">
        <div class="info-box shadow-lg">
          <span class="info-box-icon bg-warning"><i class="far fa-clock"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">To Do</span>
            <span class="info-box-number">{{ number_format($todoTasks, 0) }}</span>
          </div>
        </div>
        </a>
      </div>
      <div class="col-md-3 col-sm-6 col-12">
        <a href="{{ route('tasks.all', ['status' => 2]) }}" class="text-dark">
        <div class="info-box shadow-lg">
          <span class="info-box-icon bg-info"><i class="far fa-clock"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Tasks in Progress</span>
            <span class="info-box-number">{{ number_format($inProgressTasks, 0) }}</span>
          </div>
        </div>
        </a>
      </div>
      <div class="col-md-3 col-sm-6 col-12">
        <a href="{{ route('tasks.all', ['status' => 3, 'is_confirmed' => 0]) }}" class="text-dark">
        <div class="info-box shadow-lg">
          <span class="info-box-icon bg-success"><i class="far fa-clock"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Tasks To Be Confirmed</span>
            <span class="info-box-number">{{ number_format($doneNotConfirmedTasks, 0) }}</span>
          </div>
        </div>
        </a>
      </div>
      <div class="col-md-3 col-sm-6 col-12 d-none">
        <a href="{{ route('tasks.all', ['status' => 3, 'is_confirmed' => 1]) }}" class="text-dark">
        <div class="info-box shadow-lg">
          <span class="info-box-icon bg-success"><i class="far fa-clock"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Completed Tasks</span>
            <span class="info-box-number">{{ number_format($doneTasks, 0) }}</span>
          </div>
        </div>
        </a>
      </div>
      @endcanany
      @canany(['add client', 'mail client', 'edit client'])
      <div class="col-md-3 col-sm-6 col-12">
        <a href="{{ route('clients.index') }}" class="text-dark">
        <div class="info-box shadow-lg">
          <span class="info-box-icon bg-info"><i class="far fa-user"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Total Clients</span>
            <span class="info-box-number">{{ $totalClients }}</span>
          </div>
        </div>
        </a>
      </div>
      @endcanany
      @canany(['add project', 'edit project'])
      <div class="col-md-3 col-sm-6 col-12">
        <a href="{{ route('client-projects.index') }}" class="text-dark">
        <div class="info-box shadow-lg">
          <span class="info-box-icon bg-success"><i class="far fa-flag"></i></span>

          <div class="info-box-content">
            <span class="info-box-text">Projects</span>
            <span class="info-box-number">{{ $activeProjects }}</span>
          </div>
        </div>
        </a>
      </div>
      @endcanany
      @canany(['add service', 'receive service', 'edit service'])
      <div class="col-md-3 col-sm-6 col-12">
        <a href="{{ route('project-services.index', ['status' => 1]) }}" class="text-dark">
        <div class="info-box shadow-lg">
          <span class="info-box-icon bg-warning"><i class="far fa-copy"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Ongoing Services</span>
            <span class="info-box-number">{{ $onGoingServices }}</span>
          </div>
        </div>
        </a>
      </div>
      @endcanany
      @canany(['add invoice', 'receive invoice', 'edit invoice', 'mail invoice'])
      <div class="col-md-3 col-sm-6 col-12">
          <a href="{{ route('invoices.index', ['status' => 1]) }}" class="text-dark">
              <div class="info-box shadow-lg">
                  <span class="info-box-icon bg-danger"><i class="far fa-star"></i></span>
                  <div class="info-box-content">
                      <span class="info-box-text">Due Invoices</span>
                      <span class="info-box-number">£{{ number_format($pendingInvoices, 0) }}</span>
                  </div>
              </div>
          </a>
      </div>
      @endcanany
      <div class="col-md-3 col-sm-6 col-12 d-none">
        <a href="{{ route('project-services.index', ['status' => 1, 'renew' => 1]) }}" class="text-dark">
        <div class="info-box shadow-lg">
          <span class="info-box-icon bg-danger"><i class="far fa-copy"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Services To Be Renewed</span>
            <span class="info-box-number">{{ $servicesNeedToBeRenewed }}</span>
          </div>
        </div>
        </a>
      </div>
      @canany(['add service', 'receive service', 'edit service'])
      <div class="col-md-3 col-sm-6 col-12">
          <a href="{{ route('project-services.index', ['due' => 'current']) }}" class="text-decoration-none">
              <div class="info-box bg-danger text-white shadow-lg">
                  <div class="ribbon-wrapper ribbon-md">
                      <div class="ribbon bg-white">
                          Current
                      </div>
                  </div>
                  <span class="info-box-icon"><i class="far fa-star"></i></span>
                  <div class="info-box-content">
                      <span class="info-box-text">Current Month Pending Services</span>
                      <span class="info-box-number fw-bold fs-4">{{ $currentMonthCount }}</span>
                  </div>
              </div>
          </a>
      </div>
      <div class="col-md-3 col-sm-6 col-12">
          <a href="{{ route('project-services.index', ['due' => 'next']) }}" class="text-dark">
              <div class="info-box shadow-lg">
                  <div class="ribbon-wrapper ribbon-md">
                      <div class="ribbon bg-danger">
                          N1
                      </div>
                  </div>
                  <span class="info-box-icon bg-danger"><i class="far fa-star"></i></span>
                  <div class="info-box-content">
                      <span class="info-box-text">Next Month Pending Services</span>
                      <span class="info-box-number">{{ $nextMonthCount }}</span>
                  </div>
              </div>
          </a>
      </div>
      <div class="col-md-3 col-sm-6 col-12">
          <a href="{{ route('project-services.index', ['due' => 'next2']) }}" class="text-dark">
              <div class="info-box shadow-lg">
                  <div class="ribbon-wrapper ribbon-md">
                      <div class="ribbon bg-danger">
                          N2
                      </div>
                  </div>
                  <span class="info-box-icon bg-danger"><i class="far fa-star"></i></span>
                  <div class="info-box-content">
                      <span class="info-box-text">Next Two Months Pending Services</span>
                      <span class="info-box-number">{{ $next2MonthCount }}</span>
                  </div>
              </div>
          </a>
      </div>
      <div class="col-md-3 col-sm-6 col-12">
          <a href="{{ route('project-services.index', ['due' => 'next3']) }}" class="text-dark">
              <div class="info-box shadow-lg">
                  <div class="ribbon-wrapper ribbon-md">
                      <div class="ribbon bg-danger">
                          N3
                      </div>
                  </div>
                  <span class="info-box-icon bg-danger"><i class="far fa-star"></i></span>
                  <div class="info-box-content">
                      <span class="info-box-text">Next Three Months Pending Services</span>
                      <span class="info-box-number">{{ $next3MonthCount }}</span>
                  </div>
              </div>
          </a>
      </div>
      @endcanany
    </div>

  </div>
</section>
@endcan

@endsection

@section('script')

@endsection