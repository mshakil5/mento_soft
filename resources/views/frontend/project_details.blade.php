@extends('frontend.master')

@section('content')
<section class="project-details"
    style="background-image: url('{{ asset('resources/frontend/images/pattern-1.svg') }}'), linear-gradient(16deg, #040b16, #0a2d66);">
    <div class="container py-5 wow fadeInUp">
        <div class="row shadow rounded-4">
            <div class="col-lg-8 px-0 text-light">
                <div class="p-4">
                    <h4>Details About {{ $project->title }}:</h4>
                    <p class="mb-3 text-light small">{!! $project->long_desc !!}</p>

                    @if ($project->projectSliders->count())
                        <div id="carouselExampleIndicators" class="carousel slide mt-4" data-bs-ride="carousel">
                            <div class="carousel-indicators">
                                @foreach($project->projectSliders as $index => $slide)
                                    <button type="button" data-bs-target="#carouselExampleIndicators"
                                        data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}"
                                        aria-current="true" aria-label="Slide {{ $index+1 }}"></button>
                                @endforeach
                            </div>
                            <div class="carousel-inner">
                                @foreach($project->projectSliders as $index => $slide)
                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                        <img src="{{ asset('images/projects/sliders/' . $slide->image) }}"
                                             class="d-block w-100" alt="">
                                    </div>
                                @endforeach
                            </div>
                            <button class="carousel-control-prev" type="button"
                                data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button"
                                data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-4 px-0">
                <div class="p-4">

                    @if(count($functionalFeatures) > 1)
                        <h5 class="fst-italic text-center bg-light text-dark px-3 py-2 rounded-3 shadow-sm mt-1">Functional - Features</h5>
                        <ul class="list-group mt-3 small fst-italic shadow-sm">
                            @foreach($functionalFeatures as $feature)
                                <li class="list-group-item text-muted bg-transparent">{{ $feature }}</li>
                            @endforeach
                        </ul>
                    @endif

                    @if(count($technologies) > 1)
                        <h5 class="fst-italic text-center bg-light text-dark px-3 py-2 rounded-3 shadow-sm mt-3">Technology - Features</h5>
                        <ul class="list-group mt-3 small fst-italic shadow-sm">
                            @foreach($technologies as $tech)
                                <li class="list-group-item text-muted bg-transparent">{{ $tech }}</li>
                            @endforeach
                        </ul>
                    @endif

                    @if($project->project_url)
                        <div class="w-100 text-center mt-4">
                            <a href="{{ $project->project_url }}" target="_blank"
                                class="btn-theme outline wow fadeInUp animated w-100">
                                Go to Main Website</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

@include('frontend.reviews')

@include('frontend.contact', ['product' => null])

@endsection