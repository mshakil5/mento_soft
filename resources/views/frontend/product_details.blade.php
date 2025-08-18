@extends('frontend.master')

@if($product->meta_title)
  @section('meta_title', $product->meta_title)
@endif

@if($product->meta_description)
  @section('meta_description', $product->meta_description)
@endif

@if($product->meta_keywords)
  @section('meta_keywords', $product->meta_keywords)
@endif

@if($product->meta_image)
  @section('meta_image', asset('images/projects/meta/' . $product->meta_image))
@endif

@section('content')

<section>
    <div class="container-fluid titleBar py-4 ">
        <h1 class="display-3 text-center  fw-bold mb-0 text-ternary">{{ $product->title }}</h1>
        <h6 class="text-center text-dark">{{ $product->sub_title }}</h6>
    </div>
</section>

<section class="product-details">
    <div class="container py-5">
        <div class="row">
            <div class="{{ $product->video ? 'col-lg-6' : 'col-12' }}">
                <div class="block-area bg-white p-4 h-100">
                    <h2 class="secTtile fw-bold wow bounce">{{ $product->short_description }}</h2>
                    @if($product->long_description)
                        <p class="mb-3 text-muted small">
                            {!! $product->long_description !!}
                        </p>
                    @endif
                </div>
            </div>

            @if($product->video)
                <div class="col-lg-6 text-center">
                    <video width="100%" height="auto" controls>
                        <source src="{{ asset('images/products/videos/'.$product->video) }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
            @endif
        </div>
    </div>
</section>

<div id="demoRequest" class="wow fadeInRight infinite" data-wow-delay="1s" data-wow-iteration="1">
    <a href="{{ route('contact') }}?product_id={{ $product->id }}">Request a Demo</a>
</div>

@if ($product->features->count() > 0)
<section class="modules" style="background: linear-gradient(45deg, rgba(255, 187, 136, 0.56), rgb(255 246 210 / 87%)), url('{{ asset('resources/frontend/images/light-blue-overlay.jpg') }}'); background-size: cover; background-attachment: fixed; padding: 60px 0; background-position: 80% 80%;">
    <div class="container">
        <div class="row g-2">
            <div class="col-lg-12 mb-4">
                <h2 class="secTtile text-center text-uppercase fw-bold title-font wow bounce">Features & <span class="text-ternary">Modules</span></h2>
                <p class="mb-3 text-muted text-center">
                    {{ $product->feature_description ?? 'Explore our powerful features and modules designed to streamline your business operations.' }}
                </p>
            </div>
            
            @foreach($product->features as $feature)
            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-2">
                <div class="p-3 rounded-3 shadow-sm module-box fadeInUp wow"
                    style="cursor: pointer; height: 300px;"
                    type="button"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasFeature{{ $feature->id }}"
                    aria-controls="offcanvasFeature{{ $feature->id }}">
                    
                    @if($feature->icon)
                        <iconify-icon class="text-ternary" icon="{{ $feature->icon }}" width="50" height="50"></iconify-icon>
                    @else
                        <iconify-icon class="text-ternary" icon="emojione-monotone:ledger" width="50" height="50"></iconify-icon>
                    @endif

                    <h4>{{ $feature->title }}</h4>
                    <p class="small text-muted">{!! Str::limit($feature->description ?? '', 100) !!}</p>
                </div>
            </div>

            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasFeature{{ $feature->id }}" aria-labelledby="offcanvasFeatureLabel{{ $feature->id }}">
                <div class="offcanvas-header"></div>
                <div class="offcanvas-body small">
                    <h5 class="border p-2 d-flex justify-content-between align-items-center">
                        {{ $feature->title }}
                        <span data-bs-dismiss="offcanvas" class="btn btn-sm btn-danger">close</span>
                    </h5>
                    <p>{!! $feature->description !!}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@if ($product->clients->count() > 0)
<section class="partners">
    <div class="container">
        <div class="row">
            <h3 class="secTtile text-center text-uppercase fw-bold title-font wow bounce"><span class="txt-ternary">Who are using</span> this product</h3>
        </div>
        <div class="row mt-4">
            <div class="marquee-container">
                <div class="marquee">
                    @foreach($product->clients as $client)
                    <div>
                        <img src="{{ asset('images/product-clients/'.$client->image) }}" class="img-fluid" alt="{{ $product->name }} client">
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endif

@if ($product->faqs->count() > 0)
<section class="faq" style="background: linear-gradient(45deg, rgb(255 221 170 / 90%), #c4c6a3), url(../images/faq_bg.png); background-size: contain; background-attachment: fixed;">
    <div class="container">
        <div class="row">
            <h2 class="secTtile text-center text-dark text-uppercase fw-bold title-font wow bounce">Frequently asked questions</h2>
        </div>
        <div class="row mt-5">
            <div class="accordion accordion-flush" id="accordionFlushExample">
                @foreach($product->faqs as $index => $faq)
                <div class="accordion-item rounded-3 border-0 shadow mb-2 fadeInUp wow">
                    <h2 class="accordion-header">
                        <button class="accordion-button border-bottom fw-semibold {{ $index !== 0 ? 'collapsed' : '' }}" 
                                type="button" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#flush-collapse{{ $index }}" 
                                aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" 
                                aria-controls="flush-collapse{{ $index }}">
                            {{ $faq->question }}
                        </button>
                    </h2>
                    <div id="flush-collapse{{ $index }}" 
                        class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" 
                        data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">
                            <p>{!! $faq->answer !!}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

@if ($product->videos->count() > 0)
<section class="about-us">
    <div class="container">
        <div class="row">
            <h2 class="secTtile text-center text-dark text-uppercase fw-bold title-font wow bounce">What clients say about us</h2>
        </div>
        <div class="row mt-5">
            @foreach($product->videos as $video)
            <div class="col-lg-4">
                <div class="video-bloger shadow p-2 @if($loop->first) fadeInLeft @elseif($loop->last) fadeInRight @else fadeInUp @endif wow">
                    <video width="100%" height="auto" controls>
                        <source src="{{ asset('images/products/product-clients/'.$video->video_path) }}" type="video/mp4">
                    </video>
                    <p class="name mb-0">{{ $video->client_name }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="our-products" style="background-image: url(../images/pattern-1.svg), linear-gradient(16deg, rgb(4, 11, 22), rgb(10, 45, 102)); background-size: 100% 100%; background-position: 100% 100%;" id="products">
    <div class="row px-2 text-center py-5">
        <h2 class="secTtile text-light text-uppercase fw-bold title-font wow bounce mb-0">Other Products</h2>
    </div>
    <div class="row g-0">
        @foreach($otherProducts as $product)
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

@include('frontend.reviews')

@include('frontend.contact', ['product' => null])

@endsection