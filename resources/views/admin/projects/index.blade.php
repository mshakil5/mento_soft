@extends('admin.master')

@section('content')
<!-- Main content -->
<section class="content" id="newBtnSection">
    <div class="container-fluid">
        <div class="row">
            <div class="col-2">
                <button type="button" class="btn btn-secondary my-3" id="newBtn">Add new</button>
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
                        <h3 class="card-title" id="cardTitle">Add new project</h3>
                    </div>
                    <div class="card-body">
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" class="form-control" id="codeid" name="codeid">
                            
                            <div class="row">
                                <div class="col-md-6 d-none">
                                    <div class="form-group">
                                        <label>Service <span class="text-danger">*</span></label>
                                        <select class="form-control" id="service_id" name="service_id" required>
                                            <option value="">Select Service</option>
                                            @foreach($services as $service)
                                            <option value="{{ $service->id }}">{{ $service->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Project Type <span class="text-danger">*</span></label>
                                        <select class="form-control" id="project_type_id" name="project_type_id" required>
                                            <option value="">Select Project Type</option>
                                            @foreach($projectTypes as $projectType)
                                            <option value="{{ $projectType->id }}">{{ $projectType->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="title" name="title" placeholder="Enter title" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Sub Title</label>
                                        <input type="text" class="form-control" id="sub_title" name="sub_title" placeholder="Enter sub title">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Project URL</label>
                                        <input type="url" class="form-control" id="project_url" name="project_url" placeholder="Enter project URL">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Short Description</label>
                                <textarea class="form-control" id="short_desc" name="short_desc" rows="3" placeholder="Enter short description"></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label>Long Description</label>
                                <textarea class="form-control summernote" id="long_desc" name="long_desc" rows="5" placeholder="Enter long description"></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label>Technologies Used (comma separated)</label>
                                <textarea class="form-control" id="technologies_used" name="technologies_used" rows="2" placeholder="Enter technologies used"></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label>Functional Features (comma separated)</label>
                                <textarea class="form-control" id="functional_features" name="functional_features" rows="2" placeholder="Enter functional features"></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Main Image (1200x...)</label>
                                        <input type="file" class="form-control-file" id="image" name="image" accept="image/*">
                                        <img id="preview-image" src="#" alt="" style="max-width: 300px; width: 100%; height: auto; margin-top: 20px;">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Demo Video</label>
                                        <input type="file" class="filepond" id="demo_video" name="demo_video" accept="video/mp4,video/webm">
                                        <div id="video-preview" class="mt-2"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Project Sliders</label>
                                <input type="file" class="filepond-multiple" name="slider_images[]" multiple>
                                <div id="slider-preview" class="row mt-3"></div>
                            </div>
                            
                            <div class="form-group">
                                <label>Sort Order</label>
                                <input type="number" class="form-control" id="sl" name="sl" value="0">
                            </div>
                            
                            <!-- Meta Fields Section -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h4>SEO Meta Fields</h4>
                                    <hr>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Meta Title</label>
                                        <input type="text" class="form-control" id="meta_title" name="meta_title" placeholder="Enter meta title">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Meta Description</label>
                                        <textarea class="form-control" id="meta_description" name="meta_description" rows="3" placeholder="Enter meta description"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Meta Keywords (comma separated)</label>
                                        <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" placeholder="e.g. project, web, design">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Meta Image (1200x630 recommended)</label>
                                        <input type="file" class="form-control-file" id="meta_image" name="meta_image" accept="image/*">
                                        <img id="preview-meta-image" src="#" alt="" style="max-width: 300px; width: 100%; height: auto; margin-top: 20px;">
                                    </div>
                                </div>
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
                        <h3 class="card-title">All Projects</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table cell-border table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Thumbnail</th>
                                    <th>Title</th>
                                    {{-- <th>Service</th> --}}
                                    <th>Status</th>
                                    {{-- <th>Featured</th> --}}
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
<!-- Include FilePond -->
<link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
<link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
<script src="https://unpkg.com/filepond/dist/filepond.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-resize/dist/filepond-plugin-image-resize.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-encode/dist/filepond-plugin-file-encode.js"></script>

<script>
    // Initialize FilePond for preview only
    FilePond.registerPlugin(
        FilePondPluginImagePreview,
        FilePondPluginFileValidateType,
        FilePondPluginImageResize,
        FilePondPluginFileEncode
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

    const sliderPond = FilePond.create(document.querySelector('.filepond-multiple'), {
        acceptedFileTypes: ['image/*'],
        allowMultiple: true,
        maxFiles: 10,
        storeAsFile: true,
        labelIdle: 'Drag & Drop slider images or <span class="filepond--label-action">Browse</span>'
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
          pond.removeFiles();
      });

      $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
      
      var url = "{{URL::to('/admin/projects')}}";
      var upurl = "{{URL::to('/admin/projects/update')}}";

      $("#addBtn").click(function(){
          var form_data = new FormData();
          form_data.append("service_id", $("#service_id").val());
          form_data.append("project_type_id", $("#project_type_id").val());
          form_data.append("title", $("#title").val());
          form_data.append("sub_title", $("#sub_title").val());
          form_data.append("project_url", $("#project_url").val());
          form_data.append("short_desc", $("#short_desc").val());
          form_data.append("long_desc", $("#long_desc").val());
          form_data.append("technologies_used", $("#technologies_used").val());
          form_data.append("functional_features", $("#functional_features").val());
          form_data.append("sl", $("#sl").val());
          form_data.append("meta_title", $("#meta_title").val());
          form_data.append("meta_description", $("#meta_description").val());
          form_data.append("meta_keywords", $("#meta_keywords").val());

          // Handle image upload
          var imageInput = document.getElementById('image');
          if(imageInput.files && imageInput.files[0]) {
              form_data.append("image", imageInput.files[0]);
          }

          // Handle FilePond video upload (only when files are added)
          if (pond.getFiles().length > 0) {
              form_data.append("demo_video", pond.getFiles()[0].file);
          }

          // Handle meta image upload
          var metaImageInput = document.getElementById('meta_image');
          if(metaImageInput.files && metaImageInput.files[0]) {
              form_data.append("meta_image", metaImageInput.files[0]);
          }

          if (sliderPond.getFiles().length > 0) {
              sliderPond.getFiles().forEach((file, index) => {
                  form_data.append(`slider_images[${index}]`, file.file);
              });
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
                      error();
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
                      error();
                  }
              });
          }
      });

      $(document).on('click', '.delete-slider', function() {
          if (!confirm('Are you sure you want to delete this slider image?')) return;
          
          var sliderId = $(this).data('id');
          $.ajax({
              url: '/admin/projects/sliders/' + sliderId,
              method: 'DELETE',
              data: {
                  _token: "{{ csrf_token() }}"
              },
              success: function(res) {
                  if (res.success) {
                      $('.slider-preview-item[data-id="' + sliderId + '"]').parent().remove();
                      success(res.message);
                  }
              },
              error: function(xhr) {
                  console.error(xhr.responseText);
                  error('Failed to delete slider');
              }
          });
      });

      //Edit
      $("#contentContainer").on('click','.edit', function(){
          $("#cardTitle").text('Update this project');
          codeid = $(this).data('id');
          info_url = url + '/'+codeid+'/edit';
          $.get(info_url,{},function(d){
              populateForm(d);
          });
      });

      function populateForm(data){
          $("#service_id").val(data.service_id);
          $("#project_type_id").val(data.project_type_id);
          $("#title").val(data.title);
          $("#sub_title").val(data.sub_title);
          $("#project_url").val(data.project_url);
          $("#short_desc").val(data.short_desc);
          $("#long_desc").summernote('code', data.long_desc);
          $("#technologies_used").val(data.technologies_used);
          $("#functional_features").val(data.functional_features);
          $("#sl").val(data.sl);
          $("#meta_title").val(data.meta_title);
          $("#meta_description").val(data.meta_description);
          $("#meta_keywords").val(data.meta_keywords);
          $("#codeid").val(data.id);
          
          // Set preview images
          if (data.image) {
              $("#preview-image").attr("src", '/images/projects/' + data.image).show();
          }
          if (data.thumbnail_image) {
              $("#preview-thumbnail").attr("src", '/images/projects/thumbnails/' + data.thumbnail_image).show();
          }
          if (data.meta_image) {
              $("#preview-meta-image").attr("src", '/images/projects/meta/' + data.meta_image).show();
          }
          
          // Handle video preview
          if (data.demo_video) {
              $("#video-preview").html(`
                  <div class="alert alert-info">
                      Current video: ${data.demo_video}
                      <button type="button" class="close" id="remove-video" data-id="${data.id}">
                          <span>&times;</span>
                      </button>
                  </div>
              `);
          }

          $('#slider-preview').empty();
          if (data.project_sliders && data.project_sliders.length > 0) {
              data.project_sliders.forEach(function(slider) {
                  $('#slider-preview').append(`
                      <div class="col-md-3 mb-3">
                          <div class="slider-preview-item" data-id="${slider.id}">
                              <img src="/images/projects/sliders/${slider.image}" class="img-fluid">
                              <button class="btn btn-sm btn-danger btn-block mt-1 delete-slider" data-id="${slider.id}">Delete</button>
                          </div>
                      </div>
                  `);
              });
          }

          $("#addBtn").val('Update');
          $("#addBtn").html('Update');
          $("#addThisFormContainer").show(300);
          $("#newBtn").hide(100);
      }
      
      function clearform(){
          $('#createThisForm')[0].reset();
          $("#addBtn").val('Create');
          $("#addBtn").html('Create');
          $("#addThisFormContainer").slideUp(200);
          $("#newBtn").slideDown(200);
          $('#video-preview').html('');
          $('.summernote').summernote('reset');
          pond.removeFiles();
          sliderPond.removeFiles();
          $('#slider-preview').empty();
          $('#preview-image').attr('src', '#');
          $('#preview-meta-image').attr('src', '#');
          $("#cardTitle").text('Add new project');
      }
      
      previewImage('#image', '#preview-image');
      previewImage('#meta_image', '#preview-meta-image');

      // Status toggle
      $(document).on('change', '.toggle-status', function() {
          var project_id = $(this).data('id');
          var status = $(this).prop('checked') ? 1 : 0;

          $.ajax({
              url: '/admin/projects/status',
              method: "POST",
              data: {
                  project_id: project_id,
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

      // Featured toggle
      $(document).on('change', '.toggle-featured', function() {
          var project_id = $(this).data('id');
          var is_featured = $(this).prop('checked') ? 1 : 0;

          $.ajax({
              url: '/admin/projects/featured',
              method: "POST",
              data: {
                  project_id: project_id,
                  is_featured: is_featured,
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
          if(!confirm('Are you sure you want to delete this project?')) return;
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
              url: "{{ route('projects.index') }}",
              type: "GET",
              error: function (xhr, status, error) {
                  console.error(xhr.responseText);
              }
          },
          columns: [
              {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
              {data: 'thumbnail', name: 'thumbnail', orderable: false, searchable: false},
              {data: 'title', name: 'title'},
              // {data: 'service', name: 'service'},
              {data: 'status', name: 'status', orderable: false, searchable: false},
              // {data: 'featured', name: 'featured', orderable: false, searchable: false},
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