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
                        <h3 class="card-title" id="cardTitle">Add new product</h3>
                    </div>
                    <div class="card-body">
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" class="form-control" id="codeid" name="codeid">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="title" name="title" placeholder="Enter title" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Sub Title</label>
                                        <input type="text" class="form-control" id="sub_title" name="sub_title" placeholder="Enter sub title">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Product URL</label>
                                        <input type="url" class="form-control" id="url" name="url" placeholder="Enter product URL">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Sort Order</label>
                                        <input type="number" class="form-control" id="sl" name="sl" value="0">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Short Description</label>
                                <textarea class="form-control" id="short_description" name="short_description" rows="3" placeholder="Enter short description"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Feature Description</label>
                                <textarea class="form-control" id="feature_description" name="feature_description" rows="3" placeholder="Enter feature description"></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label>Long Description</label>
                                <textarea class="form-control summernote" id="long_description" name="long_description" rows="5" placeholder="Enter long description"></textarea>
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
                                        <label>Product Video</label>
                                        <input type="file" class="filepond" id="video" name="video" accept="video/mp4,video/webm">
                                        <div id="video-preview" class="mt-2"></div>
                                    </div>
                                </div>
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
                                        <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" placeholder="e.g. product, item, shop">
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
                        <h3 class="card-title">All Products</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table cell-border table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Thumbnail</th>
                                    <th>Title</th>
                                    <th>Sort No.</th>
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
<!-- Include FilePond -->
<link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
<link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
<script src="https://unpkg.com/filepond/dist/filepond.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>

<script>
    // Initialize FilePond for preview only
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
          pond.removeFiles();
      });

      $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
      
      var url = "{{URL::to('/admin/products')}}";
      var upurl = "{{URL::to('/admin/products/update')}}";

      $("#addBtn").click(function(){
          var form_data = new FormData();
          form_data.append("title", $("#title").val());
          form_data.append("sub_title", $("#sub_title").val());
          form_data.append("url", $("#url").val());
          form_data.append("short_description", $("#short_description").val());
          form_data.append("feature_description", $("#feature_description").val());
          form_data.append("long_description", $("#long_description").val());
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
              form_data.append("video", pond.getFiles()[0].file);
          }

          // Handle meta image upload
          var metaImageInput = document.getElementById('meta_image');
          if(metaImageInput.files && metaImageInput.files[0]) {
              form_data.append("meta_image", metaImageInput.files[0]);
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

      //Edit
      $("#contentContainer").on('click','.edit', function(){
          $("#cardTitle").text('Update this product');
          codeid = $(this).data('id');
          info_url = url + '/'+codeid+'/edit';
          $.get(info_url,{},function(d){
              populateForm(d);
          });
      });

      function populateForm(data){
          $("#title").val(data.title);
          $("#sub_title").val(data.sub_title);
          $("#url").val(data.url);
          $("#short_description").val(data.short_description);
          $("#feature_description").val(data.feature_description);
          $("#long_description").summernote('code', data.long_description);
          $("#sl").val(data.sl);
          $("#meta_title").val(data.meta_title);
          $("#meta_description").val(data.meta_description);
          $("#meta_keywords").val(data.meta_keywords);
          $("#codeid").val(data.id);
          
          // Image preview
          if (data.image) {
              $("#preview-image").attr("src", '/images/products/' + data.image).show();
              if(!$("#preview-image").next('.remove-file').length){
                  $("<button/>", {
                      type: "button",
                      class: "btn btn-danger remove-file position-absolute top-0 end-0",
                      html: '<i class="fas fa-times"></i>',
                      "data-filename": data.image,
                      "data-path": "images/products",
                      "data-model": "Product",
                      "data-id": data.id,
                      "data-col": "image"
                  }).insertAfter("#preview-image");
                  $("#preview-image").parent().css('position', 'relative');
              }
          } else {
              $("#preview-image").attr("src", '').hide();
              $("#preview-image").next('.remove-file').remove();
          }

          // Meta Image preview
          if (data.meta_image) {
              $("#preview-meta-image").attr("src", '/images/products/meta/' + data.meta_image).show();
              if(!$("#preview-meta-image").next('.remove-file').length){
                  $("<button/>", {
                      type: "button",
                      class: "btn btn-danger remove-file position-absolute top-0 end-0",
                      html: '<i class="fas fa-times"></i>',
                      "data-filename": data.meta_image,
                      "data-path": "images/products/meta",
                      "data-model": "Product",
                      "data-id": data.id,
                      "data-col": "meta_image"
                  }).insertAfter("#preview-meta-image");
                  $("#preview-meta-image").parent().css('position', 'relative');
              }
          } else {
              $("#preview-meta-image").attr("src", '').hide();
              $("#preview-meta-image").next('.remove-file').remove();
          }

          // Video preview
          $(".video-remove-btn").remove();
          if (data.video) {
              $("<button/>", {
                  type: "button",
                  class: "btn btn-danger remove-file video-remove-btn",
                  html: '<i class="fas fa-times"></i> Remove Video',
                  "data-filename": data.video,
                  "data-path": "images/products/videos",
                  "data-model": "Product",
                  "data-id": data.id,
                  "data-col": "video"
              }).insertAfter("#video");
          }

          $("#addBtn").val('Update').html('Update');
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
          $('#preview-image').attr('src', '#');
          $('#preview-meta-image').attr('src', '#');
          $("#cardTitle").text('Add new product');
          $('#createThisForm').find('.remove-file').remove();
          reloadTable();
      }
      
      previewImage('#image', '#preview-image');
      previewImage('#meta_image', '#preview-meta-image');

      // Status toggle
      $(document).on('change', '.toggle-status', function() {
          var product_id = $(this).data('id');
          var status = $(this).prop('checked') ? 1 : 0;

          $.ajax({
              url: '/admin/products/status',
              method: "POST",
              data: {
                  product_id: product_id,
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
          if(!confirm('Are you sure you want to delete this product?')) return;
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
              url: "{{ route('products.index') }}",
              type: "GET",
              error: function (xhr, status, error) {
                  console.error(xhr.responseText);
              }
          },
          columns: [
              {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
              {data: 'thumbnail', name: 'thumbnail', orderable: false, searchable: false},
              {data: 'title', name: 'title'},
              { data: 'sl', name: 'sl' },
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