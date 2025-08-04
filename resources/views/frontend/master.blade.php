<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale())}}">

@php
    $company = \App\Models\CompanyDetails::select('fav_icon', 'company_name', 'footer_content', 'address1', 'email1', 'phone1', 'company_logo', 'facebook', 'twitter', 'instagram', 'youtube', 'currency','google_map', 'copyright', 'whatsapp')->first();
@endphp 

<head>
    <meta charset="utf-8">
    <title>@yield('title', $company->company_name)</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    
    <meta name="title" content="@yield('meta_title', '')">

    <meta name="description" content="@yield('meta_description', '')">

    <meta name="keywords" content="@yield('meta_keywords', '')">

    <meta name="google-site-verification" content="EAiBnUW1XISlAkvcite__kJvue-vwZ2-lVUIfv1HaK4" />

    <link rel="icon" href="{{ asset('images/company/' . $company->fav_icon) }}">

    <link rel="stylesheet" href="{{ asset('resources/frontend/css/bootstrap-5.1.3min.css') }}">
    <link rel="stylesheet" href="{{ asset('resources/frontend/css/app.css') }}">

    <link rel="stylesheet" href="{{ asset('resources/frontend/css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('resources/frontend/css/slick-theme.css') }}">

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

    <!-- Footer Start -->
    @include('frontend.footer')
    <!-- Footer End -->

    <script src="{{ asset('resources/frontend/js/bootstrap-5.bundle.min.js') }}"></script>
    <script src="{{ asset('resources/frontend/js/iconify.min.js') }}"></script>
    <script src="{{ asset('resources/frontend/js/jquery-3.0.min.js') }}"></script>
    <script src="{{ asset('resources/frontend/js/jquery.ripples.min.js') }}"></script>
    <script src="{{ asset('resources/frontend/js/typed.min.js') }}"></script>
    <script src="{{ asset('resources/frontend/js/slick.min.js') }}"></script>
    <script src="{{ asset('resources/frontend/js/counter.js') }}"></script>
    <script src="{{ asset('resources/frontend/js/app.js') }}"></script>
    <script src="{{ asset('resources/frontend/js/wow.min.js') }}"></script>

    @yield('script')
    
</body>

</html>