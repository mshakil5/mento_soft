<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale())}}">

@php
    $company = \App\Models\CompanyDetails::select('fav_icon', 'company_logo', 'footer_content', 'email1', 'phone1', 'address1', 'facebook', 'instagram', 'linkedin', 'business_name', 'company_reg_number', 'vat_number', 'whatsapp', 'google_site_verification')->first();
    $services = \App\Models\Service::where('status', 1)->orderByRaw('sl = 0, sl ASC')->orderBy('id', 'desc')->limit(6)->pluck('title');
@endphp 

<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="google-site-verification" content="{{ $company->google_site_verification ?? '' }}">
    {!! SEOMeta::generate() !!}
    {!! OpenGraph::generate() !!}
    {!! Twitter::generate() !!}

    <link rel="icon" href="{{ asset('images/company/' . $company->fav_icon) }}">

    <link rel="stylesheet" href="{{ asset('resources/frontend/css/bootstrap-5.1.3min.css') }}">
    <link rel="stylesheet" href="{{ asset('resources/frontend/css/app.css') }}">

    <link rel="stylesheet" href="{{ asset('resources/frontend/css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('resources/frontend/css/slick-theme.css') }}">

    <script src="{{ asset('resources/frontend/js/wow.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('resources/frontend/css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('resources/frontend/css/magnific-popup.css') }}">
</head>

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
    <script src="{{ asset('resources/frontend/js/jquery-3.7.1.min.js') }}"></script>
    {{-- <script src="{{ asset('resources/frontend/js/jquery-3.0.min.js') }}"></script> --}}
    <script src="{{ asset('resources/frontend/js/jquery.ripples.min.js') }}"></script>
    <script src="{{ asset('resources/frontend/js/typed.min.js') }}"></script>
    <script src="{{ asset('resources/frontend/js/slick.min.js') }}"></script>
    <script src="{{ asset('resources/frontend/js/counter.js') }}"></script>
    <script src="{{ asset('resources/frontend/js/app.js') }}"></script>

    @yield('script')
    
</body>

</html>