@extends('frontend.master')

@section('content')
<section class="default contact-section wow fadeIn"
    style="background-image: url('../images/pattern-1.svg'), linear-gradient(61deg, rgb(12, 29, 77) 46%, rgb(255, 163, 15) 94%); background-attachment: fixed;">
    <div class="container">
        <div class="row">
            @include('user.sidebar')
            <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 py-4 text-light">
                @yield('user-content')
            </main>

            @include('user.partials.create-task-modal')
            @include('user.partials.ckeditor-js')
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