@php
  $teamMembers = App\Models\TeamMember::where('status', 1)
    ->orderByRaw('sl = 0, sl ASC')
    ->orderBy('id', 'desc')
    ->get();
@endphp

@if ($teamMembers->count() > 0)
  <section class="default testimonial-section wow fadeIn d-none" id="testimonial">
      <div class="container">
          <div class="row px-2">
              <div class="col-lg-12 text-center">
                  <h2 class="secTtile text-dark text-uppercase fw-bold title-font wow bounce">Our Team Members</h2>
              </div>
          </div>
          <div class="row mt-3">
              <div class="testimonial">
                  @foreach ($teamMembers as $item)
                      <div class="p-3 fadeInUp wow">
                          <div class="blogBox">
                              <div class="content text-center text-light">
                                  <img src="{{ $item->image && file_exists(public_path('images/team-members/' . $item->image)) ? asset('images/team-members/' . $item->image) : asset('/resources/frontend/images/' . ($item->title == 'Mr' ? 'man.png' : 'woman.png')) }}" class="rounded-circle mx-auto mb-3" width="90px">
                                  
                                  <h5>{{ $item->name }}</h5>

                                  <p class="m-0">
                                      <iconify-icon icon="bxs:quote-left" width="25" height="25" style="color: #fff"></iconify-icon>
                                      {!! $item->description !!}
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