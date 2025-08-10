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
                <button type="button" class="btn btn-secondary my-3" id="newBtn">Add new feature</button>
            </div>
            <div class="col-8">
                <h3 class="my-3">Features for: {{ $product->title }}</h3>
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
                        <h3 class="card-title" id="cardTitle">Add new feature</h3>
                    </div>
                    <div class="card-body">
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" class="form-control" id="codeid" name="codeid">
                            <input type="hidden" class="form-control" id="product_id" name="product_id" value="{{ $product->id }}">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="title" name="title" placeholder="Enter title" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Sort Order</label>
                                        <input type="number" class="form-control" id="sl" name="sl" value="0">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Feature Image</label>
                                        <input type="file" class="form-control-file" id="image" name="image" accept="image/*">
                                        <img id="preview-image" src="#" alt="" style="max-width: 300px; width: 100%; height: auto; margin-top: 20px;">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label>Icon (icon class)  
                                      <small style="font-weight: normal; font-size: 0.85em; margin-left: 5px;">
                                        See icons at 
                                        <a href="https://icon-sets.iconify.design/mdi-light/" target="_blank" rel="noopener noreferrer">
                                          mdi-light icons
                                        </a>
                                      </small>
                                    </label>
                                    <textarea class="form-control" id="icon" name="icon" rows="3" placeholder="Paste icon class (e.g. 'streamline-freehand:cash-payment-bill')"></textarea>
                                    <div class="mt-2" style="display: flex; align-items: center; gap: 10px;">
                                      <button type="button" id="checkIconBtn" class="btn btn-primary" onclick="checkIcon()">Check Icon</button>
                                      <div id="iconPreview"></div>
                                    </div>
                                  </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Description</label>
                                <textarea class="form-control summernote" id="description" name="description" rows="5" placeholder="Enter description"></textarea>
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
                        <h3 class="card-title">All Features</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table cell-border table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Image</th>
                                    <th>Title</th>
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

<script src="https://code.iconify.design/2/2.2.1/iconify.min.js"></script>
<script>
  async function checkIcon() {
    const input = document.getElementById('icon').value.trim();
    const preview = document.getElementById('iconPreview');

    if (!input) {
      preview.innerHTML = '<span style="color:red">Please enter an icon class.</span>';
      return;
    }

    const parts = input.split(':');
    if (parts.length !== 2) {
      preview.innerHTML = '<span style="color:red">Invalid icon format. Use provider:icon-name.</span>';
      return;
    }

    try {
      await Iconify.loadIcon(input);

      preview.innerHTML = `<iconify-icon icon="${input}" width="50" height="50"></iconify-icon>`;
    } catch (err) {
      preview.innerHTML = '<span style="color:red">Icon does not exist.</span>';
    }
  }
</script>

@endsection

@section('script')
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
      
      var url = "{{URL::to('/admin/products/features')}}";
      var upurl = "{{URL::to('/admin/products/features/update')}}";

      $("#addBtn").click(function(){
          var form_data = new FormData();
          form_data.append("product_id", $("#product_id").val());
          form_data.append("title", $("#title").val());
          form_data.append("icon", $("#icon").val());
          form_data.append("description", $("#description").val());
          form_data.append("sl", $("#sl").val());

          // Handle image upload
          var imageInput = document.getElementById('image');
          if(imageInput.files && imageInput.files[0]) {
              form_data.append("image", imageInput.files[0]);
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
          $("#cardTitle").text('Update this feature');
          codeid = $(this).data('id');
          info_url = url + '/'+codeid+'/edit';
          $.get(info_url,{},function(d){
              populateForm(d);
          });
      });

      function populateForm(data){
          $("#title").val(data.title);
          $("#icon").val(data.icon);
          $("#description").summernote('code', data.description);
          $("#sl").val(data.sl);
          $("#codeid").val(data.id);
          
          // Set preview image
          if (data.image) {
              $("#preview-image").attr("src", '/images/product-features/' + data.image).show();
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
          $('.summernote').summernote('reset');
          $('#preview-image').attr('src', '#');
          $("#cardTitle").text('Add new feature');
          $("#iconPreview").html('');
      }
      
      previewImage('#image', '#preview-image');

      // Status toggle
      $(document).on('change', '.toggle-status', function() {
          var feature_id = $(this).data('id');
          var status = $(this).prop('checked') ? 1 : 0;

          $.ajax({
              url: '/admin/products/features/status',
              method: "POST",
              data: {
                  feature_id: feature_id,
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
          if(!confirm('Are you sure you want to delete this feature?')) return;
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
              url: "{{ route('products.features.index', $product) }}",
              type: "GET",
              error: function (xhr, status, error) {
                  console.error(xhr.responseText);
              }
          },
          columns: [
              {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
              {data: 'image', name: 'image', orderable: false, searchable: false},
              {data: 'title', name: 'title'},
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