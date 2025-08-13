@extends('frontend.master')

@section('content')
<section>
    <div class="container-fluid titleBar py-4">
        <h1 class="display-3 text-center fw-bold mb-0 text-ternary">Get Quotation</h1>
    </div>
</section>

<section class="default contact-section wow fadeIn"
    style="background-image: url(&quot;../images/pattern-1.svg&quot;),linear-gradient(61deg, rgb(12, 29, 77) 46%, rgb(255, 163, 15) 94%); background-attachment: fixed;">
    <div class="container">
        <div class="row mt-2">
            <div class="col-lg-8 mx-auto">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <form action="{{ route('quotation.store') }}" method="POST" class="form-style fadeInUp">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <input type="text" name="first_name" class="form-control" placeholder="First Name *" value="{{ old('first_name', auth()->check() ? auth()->user()->name : '') }}" required>
                            @error('first_name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <input type="text" name="last_name" class="form-control" placeholder="Last Name *" value="{{ old('last_name') }}" required>
                            @error('last_name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <input type="email" name="email" class="form-control" placeholder="Email Address *" value="{{ old('email', auth()->check() ? auth()->user()->email : '') }}" required>
                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <input type="text" name="phone" class="form-control" placeholder="Phone Number *" value="{{ old('phone', auth()->check() ? auth()->user()->phone : '') }}" required>
                            @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <input type="text" name="company" class="form-control" placeholder="Company/Business Name *" value="{{ old('company') }}" required>
                            @error('company') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-6 form-group">
                            <input type="url" name="website" class="form-control" placeholder="Current Website (if any)" value="{{ old('website') }}">
                            @error('website') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-12 form-group">
                            <textarea name="dream_description" class="form-control" rows="3" placeholder="Describe Your Dream Website *" required>{{ old('dream_description') }}</textarea>
                            @error('dream_description') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-12 form-group">
                            <input type="text" name="timeline" class="form-control" placeholder="When do you need your website? (e.g. Within a week)" value="{{ old('timeline') }}">
                            @error('timeline') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-12 form-group">
                            <label class="text-white">What features do you need? (Select all that apply)</label><br>
                            @php
                                $features = [
                                    'Contact Forms', 'Online Booking', 'E-commerce/Shop', 'Photo Gallery',
                                    'Blog/News', 'Social Media Integration', 'Google Maps', 'Live Chat',
                                    'Customer Reviews', 'Email Newsletter', 'Multi-language', 'Custom Features'
                                ];
                            @endphp
                            @foreach($features as $feature)
                                <div class="form-check form-check-inline text-white">
                                    <input class="form-check-input" type="checkbox" name="features[]" value="{{ $feature }}"
                                        {{ is_array(old('features')) && in_array($feature, old('features')) ? 'checked' : '' }}>
                                    <label class="form-check-label">{{ $feature }}</label>
                                </div>
                            @endforeach
                            @error('features') <br><small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-12 form-group mt-3">
                            <textarea name="additional_info" class="form-control" rows="3" placeholder="Additional Information">{{ old('additional_info') }}</textarea>
                        </div>

                        {{-- CAPTCHA --}}
                        <div class="col-md-6 form-group">
                            <label class="text-white" id="captcha-question">What is ...?</label>
                            <input type="number" name="captcha" class="form-control" id="captcha-answer" placeholder="Your answer" required>
                            <small class="text-danger d-none" id="captcha-error">Incorrect answer. Please try again.</small>
                        </div>

                        <div class="col-12 form-group margin-b-none">
                            <button type="submit" id="submit-btn"
                                class="mt-4 border-0 d-block rounded-3 w-100 fs-5 text-uppercase btn-theme d-none">
                                Get Quotaion
                            </button>
                            <div id="sending-text" class="text-white mt-3 fw-bold d-none">Sending...</div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
    let num1 = Math.floor(Math.random() * 10) + 1;
    let num2 = Math.floor(Math.random() * 10) + 1;
    let correctAnswer = num1 + num2;

    document.getElementById('captcha-question').innerText = `What is ${num1} + ${num2}? *`;

    const answerInput = document.getElementById('captcha-answer');
    const submitBtn = document.getElementById('submit-btn');
    const errorMsg = document.getElementById('captcha-error');
    const sendingText = document.getElementById('sending-text');

    answerInput.addEventListener('input', function () {
        const userAnswer = parseInt(this.value);
        if (userAnswer === correctAnswer) {
            submitBtn.classList.remove('d-none');
            errorMsg.classList.add('d-none');
        } else {
            submitBtn.classList.add('d-none');
            if (this.value !== '') {
                errorMsg.classList.remove('d-none');
            } else {
                errorMsg.classList.add('d-none');
            }
        }
    });

    document.querySelector('form').addEventListener('submit', function () {
        submitBtn.classList.add('d-none');
        sendingText.classList.remove('d-none');
    });
</script>
@endsection