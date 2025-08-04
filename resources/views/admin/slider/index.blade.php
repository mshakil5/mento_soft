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
<!-- /.content -->


<section class="content mt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-8">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Add new data</h3>
                    </div>
                    <div class="card-body">
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" class="form-control" id="codeid" name="codeid">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Title</label>
                                        <input type="text" class="form-control" id="title" name="title" placeholder="Enter title">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Sub Title</label>
                                        <input type="text" class="form-control" id="sub_title" name="sub_title" placeholder="Enter sub title">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Link</label>
                                        <input type="url" class="form-control" id="link" name="link" placeholder="Enter link">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-10">
                                    <div class="form-group">
                                        <label for="feature-img">Slider Image <span style="color: red;">*</span></label>
                                        <input type="file" class="form-control-file" id="image" accept="image/*">
                                        <img id="preview-image" src="#" alt="" style="max-width: 300px; width: 100%; height: auto; margin-top: 20px;">
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
                        <h3 class="card-title">All Data</h3>
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
      //
      var url = "{{URL::to('/admin/slider')}}";
      var upurl = "{{URL::to('/admin/slider-update')}}";

      let table = $('#example1').DataTable({
          processing: true,
          serverSide: true,
          ajax: '{{ route("allslider") }}',
          columns: [
              { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
              { data: 'image', name: 'image', orderable: false, searchable: false },
              { data: 'title', name: 'title' },
              { data: 'status', name: 'status', orderable: false, searchable: false },
              { data: 'action', name: 'action', orderable: false, searchable: false },
          ],
          responsive: true,
          lengthChange: false,
          autoWidth: false,
      });

      function reloadTable() {
        table.ajax.reload(null, false);
      }

      $("#addBtn").click(function(){

          //create
          if($(this).val() == 'Create') {
              var form_data = new FormData();
              form_data.append("title", $("#title").val());
              form_data.append("sub_title", $("#sub_title").val());
              form_data.append("link", $("#link").val());

              var featureImgInput = document.getElementById('image');
                if(featureImgInput.files && featureImgInput.files[0]) {
                    form_data.append("image", featureImgInput.files[0]);
                }

              $.ajax({
                url: url,
                method: "POST",
                contentType: false,
                processData: false,
                data:form_data,
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
          //create  end

          //Update
          if($(this).val() == 'Update'){
              var form_data = new FormData();
              form_data.append("title", $("#title").val());
              form_data.append("sub_title", $("#sub_title").val());
              form_data.append("link", $("#link").val());

              var featureImgInput = document.getElementById('image');
                if(featureImgInput.files && featureImgInput.files[0]) {
                    form_data.append("image", featureImgInput.files[0]);
                }

              form_data.append("codeid", $("#codeid").val());
              
              $.ajax({
                  url:upurl,
                  type: "POST",
                  dataType: 'json',
                  contentType: false,
                  processData: false,
                  data:form_data,
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
        //Update  end
      });
      //Edit
      $("#contentContainer").on('click','.edit', function(){
          $("#cardTitle").text('Update this data');
          let codeid = $(this).data('id');
          info_url = url + '/'+codeid+'/edit';
          $.get(info_url,{},function(d){
              populateForm(d);
              pageTop();
          });
      });
      //Edit  end

      //Delete
      $("#contentContainer").on('click', '.delete', function() {
          if(!confirm('Sure?')) return;
          codeid = $(this).data('id');
          info_url = url + '/'+codeid;
          $.ajax({
              url:info_url,
              method: "GET",
              type: "DELETE",
              data:{
              },
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

      $(document).on('change', '.toggle-status', function() {
          var brand_id = $(this).data('id');
          var status = $(this).prop('checked') ? 1 : 0;

          $.ajax({
              url: '/admin/slider-status',
              method: "POST",
              data: {
                  brand_id: brand_id,
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
      function populateForm(data){
          $("#title").val(data.title);
          $("#sub_title").val(data.sub_title); 
          $("#link").val(data.link);

          $("#codeid").val(data.id);
          $("#addBtn").val('Update');
          $("#addBtn").html('Update');
          $("#addThisFormContainer").show(300);
          $("#newBtn").hide(100);

          var featureImagePreview = document.getElementById('preview-image');
            if (data.image) { 
                featureImagePreview.src = '/images/slider/' + data.image;
            } else {
                featureImagePreview.src = "#";
            }

      }
      function clearform(){
          $('#createThisForm')[0].reset();
          $("#addBtn").val('Create');
          $("#addBtn").html('Create');
          $("#addThisFormContainer").slideUp(200);
          $("#newBtn").slideDown(200);
          $('#preview-image').attr('src', '#');
          $("#cardTitle").text('Add new data');
      }
      previewImage('#image', '#preview-image');
  });
</script>

@endsection