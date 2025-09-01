<section class="top-bar  d-none d-md-block  ">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">
                <ul>
                    <li><iconify-icon icon="mdi-light:map-marker" width="24" height="24"></iconify-icon><a href="">
                            24 Ince Way
                            MK44NP - Milton Keynes</a></li>
                    <li><iconify-icon icon="circum:mail" width="24" height="24"></iconify-icon><a
                            href="">info@mentosoftware.co.uk</a></li>
                    <li><iconify-icon icon="uit:clock" width="24" height="24"></iconify-icon><a href="">Mon - Fri:
                            10am to 6pm</a></li>
                </ul>
            </div>
            <div class="col-lg-4 ">
                <ul class="justify-content-end">
                    <li><iconify-icon icon="lets-icons:phone-light" width="24" height="24"></iconify-icon><a
                            href="">07745975978</a></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="site-header" id="header">
    <div class="container">
        <div class="row">
            <nav class="navbar navbar-expand-lg py-0 px-3 ">
                <a class="navbar-brand" href="/">
                    <img src="{{ isset($company->company_logo) ? asset('images/company/'.$company->company_logo) : '' }}" width="200px"> 
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="iconify navbar-toggler-icon txt-ternary" data-icon="charm:menu-hamburger"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav ms-auto navCustom">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('homepage') ? 'active' : '' }}" href="/">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('homepage') }}#services">Services</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('homepage') }}#about">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('homepage') }}#products">
                                Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('portfolio') || request()->routeIs('portfolioDetails') ? 'active' : '' }}" href="{{ route('portfolio') }}">
                                Portfolio
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('homepage') }}#contact">Contact</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('quotation') ? 'active' : '' }}" href="{{ route('quotation') }}">Get Quotation</a>
                        </li>
                        @auth
                        <li class="nav-item">
                            <a class="nav-link" 
                              href="{{ route('dashboard') }}">
                                Dashboard
                            </a>
                        </li>
                        @endauth
                    </ul>
                </div>

            </nav>
        </div>
    </div>
</section>