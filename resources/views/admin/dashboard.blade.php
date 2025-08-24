@extends('admin.master')

@section('content')

<section class="content">
  <div class="container-fluid">
    <div class="row pt-3">
      <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box shadow-lg">
          <span class="info-box-icon bg-info"><i class="far fa-user"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Total Clients</span>
            <span class="info-box-number">{{ $totalClients }}</span>
          </div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box shadow-lg">
          <span class="info-box-icon bg-success"><i class="far fa-flag"></i></span>

          <div class="info-box-content">
            <span class="info-box-text">Active Projects</span>
            <span class="info-box-number">{{ $activeProjects }}</span>
          </div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box shadow-lg">
          <span class="info-box-icon bg-warning"><i class="far fa-copy"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Ongoing Services</span>
            <span class="info-box-number">{{ $onGoingServices }}</span>
          </div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box shadow-lg">
          <span class="info-box-icon bg-danger"><i class="far fa-star"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Pending Dues</span>
            <span class="info-box-number">£{{ number_format($totalPending, 2) }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection

@section('script')

@endsection