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
            <div class='col-lg-6 text-white wow fadeIn'>
                {!! $whyChooseUs->long_description !!}
            </div>
            <div class="col-lg-6">
                <div class="row g-3" id="counter">
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="bg-light box-gradient-counter txt-ternary p-3 rounded-3 d-flex align-items-center gap-3 box-model wow fadeInRight"
                            data-wow-delay=".5s">
                            <div>
                                <span class="display-3 fw-bold count" data-number="10"></span>
                                <span class="display-3 fw-bold ">X</span>
                                <br> <span class="fs-5 text-dark">Take <br> Faster Delivery </span>
                            </div>
                            <iconify-icon icon="bitcoin-icons:rocket-outline" width="90" height="90"></iconify-icon>
                        </div>

                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6">

                        <div class="bg-light box-gradient-counter txt-ternary p-3 rounded-3 d-flex align-items-center gap-3 box-model wow fadeInRight"
                            data-wow-delay=".50s">
                            <div>
                                <span class="display-3 fw-bold count" data-number="11"> </span>
                                <span class="display-3 fw-bold"> + </span>
                                <br> <span class="fs-5 text-dark"> Years of <br> Experience </span>
                            </div>
                            <iconify-icon icon="lets-icons:sun-light" width="90" height="90"></iconify-icon>
                        </div>

                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6">

                        <div class="bg-light box-gradient-counter txt-ternary p-3 rounded-3 d-flex align-items-center gap-3 box-model wow fadeInRight"
                            data-wow-delay=".25s">
                            <div>
                                <span class="display-3 fw-bold count" data-number="30"></span>
                                <span class="display-3 fw-bold ">+</span>
                                <br> <span class="fs-5 text-dark"> Customers <br>Worldwide </span>
                            </div>
                            <iconify-icon icon="arcticons:world-geography-alt" width="90"
                                height="90"></iconify-icon>
                        </div>

                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6">

                        <div class="bg-light box-gradient-counter txt-ternary p-3 rounded-3 d-flex align-items-center gap-3 box-model wow fadeInRight"
                            data-wow-delay=".35s">
                            <div>
                                <span class="display-3 fw-bold count" data-number="99"> </span>
                                <span class="display-3 fw-bold  "> % </span>
                                <br> <span class="fs-5 text-dark">Our <br> Sucess Rate </span>
                            </div>
                            <iconify-icon icon="ph:certificate-thin" width="90" height="90"></iconify-icon>
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
                  <div class="circle fadeIn wow "></div>

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
                      $hasVideo = $service->youtube_link || $service->video;
                      $class = $serviceMap[$service->id] ?? $classes[$index % count($classes)];
                  @endphp

                  @if ($hasVideo)
                      <div id="{{ $class }}-box" class="visualBox">
                          <div class="border mt-4 p-1 ps-2 position-relative mb-3 d-flex align-items-center justify-content-between">
                              {{ $service->title }}
                              <div class="crossbtn px-2" onclick="closeModule()">Close</div>
                          </div>
                          @if($service->short_desc)
                              <p class="m-0 p-3">{{ $service->short_desc }}</p>
                          @endif

                          @if ($service->youtube_link)
                              <iframe width="100%" height="380"
                                  data-src="{{ $service->youtube_link }}"
                                  title="YouTube video player" frameborder="0"
                                  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                  allowfullscreen></iframe>
                          @elseif ($service->video)
                              <video width="100%" height="380" controls data-src="{{ asset('images/service/videos/' . $service->video) }}">
                              </video>
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
                <div class="w-100 my-3">
                    <div class="divider" style="height: 2px; background: linear-gradient(90deg, rgba(12,29,77,0) 0%, rgba(12,29,77,1) 20%, rgba(255,163,15,1) 50%, rgba(12,29,77,1) 80%, rgba(12,29,77,0) 100%);"></div>
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