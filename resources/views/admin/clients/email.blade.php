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
                    @if(request('type') === 'service')
                    <div class="col-sm-12">
                      <div class="form-group">
                        <label>Select Unpaid Services</label>
                          <select id="serviceSelect" class="form-control select2" multiple>
                            @foreach($services as $service)
                              <option value="{{ $service->id }}">
                                {{ $service->serviceType->name ?? 'N/A' }}
                                (Project: {{ $service->project->title ?? 'N/A' }})
                                - £{{ number_format($service->amount, 2) }}
                              </option>
                            @endforeach
                          </select>
                      </div>
                    </div>
                    @endif
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
      $('#serviceSelect').on('change', function() {
          let selectedIds = $(this).val() || [];
          let allServices = {
              @foreach($services as $service)
              {{ $service->id }}: `
                  <strong>Service:</strong> {{ $service->serviceType->name ?? 'N/A' }}<br>
                  <strong>Project:</strong> {{ $service->project->title ?? 'N/A' }}<br>
                  <strong>Amount:</strong> £{{ number_format($service->amount, 2) }}<br>
                  <strong>Start Date:</strong> {{ $service->start_date ? \Carbon\Carbon::parse($service->start_date)->format('d-m-Y') : '' }}<br>
                  <strong>End Date:</strong> {{ $service->start_date ? \Carbon\Carbon::parse($service->end_date)->format('d-m-Y') : '' }}
              `,
              @endforeach
          };

          let content = "";
          if(selectedIds.length > 0) {
              content = "<h4>Outstanding Services</h4>";
              selectedIds.forEach(function(id) {
                  content += "<div style='margin-bottom:15px;'>" + allServices[id] + "</div>";
              });
          }

          let editorContent = $('#body').summernote('code');

          let parts = editorContent.split(`{!! $mailFooter->mail_footer ?? '' !!}`);
          let beforeFooter = parts[0];
          let footer = parts[1] ? `{!! $mailFooter->mail_footer ?? '' !!}` + parts[1] : `{!! $mailFooter->mail_footer ?? '' !!}`;

          beforeFooter = beforeFooter.replace(/<h4>Outstanding Services<\/h4>[\s\S]*?(?=$)/, '');

          $('#body').summernote('code', beforeFooter + content + footer);
      });
  });
</script>

@endsection