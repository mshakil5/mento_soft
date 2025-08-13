@extends('frontend.master')

@section('content')

<section>
    <div class="container-fluid titleBar py-4 ">
        <h1 class="display-3 text-center  fw-bold mb-0 text-ternary">Contact Us</h1>
    </div>
</section>

@include('frontend.contact', ['product' => $product])

@endsection

@section('script')

@endsection