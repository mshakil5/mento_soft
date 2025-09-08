@extends('admin.master')

@section('content')

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
      <div class="row justify-content-md-center">
        <div class="col-md-10">
          <div class="mb-3">
            <a href="{{ url()->previous() }}" class="btn btn-secondary">
              <i class="fa fa-arrow-left"></i> Back
            </a>
          </div>

          <div class="card card-secondary">
            <div class="card-header">
              <h3 class="card-title">Email</h3>
            </div>
            
            <form id="createThisForm">
              <input type="hidden" name="serviceIds" value='@json($serviceIds)' id="serviceIds">
              @csrf
              <div class="card-body">

                <div class="text-center mb-4 company-name-container">
                    <h2>{{ $client->name }}</h2>
                    <h4>{{ $client->email }}</h4>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                      <div class="form-group">
                        <label>Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject">
                      </div>
                    </div>
                    <div class="col-sm-12">
                      <div class="form-group">
                        <label>Body</label>
                        <textarea name="body" id="body" cols="30" rows="5" class="form-control summernote">
                          <p>&nbsp;</p>
                          <p>&nbsp;</p>
                          {!! $mailFooter->mail_footer ?? '' !!}
                        </textarea>
                      </div>
                    </div>
                </div>

              </div>

              <div class="card-footer">
                <button type="submit" class="btn btn-secondary" id="sendEmailButton">Send</button>
                <div id="loader" style="display: none;">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Loading...
                </div>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
</section>

@endsection
@section('script')

<script>
  $(function() {
      $('#createThisForm').on('submit', function(event) {
          event.preventDefault();

          var subject = $('#subject').val();
          var body = $('#body').val();
          var sendButton = $('#sendEmailButton');
          var loader = $('#loader');
          var serviceIds = $('#serviceIds').val();

          if (!subject || !body) {
              error('Please fill in all fields.');
              return;
          }

          sendButton.prop('disabled', true);
          loader.show();

          $.ajax({
              url: "{{ route('client.email.send') }}",
              method: 'POST',
              data: {
                  _token: "{{ csrf_token() }}",
                  subject: subject,
                  body: body,
                  id: "{{ $client->id }}",
                  serviceIds: serviceIds
              },
              success: function(res) {
                  console.log(res);
                  success(res.message);
                  setTimeout(function() {
                      window.history.back();
                  }, 1000);
              },
              error: function(xhr) {
                  console.error(xhr.responseText);
                  pageTop();
                  if (xhr.responseJSON && xhr.responseJSON.errors)
                      error(Object.values(xhr.responseJSON.errors)[0][0]);
                  else
                      error();
              },
              complete: function() {
                  sendButton.prop('disabled', false);
                  loader.hide();
              }
          });
      });
  });
</script>

@endsection