@extends('admin.master')

@section('content')

<section class="content">
  <div class="container-fluid">

    <div class="row pt-3">
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
      <div class="col-md-3 col-sm-6 col-12">
        <a href="{{ route('client-projects.index') }}" class="text-dark">
        <div class="info-box shadow-lg">
          <span class="info-box-icon bg-success"><i class="far fa-flag"></i></span>

          <div class="info-box-content">
            <span class="info-box-text">Active Projects</span>
            <span class="info-box-number">{{ $activeProjects }}</span>
          </div>
        </div>
        </a>
      </div>
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
      <div class="col-md-3 col-sm-6 col-12">
        <a href="{{ route('project-services.index', ['status' => 1, 'expiring' => 1]) }}" class="text-dark">
        <div class="info-box shadow-lg">
          <span class="info-box-icon bg-danger"><i class="far fa-copy"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Services Expiring Soon</span>
            <span class="info-box-number">{{ $servicesExpiringSoon }}</span>
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
          <span class="info-box-icon bg-secondary"><i class="far fa-clock"></i></span>
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
          <span class="info-box-icon bg-warning"><i class="far fa-clock"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Tasks To Be Confirmed</span>
            <span class="info-box-number">{{ number_format($doneNotConfirmedTasks, 0) }}</span>
          </div>
        </div>
        </a>
      </div>
      <div class="col-md-3 col-sm-6 col-12">
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
      <div class="col-md-3 col-sm-6 col-12">
          <a href="{{ route('project-services.index', ['due' => 'current']) }}" class="text-dark">
              <div class="info-box shadow-lg">
                  <span class="info-box-icon bg-danger"><i class="far fa-star"></i></span>
                  <div class="info-box-content">
                      <span class="info-box-text">Current Month Pending Services</span>
                      <span class="info-box-number">£{{ number_format($currentMonthDue, 0) }}</span>
                  </div>
              </div>
          </a>
      </div>
      <div class="col-md-3 col-sm-6 col-12">
          <a href="{{ route('project-services.index', ['due' => 'next']) }}" class="text-dark">
              <div class="info-box shadow-lg">
                  <span class="info-box-icon bg-danger"><i class="far fa-star"></i></span>
                  <div class="info-box-content">
                      <span class="info-box-text">Next Month Pending Services</span>
                      <span class="info-box-number">£{{ number_format($nextMonthDue, 0) }}</span>
                  </div>
              </div>
          </a>
      </div>
      <div class="col-md-3 col-sm-6 col-12">
          <a href="{{ route('project-services.index', ['due' => 'previous']) }}" class="text-dark">
              <div class="info-box shadow-lg">
                  <span class="info-box-icon bg-danger"><i class="far fa-star"></i></span>
                  <div class="info-box-content">
                      <span class="info-box-text">Previous Pending Services</span>
                      <span class="info-box-number">£{{ number_format($allPreviousDue, 0) }}</span>
                  </div>
              </div>
          </a>
      </div>
    </div>

    <div class="row mt-4 d-none">
      <div class="col-12">
        <div class="card shadow">
          <div class="card-header">
            <h2 class="card-title">Upcoming Deadlines</h2>
          </div>
          <div class="card-body">
            <table class="table cell-border table-striped">
              <thead>
                <tr>
                  <th>When</th>
                  <th>Item</th>
                  <th>Client</th>
                  <th>Due</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>

@endsection

@section('script')

@endsection