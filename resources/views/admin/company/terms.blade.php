@extends('admin.master')

@section('content')
<section class="content pt-3">
  <div class="container-fluid">
    <div class="row justify-content-md-center">
      <div class="col-md-12">

        @if(session('success'))
          <div class="alert alert-success pt-3 mb-3">{{ session('success') }}</div>
        @endif

        @if($errors->any())
          <div class="alert alert-danger mb-3">
            <ul class="mb-0">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <div class="card card-secondary">
          <div class="card-header">
            <h3 class="card-title">Terms and Conditions</h3>
          </div>

          <form action="{{ route('admin.terms-and-conditions') }}" method="POST">
            @csrf
            <div class="card-body">
              <div class="form-group">
                <label>Terms and Conditions <span class="text-danger">*</span></label>
                <textarea name="terms_and_conditions" class="form-control summernote @error('terms_and_conditions') is-invalid @enderror" rows="4">{!! $companyDetails->terms_and_conditions !!}</textarea>
              </div>
            </div>

            <div class="card-footer">
              <button type="submit" class="btn btn-secondary">Update</button>
            </div>
          </form>
        </div>

      </div>
    </div>
  </div>
</section>
@endsection