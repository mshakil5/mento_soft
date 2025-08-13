<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale())}}">

@php
    $company = \App\Models\CompanyDetails::first();
    $services = \App\Models\Service::where('status', 1)->orderByRaw('sl = 0, sl ASC')->orderBy('id', 'desc')->limit(6)->pluck('title');
@endphp 

<head>
    <meta charset="utf-8">
    {{-- <title>@yield('title', $company->company_name)</title> --}}
    {{-- <title>{!! SEO::getTitle() ?? $company->company_name !!}</title> --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    {!! SEOMeta::generate() !!}
    {!! OpenGraph::generate() !!}
    {!! Twitter::generate() !!}

    {{-- <meta name="google-site-verification" content="{{ $company->google_site_verification ?? '' }}" />

    <meta name="title" content="@yield('meta_title', $company->meta_title ?? $company->company_name ?? '')">
    <meta name="description" content="@yield('meta_description', $company->meta_description ?? '')">
    <meta property="og:image" content="@yield('meta_image', $company->meta_image ? asset('images/company/meta/' . $company->meta_image) : '')"> --}}

    <link rel="icon" href="{{ asset('images/company/' . $company->fav_icon) }}">

    <link rel="stylesheet" href="{{ asset('resources/frontend/css/bootstrap-5.1.3min.css') }}">
    <link rel="stylesheet" href="{{ asset('resources/frontend/css/app.css') }}">

    <link rel="stylesheet" href="{{ asset('resources/frontend/css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('resources/frontend/css/slick-theme.css') }}">

    <script src="{{ asset('resources/frontend/js/wow.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('resources/frontend/css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('resources/frontend/css/magnific-popup.css') }}">
</head>

<script>
  function scroller() {

    let p = window.pageYOffset;

    if (p > 200) {
      let k = document.getElementById('header');
      k.classList.add('active')
    } else {
      let k = document.getElementById('header');
      k.classList.remove('active')
    }

  }
</script>

<body onscroll="scroller()">
    <!-- Header Start -->
    @include('frontend.header')
    <!-- Header End -->

    <!-- Main Content Start -->
    @yield('content')
    <!-- Main Content End -->

    {{-- Cookie Consent Start --}}
    @include('frontend.cookie_consent')
    {{-- Cookie Consent End --}}

    <!-- Footer Start -->
    @include('frontend.footer')
    <!-- Footer End -->

    <script>
        window.services = {!! json_encode($services) !!};
    </script>

    <script src="{{ asset('resources/frontend/js/bootstrap-5.bundle.min.js') }}"></script>
    <script src="{{ asset('resources/frontend/js/iconify.min.js') }}"></script>
    <script src="{{ asset('resources/frontend/js/jquery-3.0.min.js') }}"></script>
    <script src="{{ asset('resources/frontend/js/jquery.ripples.min.js') }}"></script>
    <script src="{{ asset('resources/frontend/js/typed.min.js') }}"></script>
    <script src="{{ asset('resources/frontend/js/slick.min.js') }}"></script>
    <script src="{{ asset('resources/frontend/js/counter.js') }}"></script>
    <script src="{{ asset('resources/frontend/js/app.js') }}"></script>

    @yield('script')
    
</body>

</html>