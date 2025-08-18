@php
  $testimonials = App\Models\ClientReview::where('status', 1)
    ->orderByRaw('sl = 0, sl ASC')
    ->orderBy('id', 'desc')
    ->get();
  $company = \App\Models\CompanyDetails::first();
@endphp
@if ($testimonials->count() > 0)
  <section class="default testimonial-section wow fadeIn" id="testimonial">
      <div class="container">
          <div class="row px-2">
              <div class="col-lg-12 text-center">
                  <h2 class="secTtile text-dark text-uppercase fw-bold title-font wow bounce">Happy Clients Says</h2>
              </div>
          </div>
          <div class="row mt-3">
              <div class="testimonial">
                  @foreach ($testimonials as $item)
                      <div class="p-3 fadeInUp wow">
                          <div class="blogBox">
                              <div class="content text-center text-light">
                                  <img src="{{ $item->image && file_exists(public_path('images/client-reviews/' . $item->image)) ? asset('images/client-reviews/' . $item->image) : asset('images/company/' . $company->company_logo) }}" class="rounded-circle mx-auto mb-3" width="90px">
                                  <h5>{{ $item->name }} - </h5>
                                  <p class="m-0">
                                      <iconify-icon icon="bxs:quote-left" width="25" height="25" style="color: #fff"></iconify-icon>
                                      {!! $item->review !!}
                                      <iconify-icon icon="bxs:quote-right" width="25" height="25" style="color: #fff"></iconify-icon>
                                  </p>
                              </div>
                          </div>
                      </div>
                  @endforeach
              </div>
          </div>
      </div>
  </section>
@endif