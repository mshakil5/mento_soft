@extends('frontend.master')

@section('content')

<section class="floating-link shadow">
    <div class="container mx-auto d-flex flex-column my-1 py-2 justify-content-center w-100 text-center">
        <h2 class="d-block mb-2 text-capitalize fw-bold mb-3">Our recent works</h2>
        <div class="d-flex gap-1 justify-content-center">
            @foreach($projectTypes as $type)
                <a href="#{{ Str::slug($type->name) }}" class="link-btn {{ $loop->first ? 'active' : '' }}">
                    {{ $type->name }}
                </a>
            @endforeach
        </div>
    </div>
</section>

@foreach($projectTypes as $type)
<section class="portfolio py-4 bg-light" id="{{ Str::slug($type->name) }}" style="background-image: url('{{ asset('resources/frontend/images/pattern-1.svg') }}'), linear-gradient(16deg, #040b16, #114ba8);">
    <div class="container">
        <h2 class="text-center text-capitalize mb-3 fw-bold text-light">{{ $type->name }}</h2>
        <div class="row g-2 portfolio-wrapper">
            @foreach($type->projects as $project)
            <div class="col-lg-4 col-md-6 col-sm-12 portfolio-item">
                <div class="card p-3 pb-0 shadow-sm text-center portfolio-card">
                    <img src="{{ asset('images/projects/' . $project->image) }}" alt="{{ $project->title }}">
                    <div class="card-body">
                        <h5 class="card-text py-2 title-font fw-bold mb-0">{{ $project->title }}</h5>
                        <a href="{{ route('portfolioDetails', $project->slug) }}" class="btn txt-ternary fw-bold text-light btn-theme">View</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @if($type->projects->count() > 6)
        <div class="text-center mt-3">
            <button class="btn txt-ternary fw-bold text-light btn-theme view-more">View More</button>
        </div>
        @endif
    </div>
</section>
@endforeach

@include('frontend.reviews')

@include('frontend.contact', ['product' => null])

@endsection

@section('script')

<script>
  $(document).ready(function(){
      $('.portfolio').each(function(){
          let $items = $(this).find('.portfolio-item');
          let visible = 6;
          $items.slice(visible).hide();
          $(this).find('.view-more').click(function(){
              if($(this).text() === 'View More'){
                  $items.slice(visible, visible+6).slideDown();
                  visible += 6;
                  if(visible >= $items.length) $(this).text('View Less');
              } else {
                  $items.slice(6).slideUp();
                  visible = 6;
                  $(this).text('View More');
              }
          });
      });
  });
</script>

@endsection