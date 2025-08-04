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
            <div class="col-md-8">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Add new contact email</h3>
                    </div>
                    <div class="card-body">
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" class="form-control" id="codeid" name="codeid">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" required>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Email Holder <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="email_holder" name="email_holder" placeholder="Enter email holder name" required>
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
                        <h3 class="card-title">All Contact Emails</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table cell-border table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Email</th>
                                    <th>Email Holder</th>
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
      
      var url = "{{URL::to('/admin/contact-email')}}";
      var upurl = "{{URL::to('/admin/contact-email-update')}}";

      let table = $('#example1').DataTable({
          processing: true,
          serverSide: true,
          ajax: {
              url: "{{ route('allcontactemail') }}",
              type: "GET",
              error: function (xhr, status, error) {
                  console.error(xhr.responseText);
              }
          },
          columns: [
              { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
              { data: 'email', name: 'email' },
              { data: 'email_holder', name: 'email_holder' },
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
          var form_data = new FormData();
          form_data.append("email", $("#email").val());
          form_data.append("email_holder", $("#email_holder").val());

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
          $("#cardTitle").text('Update this contact email');
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
          if(!confirm('Are you sure you want to delete this contact email?')) return;
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
      //Delete  
      
      function populateForm(data){
          $("#email").val(data.email);
          $("#email_holder").val(data.email_holder);
          $("#codeid").val(data.id);
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
          $("#cardTitle").text('Add new contact email');
      }
  });
</script>
@endsection