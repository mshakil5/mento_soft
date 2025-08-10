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
        <div class="row g-2">
            @forelse($type->projects as $project)
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="card p-3 pb-0 shadow-sm text-center portfolio-card">
                        <img src="{{ asset('images/projects/' . $project->image) }}" alt="{{ $project->title }}">
                        <div class="card-body">
                            <h5 class="card-title txt-ternary mb-0">{{ $type->name }}</h5>
                            <h5 class="card-text py-2 title-font fw-bold mb-0">{{ $project->title }}</h5>
                            <a href="{{ route('portfolioDetails', $project->slug) }}" class="btn txt-ternary fw-bold text-light btn-theme">View</a>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center text-white">No projects found for {{ $type->name }}.</p>
            @endforelse
        </div>
    </div>
</section>
@endforeach

@endsection