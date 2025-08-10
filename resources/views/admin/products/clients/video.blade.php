@extends('admin.master')

@section('content')
<!-- Main content -->
<section class="content" id="newBtnSection">
    <div class="container-fluid">
        <div class="row">
            <div class="col-2">
              <a href="{{ route('products.index') }}" class="btn btn-secondary my-3"><i class="fas fa-arrow-left me-1"></i> Back</a>
            </div>
            <div class="col-2">
                <button type="button" class="btn btn-secondary my-3" id="newBtn">Add New Video</button>
            </div>
            <div class="col-8">
                <h3 class="my-3">Client Videos for: {{ $product->title }}</h3>
            </div>
        </div>
    </div>
</section>

<section class="content mt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-10">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Add New Client Video</h3>
                    </div>
                    <div class="card-body">
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" class="form-control" id="codeid" name="codeid">
                            <input type="hidden" class="form-control" id="product_id" name="product_id" value="{{ $product->id }}">
                            
                            <div class="form-group">
                                <label>Client Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="client_name" name="client_name" placeholder="Enter client name" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Video <span class="text-danger">*</span></label>
                                <input type="file" class="filepond" id="video" name="video" accept="video/mp4,video/webm">
                                <small class="text-muted">Max 50MB, MP4 or WebM format</small>
                                <div id="video-preview" class="mt-2"></div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <button type="submit" id="addBtn" class="btn btn-secondary" value="Create">Create</button>
                        <button type="submit" id="FormCloseBtn" class="btn btn-default">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="content" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">All Client Videos</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table cell-border table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Video</th>
                                    <th>Client Name</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('script')

// Add these to the header section
<link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
<link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
<script src="https://unpkg.com/filepond/dist/filepond.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>

<script>
  FilePond.registerPlugin(
      FilePondPluginImagePreview,
      FilePondPluginFileValidateType
  );

  const pond = FilePond.create(document.querySelector('.filepond'), {
      acceptedFileTypes: ['video/mp4', 'video/webm'],
      fileValidateTypeDetectType: (source, type) => new Promise((resolve, reject) => {
          resolve(type);
      }),
      maxFileSize: '50MB',
      labelIdle: 'Drag & Drop your video or <span class="filepond--label-action">Browse</span>',
      labelFileTypeNotAllowed: 'File of invalid type',
      fileValidateTypeLabelExpectedTypes: 'Expects MP4 or WebM',
      allowProcess: false, // Disable auto upload
      allowRemove: true,
      allowRevert: false
  });
</script>

<script>
  $(document).ready(function () {
      $("#addThisFormContainer").hide();
      $("#newBtn").click(function(){
          clearform();
          $("#newBtn").hide(100);
          $("#addThisFormContainer").show(300);
      });
      $("#FormCloseBtn").click(function(){
          $("#addThisFormContainer").hide(200);
          $("#newBtn").show(100);
          clearform();
      });

      $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
      
      var url = "{{URL::to('/admin/products/client-videos')}}";
      var upurl = "{{URL::to('/admin/products/client-videos/update')}}";

      // Preview video before upload
      $('#video').change(function() {
          var file = this.files[0];
          if (file) {
              var videoNode = document.createElement('video');
              videoNode.controls = true;
              videoNode.width = 300;
              
              var sourceNode = document.createElement('source');
              sourceNode.src = URL.createObjectURL(file);
              sourceNode.type = file.type;
              
              videoNode.appendChild(sourceNode);
              $('#video-preview').html(videoNode);
          }
      });

      $("#addBtn").click(function(){
          var form_data = new FormData();
          form_data.append("product_id", $("#product_id").val());
          form_data.append("client_name", $("#client_name").val());
          
          // Handle video upload
        if (pond.getFiles().length > 0) {
            form_data.append("video", pond.getFiles()[0].file);
        }


          if($(this).val() == 'Create') {
              // Create
              $.ajax({
                  url: url,
                  method: "POST",
                  contentType: false,
                  processData: false,
                  data: form_data,
                  success: function(res) {
                    clearform();
                    success(res.message);
                    pageTop();
                    reloadTable();
                  },
                  error: function(xhr) {
                    console.error(xhr.responseText);
                    pageTop();
                    if (xhr.responseJSON && xhr.responseJSON.errors)
                      error(Object.values(xhr.responseJSON.errors)[0][0]);
                    else
                      error('Something went wrong');
                  }
              });
          } else {
              // Update
              form_data.append("codeid", $("#codeid").val());
              
              $.ajax({
                  url: upurl,
                  type: "POST",
                  dataType: 'json',
                  contentType: false,
                  processData: false,
                  data: form_data,
                  success: function(res) {
                    clearform();
                    success(res.message);
                    pageTop();
                    reloadTable();
                  },
                  error: function(xhr) {
                    console.error(xhr.responseText);
                    pageTop();
                    if (xhr.responseJSON && xhr.responseJSON.errors)
                      error(Object.values(xhr.responseJSON.errors)[0][0]);
                    else
                      error('Something went wrong');
                  }
              });
          }
      });

      //Edit
      $("#contentContainer").on('click','.edit', function(){
          $("#cardTitle").text('Update Client Video');
          codeid = $(this).data('id');
          info_url = url + '/'+codeid+'/edit';
          $.get(info_url,{},function(d){
              populateForm(d);
          });
      });

      function populateForm(data){
          $("#client_name").val(data.client_name);
          $("#codeid").val(data.id);
          $("#addBtn").val('Update');
          $("#addBtn").html('Update');
          $("#addThisFormContainer").show(300);
          $("#newBtn").hide(100);

          if(data.video_path) {
              $('#video-preview').html(`
                  <div class="alert alert-info">
                      Current video: ${data.video_path}
                      <button type="button" class="close" id="remove-video" data-id="${data.id}">
                          <span>&times;</span>
                      </button>
                  </div>
              `);
          }
      }
      
      function clearform(){
          $('#createThisForm')[0].reset();
          $("#addBtn").val('Create');
          $("#addBtn").html('Create');
          $("#addThisFormContainer").slideUp(200);
          $("#newBtn").slideDown(200);
          $('#video-preview').html('');
          pond.removeFiles();
          $("#cardTitle").text('Add New Client Video');
      }

      // Status toggle
      $(document).on('change', '.toggle-status', function() {
          var video_id = $(this).data('id');
          var status = $(this).prop('checked') ? 1 : 0;

          $.ajax({
              url: "{{ route('products.client-videos.status') }}",
              method: "POST",
              data: {
                  video_id: video_id,
                  status: status,
                  _token: "{{ csrf_token() }}"
              },
              success: function(res) {
                success(res.message);
                reloadTable();
              },
              error: function(xhr, status, error) {
                console.error(xhr.responseText);
                error('Failed to update status');
              }
          });
      });

      //Delete
      $("#contentContainer").on('click','.delete', function(){
          if(!confirm('Are you sure you want to delete this video?')) return;
          codeid = $(this).data('id');
          info_url = url + '/'+codeid;
          $.ajax({
              url: info_url,
              method: "GET",
              success: function(res) {
                clearform();
                success(res.message);
                pageTop();
                reloadTable();
              },
              error: function(xhr) {
                console.error(xhr.responseText);
                pageTop();
                if (xhr.responseJSON && xhr.responseJSON.errors)
                  error(Object.values(xhr.responseJSON.errors)[0][0]);
                else
                  error();
              }
          });
      });

      var table = $('#example1').DataTable({
          processing: true,
          serverSide: true,
          ajax: {
              url: "{{ route('products.clients.video', $product) }}",
              type: "GET",
              error: function (xhr, status, error) {
                  console.error(xhr.responseText);
              }
          },
          columns: [
              {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
              {data: 'video_preview', name: 'video_preview', orderable: false, searchable: false},
              {data: 'client_name', name: 'client_name'},
              {data: 'status', name: 'status', orderable: false, searchable: false},
              {data: 'action', name: 'action', orderable: false, searchable: false},
          ],
          responsive: true,
          lengthChange: false,
          autoWidth: false,
      });

      function reloadTable() {
        table.ajax.reload(null, false);
      }
  });
</script>
@endsection