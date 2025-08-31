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
                        <h3 class="card-title" id="cardTitle">Add new Employee</h3>
                    </div>
                    <div class="card-body">
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" class="form-control" id="codeid" name="codeid">
                            
                            <div class="row">
                                <div class="col-12">
                                    <h5>Basic Information</h5>
                                    <hr>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name" placeholder="" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Contact No. <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="contact_no" name="contact_no" placeholder="" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Joining Date </label>
                                        <input type="date" class="form-control" id="joining_date" name="joining_date" placeholder="">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Em. Contact Person</label>
                                        <input type="text" class="form-control" id="em_contact_person" name="em_contact_person" placeholder="">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Em. Contact No. </label>
                                        <input type="text" class="form-control" id="em_contact_no" name="em_contact_no" placeholder="">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>NID</label>
                                        <input type="file" class="form-control" id="nid" name="nid">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                  <div class="form-group">
                                    <label>Address </label>
                                    <textarea class="form-control" id="address" name="address" placeholder="" rows="3"></textarea>
                                  </div>
                                </div>
                                <div class="col-12">
                                    <h5>Payment Information</h5>
                                    <hr>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Salary </label>
                                        <input type="number" class="form-control" id="salary" name="salary" placeholder="">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                  <div class="form-group">
                                    <label>Bank Details </label>
                                    <textarea class="form-control" id="bank_details" name="bank_details" placeholder="" rows="1"></textarea>
                                  </div>
                                </div>
                                    <div class="col-12">
                                    <h5>Login Information</h5>
                                    <hr>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Password <span class="text-danger" id="passwordRequired">*</span></label>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter password">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Confirm Password <span class="text-danger" id="passwordConfirmationRequired">*</span></label>
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm password">
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
                        <h3 class="card-title">All Employees</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table cell-border table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    {{-- <th>Date</th> --}}
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Contact No.</th>
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
        
        var url = "{{URL::to('/admin/employees')}}";
        var upurl = "{{URL::to('/admin/employees/update')}}";

        $("#addBtn").click(function(){
            if($(this).val() == 'Create') {
                var form_data = new FormData();
                form_data.append('name', $("#name").val());
                form_data.append('email', $("#email").val());
                form_data.append('contact_no', $("#contact_no").val());
                form_data.append('joining_date', $("#joining_date").val());
                form_data.append('em_contact_person', $("#em_contact_person").val());
                form_data.append('em_contact_no', $("#em_contact_no").val());
                form_data.append('address', $("#address").val());
                form_data.append('salary', $("#salary").val());
                form_data.append('bank_details', $("#bank_details").val());
                form_data.append('password', $("#password").val());
                form_data.append('password_confirmation', $("#password_confirmation").val());

                if($("#nid")[0].files.length > 0){
                    form_data.append('nid', $("#nid")[0].files[0]);
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
            }
            
            if($(this).val() == 'Update') {
                var form_data = new FormData();
                form_data.append('name', $("#name").val());
                form_data.append('email', $("#email").val());
                form_data.append('contact_no', $("#contact_no").val());
                form_data.append('joining_date', $("#joining_date").val());
                form_data.append('em_contact_person', $("#em_contact_person").val());
                form_data.append('em_contact_no', $("#em_contact_no").val());
                form_data.append('address', $("#address").val());
                form_data.append('salary', $("#salary").val());
                form_data.append('bank_details', $("#bank_details").val());
                form_data.append('password', $("#password").val());
                form_data.append('password_confirmation', $("#password_confirmation").val());
                form_data.append('codeid', $("#codeid").val());

                if($("#nid")[0].files.length > 0){
                    form_data.append('nid', $("#nid")[0].files[0]);
                }

                $.ajax({
                    url: upurl,
                    method: "POST",
                    data: form_data,
                    processData: false,
                    contentType: false,
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
            $("#cardTitle").text('Update this employee');
            $("#passwordRequired").hide();
            $("#passwordConfirmationRequired").hide();
            
            codeid = $(this).data('id');
            info_url = url + '/'+codeid+'/edit';
            $.get(info_url,{},function(d){
                populateForm(d);
            });
        });

        function populateForm(data){
            pageTop();
            $("#name").val(data.name);
            $("#email").val(data.email);
            $("#contact_no").val(data.contact_no);
            $("#joining_date").val(data.joining_date);
            $("#em_contact_person").val(data.em_contact_person);
            $("#em_contact_no").val(data.em_contact_no);
            if(data.nid) {
                let fileUrl = '/images/employees/' + data.nid;
                $("#nidFileLink").remove();
                $("#nid").after(`
                    <a href="${fileUrl}" target="_blank" id="nidFileLink" class="d-block mt-2 text-decoration-none">
                        <i class="fas fa-file-download me-1"></i> Download current NID
                    </a>
                `);
            } else {
                $("#nidFileLink").remove();
            }
            $("#address").val(data.address);
            $("#salary").val(data.salary);
            $("#bank_details").val(data.bank_details);
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
            $("#passwordRequired").show();
            $("#passwordConfirmationRequired").show();
            $("#cardTitle").text('Add new employee');
            $("#nidFileLink").remove();
        }

        // Status toggle
        $(document).on('change', '.toggle-status', function() {
            var employee_id = $(this).data('id');
            var status = $(this).prop('checked') ? 1 : 0;

            $.ajax({
                url: '/admin/employees/status',
                method: "POST",
                data: {
                    employee_id: employee_id,
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
            if(!confirm('Are you sure you want to delete this employee?')) return;
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
                url: "{{ route('employees.index') }}",
                type: "GET",
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                // {data: 'date', name: 'date'},
                {data: 'name', name: 'name'},
                {data: 'email', name: 'email'},
                {data: 'contact_no', name: 'contact_no'},
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