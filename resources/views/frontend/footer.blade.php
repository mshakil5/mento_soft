<section class="py-2 footer-main text-center text-md-start">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-6 col-lg-3 col-xl-3">
                <p>
                    <a class="navbar-brand" href="/">
                        <img src="{{ isset($company->company_logo) ? asset('images/company/'.$company->company_logo) : '' }}" width="190px">
                    </a>
                </p>
                <small>{{ $company->footer_content }} </small>
            </div>

            <div class="col-sm-12 col-md-6 col-lg-2 col-xl-2">
                <span class="title">Company</span> <br>
                <ul>
                    <li><a href="/">Home</a></li>
                    <li><a href="{{ route('homepage') }}#services">Services</a></li>
                    <li><a href="{{ route('homepage') }}#about">About</a></li>
                    <li><a href="{{ route('homepage') }}#products">Products</a></li>
                    <li><a href="{{ route('portfolio') }}">Portfolio</a></li>
                </ul>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-2 col-xl-2 pt-lg-4 pt-xl-4">
                <ul>
                    <li>
                        @auth
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                        @else
                            <a href="{{ route('client.login') }}">Login</a>
                        @endauth
                    </li>
                    <li><a href="{{ route('homepage') }}#contact">Contact</a></li>
                    <li><a href="{{ route('quotation') }}">Get Quotation</a></li>
                    <li><a href="{{ route('privacy-policy') }}">Privacy Policy</a></li>
                    <li><a href="{{ route('terms-and-conditions') }}">Terms & Conditions</a></li>
                    <li><a href="{{ route('frequently-asked-questions') }}">FAQ</a></li>
                </ul>
            </div>

            <div class="col-sm-12 col-md-6 col-lg-3 col-xl-3">
                <span class="title">Contact</span> <br>

              <ul>
                  <li>
                      Email <br>
                      <a href="mailto:{{ $company->email1 }}">
                          <span class="txt-ternary">{{ $company->email1 }}</span>
                      </a>
                  </li>
                  <li>
                      Phone <br>
                      <a href="tel:{{ $company->phone1 }}">
                          <span class="txt-ternary">{{ $company->phone1 }}</span>
                      </a>
                  </li>
                  <li>
                      Address <br>
                      <a>
                          <span class="txt-ternary">{{ $company->address1 }}</span>
                      </a>
                  </li>
              </ul>

            </div>
            
            <div class="col-sm-12 col-md-6 col-lg-2 col-xl-2 text-center">
                <span class="title">Follow Us</span>
                <ul class="social_icon justify-content-center justify-conent-md-start">
                    @if(isset($company->facebook))
                    <li class="me-2">
                        <a href="{{ $company->facebook }}" target="_blank" rel="noopener noreferrer">
                            <iconify-icon icon="devicon:facebook" width="48" height="48"></iconify-icon>
                        </a>
                    </li>
                    @endif
                    @if(isset($company->instagram))
                    <li class="me-2">
                        <a href="{{ $company->instagram }}" target="_blank" rel="noopener noreferrer">
                            <iconify-icon icon="skill-icons:instagram" width="48" height="48"></iconify-icon>
                        </a>
                    </li>
                    @endif
                    @if(isset($company->linkedin))
                    <li>
                        <a href="{{ $company->linkedin }}" target="_blank" rel="noopener noreferrer">
                            <iconify-icon icon="skill-icons:linkedin" width="48" height="48"></iconify-icon>
                        </a>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="footer text-center text-md-start">
    <div class="container py-2">
      <div class="row justify-content-center">
          <div class="col-auto text-center">
            <small>
                &copy; {{ date('Y') }} {{ $company->business_name ?? '' }}
                @if(!empty($company->company_reg_number))
                    | COMPANY NUMBER: {{ $company->company_reg_number }}
                @endif
                @if(!empty($company->vat_number))
                    | VAT No: {{ $company->vat_number }}
                @endif
            </small>
          </div>
      </div>
    </div>
</section>

<button id="scrollUp" title="Go to top">↑</button>

<a href="https://wa.me/{{ $company->whatsapp }}" target="_blank" class="material-whatsapp" aria-label="WhatsApp">
    <img src="{{ asset('resources/frontend/images/whatsapp.png') }}" alt="WhatsApp" style="width:24px; height:24px;">
</a>