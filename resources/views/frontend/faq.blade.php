@extends('frontend.master')

@section('content')

<section>
    <div class="container-fluid titleBar py-4">
        <h1 class="display-3 text-center fw-bold mb-0 text-ternary">
            Frequently Asked Questions
        </h1>
    </div>
</section>

<section class="faq" style="background: linear-gradient(45deg, rgba(3, 67, 249, 0.05), rgba(244, 118, 4, 0.15)), url(../images/faq_bg.png); background-size: cover; background-attachment: fixed;">
    <div class="container">
        <div class="row mt-5">
            <div class="accordion accordion-flush" id="accordionFlushExample">
                @foreach($faqQuestions as $index => $faq)
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

@endsection

@section('script')

@endsection