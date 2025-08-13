<section class="py-5 footer-main text-center text-md-start">
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
            <div class="col-sm-12 col-md-6 col-lg-3 col-xl-3">
                <span class="title">Company</span> <br>
                <ul>
                    <li><a href="/">Home</a></li>
                    <li><a href="{{ route('homepage') }}#services">Services</a></li>
                    <li><a href="{{ route('homepage') }}#about">About</a></li>
                    <li><a href="{{ route('homepage') }}#products">Products</a></li>
                    <li><a href="{{ route('portfolio') }}">Portfolio</a></li>
                    <li><a href="{{ route('homepage') }}#contact">Contact</a></li>
                    <li><a href="{{ route('quotation') }}">Get Quotation</a></li>
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
            <div class="col-sm-12 col-md-6 col-lg-3 col-xl-3 text-center text-md-start">
                <span class="title d-block mb-3">Follow Us</span>
                <ul class="social_icon justify-content-center justify-content-md-start list-unstyled d-flex mb-4">
                    @if (isset($company->facebook))
                    <li>
                        <a href="{{ $company->facebook }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                            <iconify-icon icon="devicon:facebook" width="48" height="48"></iconify-icon>
                        </a>
                    </li>
                    @endif
                    @if (isset($company->instagram))
                    <li>
                        <a href="{{ $company->instagram }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                            <iconify-icon icon="skill-icons:instagram" width="48" height="48"></iconify-icon>
                        </a>
                    </li>
                    @endif
                    @if (isset($company->linkedin))   
                    <li>
                        <a href="{{ $company->linkedin }}" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
                            <iconify-icon icon="skill-icons:linkedin" width="48" height="48"></iconify-icon>
                        </a>
                    </li>
                    @endif
                </ul>

                @if (isset($company->email2))
                <span class="d-block mb-2">Email</span>
                <ul class="my-0 list-unstyled mb-4">
                    <li>
                        <a href="mailto:{{ $company->email2 }}">
                            <span class="txt-ternary">{{ $company->email2 }}</span>
                        </a>
                    </li>
                </ul>
                @endif
                @if (isset($company->phone2))
                <span class="d-block mb-2">Phone</span>
                <ul class="my-0 list-unstyled">
                    <li>
                        <a href="tel:{{ $company->phone2 }}">
                            <span class="txt-ternary">{{ $company->phone2 }}</span>
                        </a>
                    </li>
                </ul>
                @endif
            </div>
        </div>
    </div>
</section>

<section class="footer text-center text-md-start">
    <div class="container py-2">
      <div class="row justify-content-center">
          <div class="col-auto text-center">
              <small>
                  &copy; {{ date('Y') }} Mentosoftware.co.uk, All rights reserved!
              </small>
          </div>
      </div>
    </div>
</section>