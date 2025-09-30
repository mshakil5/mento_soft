<section class="site-header" id="header">
    <div class="container">
        <div class="row">
            <nav class="navbar navbar-expand-lg py-0 px-3 ">
                <a class="navbar-brand" href="{{ route('homepage') }}" title="Go to {{ $company->business_name }} homepage">
                    <img src="{{ isset($company->company_logo) ? asset('images/company/'.$company->company_logo) : '' }}" width="200px" alt="{{ $company->business_name ?? 'Company Logo' }}"> 
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