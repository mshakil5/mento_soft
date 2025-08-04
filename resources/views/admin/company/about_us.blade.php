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
            <h3 class="card-title">About Us</h3>
          </div>

          <form action="{{ route('admin.aboutUs') }}" method="POST">
            @csrf
            <div class="card-body">
              <div class="form-group">
                <label>About Us <span class="text-danger">*</span></label>
                <textarea name="about_us" class="form-control summernote @error('about_us') is-invalid @enderror" rows="4">{!! $companyDetails->about_us !!}</textarea>
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