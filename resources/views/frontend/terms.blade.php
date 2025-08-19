@extends('frontend.master')

@section('content')

<section>
    <div class="container-fluid titleBar py-4">
        <h1 class="display-3 text-center fw-bold mb-0 text-ternary">
            Terms And Conditions
        </h1>
    </div>
</section>

<section class="faq" style="background: linear-gradient(45deg, rgba(3, 67, 249, 0.05), rgba(244, 118, 4, 0.15)), url(../images/faq_bg.png); background-size: cover; background-attachment: fixed;">
    <div class="container">
        <div class="row  p-3">
            <div class="col-lg-10 offset-lg-1 text-center">
                {!! $companyDetails->terms_and_conditions !!}
            </div>
        </div>
    </div>
</section>

@endsection

@section('script')

@endsection