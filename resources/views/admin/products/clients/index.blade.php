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
                <button type="button" class="btn btn-secondary my-3" id="newBtn">Add new client</button>
            </div>
            <div class="col-8">
                <h3 class="my-3">Clients using: {{ $product->title }}</h3>
            </div>
        </div>
    </div>
</section>

<section class="content mt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-6">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Add new client</h3>
                    </div>
                    <div class="card-body">
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" class="form-control" id="product_id" name="product_id" value="{{ $product->id }}">
                            
                            <div class="form-group">
                                <label>Client Logo/Image <span class="text-danger">*</span></label>
                                <input type="file" class="form-control-file" id="image" name="image" accept="image/*" required>
                                <img id="preview-image" src="#" alt="" style="max-width: 300px; width: 100%; height: auto; margin-top: 20px;">
                            </div>
                            
                            <div class="form-group">
                                <label>Sort Order</label>
                                <input type="number" class="form-control" id="sl" name="sl" value="0">
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <button type="submit" id="addBtn" class="btn btn-secondary">Add Client</button>
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
                        <h3 class="card-title">All Clients</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table cell-border table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Image</th>
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
      
      var url = "{{URL::to('/admin/products/clients')}}";

      $("#addBtn").click(function(){
          var form_data = new FormData();
          form_data.append("product_id", $("#product_id").val());
          form_data.append("sl", $("#sl").val());

          // Handle image upload
          var imageInput = document.getElementById('image');
          if(imageInput.files && imageInput.files[0]) {
              form_data.append("image", imageInput.files[0]);
          }

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
      });

      function clearform(){
          $('#createThisForm')[0].reset();
          $("#addThisFormContainer").slideUp(200);
          $("#newBtn").slideDown(200);
          $('#preview-image').attr('src', '#');
      }
      
      previewImage('#image', '#preview-image');

      // Status toggle
      $(document).on('change', '.toggle-status', function() {
          var client_id = $(this).data('id');
          var status = $(this).prop('checked') ? 1 : 0;

          $.ajax({
              url: '/admin/products/clients/status',
              method: "POST",
              data: {
                  client_id: client_id,
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
          if(!confirm('Are you sure you want to delete this client?')) return;
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
              url: "{{ route('products.clients.index', $product) }}",
              type: "GET",
              error: function (xhr, status, error) {
                  console.error(xhr.responseText);
              }
          },
          columns: [
              {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
              {data: 'image', name: 'image', orderable: false, searchable: false},
              {data: 'sl', name: 'sl'},
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