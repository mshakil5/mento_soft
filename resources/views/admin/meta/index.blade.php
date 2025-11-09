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
            <div class="col-md-10">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Add New </h3>
                    </div>
                    <div class="card-body">
                        <div class="ermsg"></div>
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" class="form-control" id="codeid" name="codeid">



                            <div class="form-row">


                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label> Title</label>
                                        <input type="text" class="form-control" id="short_title" name="short_title">
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                    <label for="category">Category <span style="color: red;">*</span></label>
                                    <select class="form-control category" id="category" name="category">
                                        <option value="">Select Category</option>
                                        <option value="Home">Home</option>
                                        <option value="Contact Page">Contact</option>
                                        <option value="Portfolio">Portfolio</option>
                                        <option value="Quotation">Quotation</option>
                                        <option value="Faq">Faq</option>
                                        <option value="Terms">Terms</option>
                                        <option value="Privacy">Privacy</option>
                                    </select>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Meta Title</label>
                                        <input type="text" class="form-control" id="meta_title" name="meta_title">
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
                                        <label>Meta Keywords</label>
                                        <textarea class="form-control" id="meta_keywords" name="meta_keywords" rows="2" placeholder="Enter meta keywords"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Image part start -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="feature-img">Meta Image</label>
                                    <input type="file" class="form-control-file" id="feature-img" accept="image/*">
                                    <img id="preview-image" src="#" alt="" style="max-width: 300px; width: 100%; height: auto; margin-top: 20px;">
                                </div>
   
                            </div>
                             <!-- Image part end -->

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
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Category</th>
                                    <th>Meta Title</th>
                                    <th>Image</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $key => $data)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $data->name }}</td>
                                    <td>{{ $data->meta_title }}</td>
                                    @php
                                        $imagePath = public_path('images/meta/' . $data->meta_image);
                                    @endphp
                                    <td>
                                        @if ($data->meta_image)
                                            @if(file_exists($imagePath))
                                                <img src="{{ asset('images/meta/' . $data->meta_image) }}" 
                                                    alt="Meta Image" 
                                                    style="width: 50px; height: 50px; object-fit: cover;">
                                            @endif
                                        @endif
                                        
                                    </td>
                                    <td>
                                       <div id="accordion{{ $data->id }}">
                                            <div class="card">
                                                <div class="card-header p-1" id="heading{{ $data->id }}">
                                                    <h5 class="mb-0">
                                                        <button class="btn btn-link p-1" data-toggle="collapse" data-target="#collapse{{ $data->id }}" aria-expanded="false" aria-controls="collapse{{ $data->id }}" style="font-size: 12px;">
                                                            Show Meta data
                                                        </button>
                                                    </h5>
                                                </div>
                                                <div id="collapse{{ $data->id }}" class="collapse" aria-labelledby="heading{{ $data->id }}" data-parent="#accordion{{ $data->id }}">
                                                    <div class="card-body p-2" style="font-size: 13px;">
                                                        Description: <br> {{$data->meta_description}}
                                                    </div>
                                                    <div class="card-body p-2" style="font-size: 13px;">
                                                        Key words: <br> {{$data->meta_keywords}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    

                                    <td>

                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="actionMenu'.$row->id.'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            </button>
                                            <div class="dropdown-menu p-2" style="min-width: 160px;">
                                                <a href="javascript:void(0);"  class="btn btn-primary btn-sm btn-block mb-1 detailsBtn"
                                                        data-id="{{ $data->id }}"
                                                        data-title="{{ $data->short_title }}"
                                                        data-category="{{ $data->category }}"
                                                        data-image="{{ asset('images/meta/' . $data->meta_image) }}"
                                                        data-meta_title="{{ $data->meta_title }}"
                                                        data-meta_description="{{ $data->meta_description }}"
                                                        data-meta_keywords="{{ $data->meta_keywords }}"
                                                        data-status="{{ $data->status }}"
                                                        style="margin-right:5px;">Details</a>
                                                <hr class="dropdown-divider">
                                                <button class="btn btn-outline-primary btn-sm btn-block mb-1" id="EditBtn" rid="{{ $data->id }}">Edit</button>
                                                <button class="btn btn-outline-danger btn-sm btn-block" id="deleteBtn" rid="{{ $data->id }}">Delete</button>
                                            </div>
                                        </div>

                                        
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>

    .image-input-wrapper {
        flex: 0 0 auto;
        display: inline-block; 
        vertical-align: top;
        text-align: center;
        width: calc(25% - 10px);
        margin-bottom: 10px;
        position: relative;
    }

    .image-input-wrapper img {
        max-width: 100%;
        height: auto;
    }

    .image-input-icon {
        position: absolute;
        top: 5px;
        right: 5px;
        z-index: 10;
        background-color: rgba(255, 255, 255, 0.8);
        border-radius: 50%;
        padding: 5px;
        cursor: pointer;
    }

    .image-input-icon i {
        color: red;
    }

</style>


<!-- Story Details Modal -->
<div class="modal fade" id="storyDetailsModal" tabindex="-1" role="dialog" aria-labelledby="storyDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="storyDetailsModalLabel">Story Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                        <div class="col-md-12 text-center mb-3">
                                <img id="modal-story-image" src="" alt="Story Image" style="max-width: 100%; height: auto; border-radius: 8px;">
                        </div>
                        <div class="col-md-12">
                                <h4 id="modal-story-title"></h4>
                                <p><strong>Category:</strong> <span id="modal-story-category"></span></p>
                                <p><strong>Status:</strong> <span id="modal-story-status"></span></p>
                                <p><strong>Short Story:</strong> <span id="modal-story-short"></span></p>
                                <p><strong>Description:</strong></p>
                                <div id="modal-story-description"></div>
                                <hr>
                                <p><strong>Meta Title:</strong> <span id="modal-story-meta-title"></span></p>
                                <p><strong>Meta Description:</strong> <span id="modal-story-meta-description"></span></p>
                                <p><strong>Meta Keywords:</strong> <span id="modal-story-meta-keywords"></span></p>
                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection
@section('script')

<script>

    $(function () {
      $("#example1").DataTable({
        "responsive": true, "lengthChange": false, "autoWidth": false, "pageLength": 100,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });


</script>
<script>
$(document).ready(function() {
        // Show details modal on detailsBtn click
        $(document).on('click', '.detailsBtn', function() {
                // Get data attributes
                var title = $(this).data('title') || '';
                var image = $(this).data('image') || '';
                var description = $(this).data('description') || '';
                var short_description = $(this).data('short_description') || '';
                var category = $(this).data('category') || '';
                var meta_title = $(this).data('meta_title') || '';
                var meta_description = $(this).data('meta_description') || '';
                var meta_keywords = $(this).data('meta_keywords') || '';
                var status = $(this).data('status') == 1 ? 'Active' : 'Inactive';

                // Set modal fields
                $('#modal-story-title').text(title);
                $('#modal-story-image').attr('src', image);
                $('#modal-story-category').text(category);
                $('#modal-story-status').text(status);
                $('#modal-story-short').text(short_description);
                $('#modal-story-description').text(description);
                $('#modal-story-meta-title').text(meta_title);
                $('#modal-story-meta-description').text(meta_description);
                $('#modal-story-meta-keywords').text(meta_keywords);

                // Show modal
                $('#storyDetailsModal').modal('show');
        });
});
</script>

<script>
  $(document).ready(function () {
      $("#addThisFormContainer").hide();
      $("#newBtn").click(function(){
          clearform();
          $("#newBtn").hide(100);
          $("#addThisFormContainer").show(300);
          $('#warranty-section').show();

      });
      $("#FormCloseBtn").click(function(){
          $("#addThisFormContainer").hide(200);
          $("#newBtn").show(100);
          clearform();
      });

      $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

      var url = "{{URL::to('/admin/meta-data')}}";
      var upurl = "{{ route('admin.meta.update') }}";

      $("#addBtn").click(function(){

          if($(this).val() == 'Create') {
              var form_data = new FormData();
                form_data.append("name", $("#name").val());
                form_data.append("short_title", $("#short_title").val());
                form_data.append("description", $("#description").val());
                form_data.append("short_description", $("#short_description").val());
                form_data.append("category", $("#category").val());
                form_data.append("meta_title", $("#meta_title").val());
                form_data.append("meta_description", $("#meta_description").val());
                form_data.append("meta_keywords", $("#meta_keywords").val());


                var featureImgInput = document.querySelector('#feature-img');
                if(featureImgInput.files && featureImgInput.files[0]) {
                    form_data.append("meta_image", featureImgInput.files[0]);
                }

              $.ajax({
                url: url,
                method: "POST",
                contentType: false,
                processData: false,
                data:form_data,
                success: function (d) {
                    if (d.status == 400) {
                        $(".ermsg").html(d.message);
                        pagetop();
                    }else if(d.status == 300){
                        
                            success(d.message);
                            location.reload();
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                 }
              });
          }
          //create  end

          //Update
          if($(this).val() == 'Update'){
              var form_data = new FormData();
                form_data.append("category", $("#category").val());
                form_data.append("short_title", $("#short_title").val());
                form_data.append("meta_title", $("#meta_title").val());
                form_data.append("meta_description", $("#meta_description").val());
                form_data.append("meta_keywords", $("#meta_keywords").val());

                var featureImgInput = document.querySelector('#feature-img');
                if(featureImgInput.files && featureImgInput.files[0]) {
                    form_data.append("meta_image", featureImgInput.files[0]);
                }

                form_data.append("codeid", $("#codeid").val());
 
              $.ajax({
                  url:upurl,
                  type: "POST",
                  dataType: 'json',
                  contentType: false,
                  processData: false,
                  data:form_data,
                  success: function(d){
                    //   console.log(d);
                      if (d.status == 400) {
                          $(".ermsg").html(d.message);
                          pagetop();
                      }else if(d.status == 300){
                          
                            success(d.message);
                            location.reload();
                      }
                  },
                  error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                  }
              });
          }
        //Update  end
      });
      //Edit
      $("#contentContainer").on('click','#EditBtn', function(){
          $("#cardTitle").text('Update this data');
          codeid = $(this).attr('rid');
          info_url = url + '/'+codeid+'/edit';
          $.get(info_url,{},function(d){
              populateForm(d);
              pagetop();
          });
      });
      //Edit  end

      //Delete
      $("#contentContainer").on('click','#deleteBtn', function(){
            if(!confirm('Sure?')) return;
            codeid = $(this).attr('rid');
            info_url = url + '/'+codeid;
            $.ajax({
                url:info_url,
                method: "GET",
                type: "DELETE",
                data:{
                },
                success: function(d){
                    if(d.success) {
                    
                        success(d.message);
                        location.reload();
                    }
                },
                error:function(d){
                    // console.log(d);
                }
            });
        });
      //Delete  
      function populateForm(data){

          $("#short_title").val(data.short_title);
          $("#category").val(data.name);
          $("#meta_title").val(data.meta_title);
          $("#meta_description").val(data.meta_description);
          $("#meta_keywords").val(data.meta_keywords);

          $("#codeid").val(data.id);
          $("#addBtn").val('Update');
          $("#addBtn").html('Update');
          $("#addThisFormContainer").show(300);
          $("#newBtn").hide(100);

          var featureImagePreview = document.getElementById('preview-image');
            if (data.feature_image) { 
                featureImagePreview.src = '/images/products/' + data.feature_image; 
            } else {
                featureImagePreview.src = "#";
            }

          
      }
      function clearform(){
          $('#createThisForm')[0].reset();
          $("#addBtn").val('Create').text('Create');
          $("#addBtn").val('Create');
          $("#codeid").val('');
          $("#cardTitle").text('Add new data');
          $('#preview-image').attr('src', '#');
          $('#feature-img').val('');
          $('#imageUpload1').val('');
          $("#description").summernote('code', '');
          $("#short_description").summernote('code', '');
          $('#addBtn').attr('disabled', false);
      }
  });
</script>



<script>
    $(document).ready(function(){
        $("#feature-img").change(function(e){
            var reader = new FileReader();
            reader.onload = function(e){
                $("#preview-image").attr("src", e.target.result);
            };
            reader.readAsDataURL(this.files[0]);
        });
    });
</script>



@endsection