@php
  $contact = App\Models\Master::where('name', 'contact')->first();
  $company = \App\Models\CompanyDetails::first();
@endphp

<section class="default contact-section wow fadeIn"
    style="background-image: url(&quot;../images/pattern-1.svg&quot;),linear-gradient(61deg, rgb(12, 29, 77) 46%, rgb(255, 163, 15) 94%); background-attachment: fixed;"
    id="contact">
    <div class="container">
        <div class="row  px-2">
            <div class="col-lg-12 text-center">
                <h2 class=" text-light text-uppercase fw-bold title-font wow bounce mb-0 secTtile">
                    Get in touch
                </h2>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-lg-6 d-flex align-items-center text-light">
                <div class="text-center text-md-start mb-4">
                    <h1 class="display-4 text-capitalize fw-bold fadeInLeft  wow">{{ $contact->short_title }}</h1>
                    <h6 class="lh-3">{{ $contact->long_title }}</h6>
                    <div class="my-3 d-flex gap-3 flex-wrap align-items-center w-100 text-center fs-6">
                        <span class="d-flex gap-2  align-items-center fadeInUp wow  "><iconify-icon
                                icon="iconamoon:email-thin" width="25" height="25"
                                style="color: #ff961d"></iconify-icon>
                            {{ $company->email1 }}</span>
                        <span class="d-flex gap-2  align-items-center fadeInUp wow  "><iconify-icon
                                icon="lets-icons:phone-light" width="25" height="25"
                                style="color: #ff961d"></iconify-icon> {{ $company->phone1 }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 ">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <form action="{{ route('contact.store') }}" method="POST" class="form-style fadeInUp">
                    @csrf
                    <div class="row">
                      @php
                          $product = $product ?? null;
                      @endphp

                        <h3 class="text-white">Speak to an expert 
                            @if(isset($product))
                                about <b><a href="{{ route('productDetails', $product->slug) }}" class="text-light text-decoration-underline">{{ $product->title }}</a></b>
                            @endif
                        </h3>
                        <input type="hidden" name="product_id" value="{{ $product->id ?? '' }}">
                        <div class="col-md-6 form-group">
                            <input type="text" name="first_name" class="form-control" placeholder="First Name *" value="{{ old('first_name', auth()->check() ? auth()->user()->name : '') }}" required>
                            @error('first_name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 form-group">
                            <input type="text" name="last_name" class="form-control" placeholder="Last Name *" value="{{ old('last_name') }}" required>
                            @error('last_name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 form-group">
                            <input type="email" name="email" class="form-control" placeholder="E-mail *" value="{{ old('email', auth()->check() ? auth()->user()->email : '') }}" required>
                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 form-group">
                            <input type="number" name="phone" class="form-control" placeholder="Phone *" value="{{ old('phone', auth()->check() ? auth()->user()->phone : '') }}" required>
                            @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-12 form-group">
                            <textarea name="message" class="textarea form-control" rows="3" placeholder="Message *" required>{{ old('message') }}@if(isset($product))I want to see a demo of {{ $product->title ?? '' }}@endif</textarea>
                            @error('message') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="text-white" id="captcha-question">What is ...?</label>
                            <input type="number" name="captcha" class="form-control" id="captcha-answer" placeholder="Your answer" required>
                            <small class="text-danger d-none" id="captcha-error">Incorrect answer. Please try again.</small>
                        </div>

                        <div class="col-12 form-group margin-b-none">
                            <button type="submit" id="submit-btn"
                                class="mt-4 border-0 d-block rounded-3 w-100 fs-5 text-uppercase btn-theme d-none">
                                Send Message
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

    document.querySelector('form').addEventListener('submit', function (e) {
        submitBtn.classList.add('d-none');
        sendingText.classList.remove('d-none');
    });
</script>