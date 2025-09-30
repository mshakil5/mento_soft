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

    <link rel="stylesheet" href="{{ asset('resources/frontend/css/slick.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('resources/frontend/css/slick-theme.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('resources/frontend/css/animate.min.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('resources/frontend/css/magnific-popup.css') }}" media="print" onload="this.media='all'">

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

    <script src="{{ asset('resources/frontend/js/jquery-3.7.1.min.js') }}" defer></script>

    <script defer>
        (function($){
            const oldOn = $.fn.on;
            $.fn.on = function(types, selector, data, fn) {
                if (typeof types === "string") {
                    const passiveEvents = ['touchstart','touchmove','wheel','mousewheel'];
                    types.split(' ').forEach((type) => {
                        if (passiveEvents.includes(type)) {
                            if (typeof selector === 'function') {
                                oldOn.call(this, type, { passive: true }, selector);
                            } else if (typeof fn === 'function') {
                                oldOn.call(this, type, selector, data, fn, { passive: true });
                            }
                        }
                    });
                }
                return oldOn.apply(this, arguments);
            };
        })(jQuery);
    </script>

    <script src="{{ asset('resources/frontend/js/bootstrap-5.bundle.min.js') }}" defer></script>
    <script src="{{ asset('resources/frontend/js/jquery.ripples.min.js') }}" defer></script>
    <script src="{{ asset('resources/frontend/js/typed.min.js') }}" defer></script>
    <script src="{{ asset('resources/frontend/js/slick.min.js') }}" defer></script>
    <script src="{{ asset('resources/frontend/js/counter.js') }}" defer></script>
    <script src="{{ asset('resources/frontend/js/app.js') }}" defer></script>

    <script src="{{ asset('resources/frontend/js/iconify.min.js') }}" async></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" async></script>

    <script defer>
        window.services = {!! json_encode($services) !!};
    </script>

    @if(session('success'))
        <script>
            $(function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#FF6D33'
                });
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            $(function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#FF6D33'
                });
            });
        </script>
    @endif

    @if ($errors->any())
        <script>
            $(function () {
                let errorMessages = `{!! implode('<br>', $errors->all()) !!}`;
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: errorMessages,
                    confirmButtonColor: '#FF6D33'
                });
            });
        </script>
    @endif

    @yield('script')
    
</body>

</html>