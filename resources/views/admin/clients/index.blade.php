@extends('admin.master')

@section('content')
<!-- Main content -->
<section class="content" id="newBtnSection">
    <div class="container-fluid">
        <div class="row">
            <div class="col-2">
                  @if(request()->client_type_id)
                    <a href="{{ url()->previous() }}" class="btn btn-secondary my-3">Back</a>
                  @endif
                @can('add client')   
                <button type="button" class="btn btn-secondary my-3" id="newBtn">Add new</button>
                @endcan
            </div>
            <div class="col-4 my-3 d-flex">
                <select id="statusFilter" class="form-control ml-2 select2">
                    <option value="">All</option>
                    <option value="1">Active</option>
                    <option value="2">Pending</option>
                    <option value="3">Paused</option>
                    <option value="4">Prospect</option>
                </select>
            </div>
        </div>
    </div>
</section>

<section class="content pt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-10">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Add new Client</h3>
                    </div>
                    <div class="card-body">
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" class="form-control" id="codeid" name="codeid">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Client Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="business_name" name="business_name" placeholder="">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Primary Contact <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="primary_contact" name="primary_contact" placeholder="">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Phone <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="phone1" name="phone1" placeholder="Enter primary phone">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                              <label>Password <span class="text-danger">*</span></label>
                              <input type="text" class="form-control" id="password" name="password" placeholder="Enter password">
                            </div>
                            
                            <div class="form-group">
                                <label>Address</label>
                                <textarea class="form-control" id="address" name="address" rows="2" placeholder="Enter address"></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label>Additional Fields</label>
                                <textarea class="form-control summernote" id="additional_fields" name="additional_fields" rows="5" placeholder="Enter additional information"></textarea>
                            </div>
                            
                            <div class="row d-none">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Client Image (400x400 recommended)</label>
                                        <input type="file" class="form-control-file" id="image" name="image" accept="image/*">
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
                        <h3 class="card-title">Clients</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table cell-border table-striped">
                            <thead>
                                <tr>
                                    {{-- <th>Sl</th> --}}
                                    {{-- <th>Date</th> --}}
                                    <th>Client</th>
                                    <th>Contact</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Projects</th>
                                    <th>Outstanding</th>
                                    <th>Received</th>
                                    {{-- <th>Name</th> --}}
                                    {{-- <th>Client Type</th> --}}
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
            $("#newBtnSection").hide(100);
            $("#addThisFormContainer").show(300);
        });
        $("#FormCloseBtn").click(function(){
            $("#addThisFormContainer").hide(200);
            $("#newBtnSection").show(100);
            clearform();
        });

        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        
        var url = "{{URL::to('/admin/clients')}}";
        var upurl = "{{URL::to('/admin/clients/update')}}";

        $("#addBtn").click(function(){
            var form_data = new FormData();
            // form_data.append("name", $("#name").val());
            form_data.append("email", $("#email").val());
            form_data.append("phone1", $("#phone1").val());
            // form_data.append("phone2", $("#phone2").val());
            // form_data.append("on_going", $("#on_going").is(":checked") ? 1 : 0);
            // form_data.append("one_of", $("#one_of").is(":checked") ? 1 : 0);
            form_data.append("address", $("#address").val());
            form_data.append("password", $("#password").val());
            form_data.append("business_name", $("#business_name").val());
            form_data.append("primary_contact", $("#primary_contact").val());
            // form_data.append("client_type_id", $("#client_type_id").val());
            form_data.append("additional_fields", $("#additional_fields").val());

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
            $("#cardTitle").text('Update this client');
            codeid = $(this).data('id');
            info_url = url + '/'+codeid+'/edit';
            $.get(info_url,{},function(d){
                populateForm(d);
            });
        });

        function populateForm(data){
            // $("#name").val(data.name);
            $("#email").val(data.email);
            $("#phone1").val(data.phone1);
            $("#phone2").val(data.phone2);
            $("#on_going").prop("checked", data.on_going == 1);
            $("#one_of").prop("checked", data.one_of == 1);
            $("#address").val(data.address);
            $("#business_name").val(data.business_name);
            $("#primary_contact").val(data.primary_contact);
            // $("#client_type_id").val(data.client_type_id);
            $("#additional_fields").summernote('code', data.additional_fields);
            $("#codeid").val(data.id);
            
            // Set preview image
            if (data.image) {
                $("#preview-image").attr("src", '/images/clients/' + data.image).show();
            }

            $("#addBtn").val('Update');
            $("#addBtn").html('Update');
            $("#addThisFormContainer").show(300);
            $("#newBtnSection").hide(100);
        }
        
        function clearform(){
            $('#createThisForm')[0].reset();
            $("#addBtn").val('Create');
            $("#addBtn").html('Create');
            $("#addThisFormContainer").slideUp(200);
            $("#newBtnSection").slideDown(200);
            $('.summernote').summernote('reset');
            $('#preview-image').attr('src', '#');
            $("#cardTitle").text('Add new client');
        }
        
        previewImage('#image', '#preview-image');

        // Status toggle
        $(document).on('click', '.status-change', function(e) {
            e.preventDefault(); 
            var client_id = $(this).data('id');
            var status = $(this).data('status');

            $.ajax({
                url: '/admin/clients/status',
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
                url: "{{ route('clients.index') }}" + window.location.search,
                type: "GET",
                data: function (d) {
                    d.status = $('#statusFilter').val();
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                }
            },
            columns: [
                //{data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                //{data: 'date', name: 'date'},
                {data: 'business_name', name: 'business_name'},
                {data: 'primary_contact', name: 'primary_contact'},
                {data: 'email', name: 'email'},
                {data: 'phone1', name: 'phone1'},
                {data: 'projects_count', name: 'projects_count'},
                {data: 'outstanding_amount', name: 'outstanding_amount'},
                {data: 'received', name: 'received'},
                // {data: 'image', name: 'image', orderable: false, searchable: false},
                // {data: 'name', name: 'name'},
                //{data: 'client_type', name: 'client_type'},
                {data: 'status', name: 'status', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            responsive: true,
            lengthChange: false,
            autoWidth: false,
        });

        $('#statusFilter').on('change', function() {
            table.ajax.reload();
        });

        function reloadTable() {
          table.ajax.reload(null, false);
        }
    });
</script>
@endsection