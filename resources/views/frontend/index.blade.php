@extends('frontend.master')

@section('content')

<section class="hero" id="home">
    <video id="videoAdjust" class="" autoplay="true" width="100%" loop="loop" muted="muted" playsinline="true">
        <source src="{{ asset('resources/frontend/videos/bg.mp4') }}" type="video/mp4">
    </video>
    <div class="container-fluid px-0 h-100">
        <div class="h-100 hero-content">
            <div class="text-center mx-auto py-5">
                <h2 class="text-uppercase title-font mb-4 wow zoomInDown">{{ $landingPage->short_title }} <span class="txt-ternary">mento
                        software</span>
                </h2>
                <h1 class="fw-bold display-3 wow fadeIn" style="color: #963434;">{{ $landingPage->long_title }}
                    <br>{{ $landingPage->short_description }}
                </h1>
                <p class="w-75 fs-3 text-center mx-auto wow fadeIn">
                    {!! $landingPage->long_description !!}
                </p>
                <div class="my-4">
                    <h4 class="txt-bold title-font wow fadeIn"><span
                            class="typed txt-ternary fw-bold "> </span>
                    </h4>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="default choose-us position-relative" id="about">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h2 class="secTtile text-light text-uppercase fw-bold title-font wow bounce"> {{ $whyChooseUs->short_title }}</h2>
            </div>
        </div>
        <div class="row mt-5 text-center text-md-start">
            <div class='col-lg-6 text-white wow fadeIn text-justify mb-5 mb-lg-0'>
                {!! $whyChooseUs->long_description !!}
            </div>
            <div class="col-lg-6">
                <div class="row g-3" id="counter">
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="bg-light box-gradient-counter txt-ternary p-4 rounded-3 d-flex flex-column align-items-start h-100 wow fadeInRight" data-wow-delay=".25s">
                            <h3 class="fw-bold mb-2">15+ Years of Expertise</h3>
                            <p class="fs-6 text-dark m-0 text-justify">
                                Decades of proven success delivering high-quality software solutions across industries.
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="bg-light box-gradient-counter txt-ternary p-4 rounded-3 d-flex flex-column align-items-start h-100 wow fadeInRight" data-wow-delay=".35s">
                            <h3 class="fw-bold mb-2">Tailored Solutions</h3>
                            <p class="fs-6 text-dark m-0 text-justify">
                                We adapt our approach to match your vision, goals, and evolving needs.
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="bg-light box-gradient-counter txt-ternary p-4 rounded-3 d-flex flex-column align-items-start h-100 wow fadeInRight" data-wow-delay=".45s">
                            <h3 class="fw-bold mb-2">Agile & Efficient Delivery</h3>
                            <p class="fs-6 text-dark m-0 text-justify">
                                Flexible development ensures faster turnaround without compromising quality.
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="bg-light box-gradient-counter txt-ternary p-4 rounded-3 d-flex flex-column align-items-start h-100 wow fadeInRight" data-wow-delay=".55s">
                            <h3 class="fw-bold mb-2">Trusted Partnerships</h3>
                            <p class="fs-6 text-dark m-0 text-justify">
                                Long-term relationships built on transparency, collaboration, and measurable results.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="cta">
    <div class="container">
        <div class="row  px-2  text-center  ">
            <h2 class="secTtile text-dark text-uppercase fw-bold title-font wow bounce mb-0 wow fadeInUp">{{ $ourFlexible->short_title }}</h2>
            <h5 class="text-center w-75 mx-auto mt-3">{!! $ourFlexible->long_description !!}</h5>
        </div>
        <div class="row mt-5">
            <div class="inner text-center ">
                <div class="items fadeInLeft wow">
                    <img src="{{ asset('resources/frontend/images/ico1.svg') }}" alt="">
                    <h3 class="wow fadeInUp text-dark">Initiation</h3>
                </div>
                <div class="items fadeInLeft wow">
                    <img src="{{ asset('resources/frontend/images/ico2.svg') }}" alt="">
                    <h3 class="wow fadeInUp text-dark">Discovery</h3>
                </div>
                <div class="items fadeInRight wow">
                    <img src="{{ asset('resources/frontend/images/ico3.svg') }}" alt="">
                    <h3 class="wow fadeInUp text-dark">Development</h3>
                </div>
                <div class="items fadeInRight wow">
                    <img src="{{ asset('resources/frontend/images/ico4.svg') }}" alt="">
                    <h3 class="wow fadeInUp text-dark">Support</h3>
                </div>
            </div>
        </div>
    </div>
</section>

@if ($services->count() > 0)
  <section class="default what-we-do position-relative" id="services">
      <div class="container-fluid">
          <div class="row">
              <div class="col-lg-12 text-center mb-4">
                  <h2 class="secTtile text-light text-uppercase fw-bold title-font wow bounce">What we do?</h2>
              </div>
          </div>
          @php
              $classes = ['ecommerce', 'app', 'customSoftware', 'graphics', 'seo', 'web'];
          @endphp

          <div class="w-100 d-flex">
              <div class="inner">
                  <div class="circle fadeIn wow" style="width:400px; height:400px; border-radius:50%; background: url('{{ asset('resources/frontend/images/OF71Y80.png') }}') center center / contain no-repeat; animation: blink 10s linear infinite;"></div>

                  @foreach ($services as $index => $service)
                      @php
                          $hasVideo = $service->youtube_link || $service->video;
                          $class = $classes[$index % count($classes)];
                          $serviceMap[$service->id] = $class;
                      @endphp

                      <div class="{{ $class }}" {!! $hasVideo ? 'onclick="openBox(\'' . $class . '\')"' : '' !!}>
                          {{ $service->title }}
                      </div>
                  @endforeach
              </div>

              @foreach ($services as $index => $service)
                  @php
                      $hasVideo = $service->video || $service->youtube_link;
                      $class = $serviceMap[$service->id] ?? $classes[$index % count($classes)];
                  @endphp

                  @if ($hasVideo)
                      <div id="{{ $class }}-box" class="visualBox">
                          <div class="border mt-4 p-1 ps-2 position-relative mb-3 d-flex align-items-center justify-content-between txt-ternary fw-bold">
                              {{ $service->title }}
                              <div class="crossbtn px-2" onclick="closeModule()">Close</div>
                          </div>
                          @if($service->short_desc)
                              <p class="m-0 p-1">{{ $service->short_desc }}</p>
                          @endif

                          @if ($service->video)
                              <video width="100%" controls data-src="{{ asset('images/service/videos/' . $service->video) }}"></video>
                          @elseif ($service->youtube_link)
                              <iframe width="100%" data-src="{{ $service->youtube_link }}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                          @endif
                      </div>
                  @endif
              @endforeach
          </div>
      </div>
  </section>
@endif

@if ($products->count() > 0)
  <section class="our-products" style="background-image: url(../images/pattern-1.svg), linear-gradient(16deg, rgb(4, 11, 22), rgb(10, 45, 102)); background-size: 100% 100%; background-position: 100% 100%;" id="products">
      <div class="row px-2 text-center py-5">
          <h2 class="secTtile text-light text-uppercase fw-bold title-font wow bounce mb-0">our products</h2>
      </div>
      <div class="row g-0">
          @foreach($products as $product)
              <div class="col-lg-6" style="position: relative;">
                    @if($loop->iteration % 2 == 1 && !$loop->last)
                      <div style="position: absolute; top: 0; right: 0;width: 2px; height: 100%; background: linear-gradient(to bottom, rgba(255,163,15,0) 0%, rgba(255,163,15,0.8) 50%, rgba(255,163,15,0) 100%);
                      "></div>
                    @endif
                  <div class="innerbox {{ $loop->odd ? 'accounting' : 'ai' }}">
                      <a href="{{ route('productDetails', $product->slug) }}" class="productitle">
                          {{ $product->title }}
                      </a>
                      
                      @php
                          $allClasses = ['one', 'two', 'three', 'four', 'five', 'six'];
                          $bgClasses = ['bg-dark text-light', ''];
                          
                          shuffle($allClasses);
                          
                          $features = $product->features;
                      @endphp
                      
                      @foreach($features as $index => $feature)
                          @php
                              $bgClass = $bgClasses[$index % count($bgClasses)];
                              $positionClass = $allClasses[$index % count($allClasses)];
                              $animationClass = $index === 0 ? 'fadeInUp wow' : '';
                          @endphp
                          
                          <div class="{{ $positionClass }} {{ $bgClass }} {{ $animationClass }}">
                              {{ $feature->title }}
                          </div>
                      @endforeach
                  </div>
              </div>
              @if($loop->iteration % 2 == 0 && !$loop->last)
                <div class="w-100 my-3 d-none d-md-block">
                    <div style="height: 2px; background: linear-gradient(90deg, rgba(12,29,77,0) 0%, rgba(12,29,77,1) 20%, rgba(255,163,15,1) 50%, rgba(12,29,77,1) 80%, rgba(12,29,77,0) 100%);"></div>
                </div>
              @endif

              @if(!$loop->last)
                <div class="w-100 my-3 d-block d-md-none">
                    <div style="height: 2px; background: linear-gradient(90deg, rgba(12,29,77,0) 0%, rgba(12,29,77,1) 20%, rgba(255,163,15,1) 50%, rgba(12,29,77,1) 80%, rgba(12,29,77,0) 100%);"></div>
                </div>
              @endif
          @endforeach
      </div>
  </section>
@endif

@include('frontend.reviews')

@include('frontend.contact', ['product' => null])

@endsection

@section('script')

@endsection