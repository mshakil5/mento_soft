@extends('admin.master')

@section('content')
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
            <h3 class="card-title">Add new</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="ermsg"></div>
            <form id="createThisForm">
              @csrf
              <input type="hidden" class="form-control" id="codeid" name="codeid">     

              <div class="row">
                <div class="col-sm-12">
                  <div class="form-group">
                    <label>Question <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="question" name="question" placeholder="Enter question">
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="form-group">
                        <label>Answer <span class="text-danger">*</span></label>
                        <textarea class="form-control summernote" id="answer" name="answer" placeholder="Enter answer"></textarea>
                    </div>
                </div>
              </div>      
            </form>
          </div>
          <div class="card-footer">
            <button type="submit" id="addBtn" class="btn btn-secondary" value="Create">Create</button>
            <button type="submit" id="FormCloseBtn" class="btn btn-default">Cancel</button>

              <div class="loader text-center" style="display: none;">
                  <div class="spinner-border text-primary" role="status">
                      <span class="visually-hidden">Loading...</span>
                  </div>
              </div>
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
                  <th>Question</th>
                  <th>Answer</th>
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
      var url = "{{URL::to('/admin/faq-questions')}}";
      var upurl = "{{URL::to('/admin/faq-questions-update')}}";

      $("#addBtn").click(function(){
          if($(this).val() == 'Create') {
              var form_data = new FormData();
              form_data.append("question", $("#question").val());
              form_data.append("answer", $("#answer").summernote('code'));
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
              form_data.append("question", $("#question").val());
              form_data.append("answer", $("#answer").summernote('code'));
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
          //Update
      });
      //Edit
      $("#contentContainer").on('click','.edit', function(){
          //alert("btn work");
          let codeid = $(this).data('id');
          //console.log($codeid);
          info_url = url + '/'+codeid+'/edit';
          //console.log($info_url);
          $.get(info_url,{},function(d){
            populateForm(d);
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
      //Delete  
      function populateForm(data){
        $("#question").val(data.question);
        $("#answer").summernote('code', data.answer);
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
        $("#answer").summernote('code', '');
        $("#addThisFormContainer").slideUp(200);
        $("#newBtn").slideDown(200);
        $("#cardTitle").text('Add new');
      }

      let table = $('#example1').DataTable({
          processing: true,
          serverSide: true,
            ajax: {
              url: "{{ route('allFaq') }}",
              type: "GET",
              error: function (xhr, status, error) {
                  console.error(xhr.responseText);
              }
          },
          columns: [
              { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false },
              { data: 'question', name: 'question' },
              { data: 'answer', name: 'answer' },
              { data: 'action', name: 'action', orderable: false, searchable: false }
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