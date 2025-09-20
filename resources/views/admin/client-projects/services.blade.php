@extends('admin.master')

@section('content')
<section class="content" id="newBtnSection">
    <div class="container-fluid">
        <div class="row py-3">
            <div class="col-3">
              @can('add service')
              <button type="button" class="btn btn-secondary mr-1" id="newBtn">Add Service</button>
              @endcan
              <a href="{{ route('service-type.index') }}" class="btn btn-secondary">Service List</a>
            </div>
            <div class="col-1 d-none">
                <input type="hidden" id="selectedClientId" value="">
                <button id="sendMail" type="button" class="btn btn-success" style="display:none;">
                    Mail
                </button>
            </div>
            @if (!(request()->client_id || request()->project_service_id || request()->status || request()->due))
            <div class="col-3 d-flex">
                <select id="clientFilter" class="form-control ml-2 select2">
                    <option value="">Select Client</option>
                    @foreach ($clients as $client)
                      <option value="{{ $client->id }}">{{ $client->business_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-2 d-flex">
                <select id="projectFilter" class="form-control ml-2 select2">
                    <option value="">Select Project</option>
                    @foreach ($projects as $project)
                      <option value="{{ $project->id }}">{{ $project->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-2 d-flex">
                <select id="serviceFilter" class="form-control ml-2 select2">
                    <option value="">Select Type</option>
                    <option value="1">In House Service</option>
                    <option value="2">Third Party Service</option>
                </select>
            </div>
            <div class="col-2 d-flex">
                <select id="serviceTypeFilter" class="form-control ml-2 select2">
                    <option value="">Select Service</option>
                    @foreach ($serviceTypes as $serviceType)
                      <option value="{{ $serviceType->id }}">{{ $serviceType->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
        </div>
    </div>
</section>

<section class="content pt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-8">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Service Details</h3>
                    </div>
                    <div class="card-body">
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" id="codeid" name="codeid">
                            <div class="row">
                                 <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Service Type <span class="text-danger">*</span></label>
                                        <select class="form-control" name="type" id="type" required>
                                            <option value="1">In House Service</option>
                                            <option value="2">Third Party Service</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Choose Service <span class="text-danger">*</span></label>
                                        <select class="form-control select2" name="service_type_id" id="service_type_id" required>
                                            <option value="">Choose Service</option>
                                            @foreach ($serviceTypes as $serviceType)
                                                <option value="{{ $serviceType->id }}">{{ $serviceType->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Choose Client <span class="text-danger">*</span></label>
                                        <select class="form-control select2" id="client_id" name="client_id" required>
                                            <option value="">Select Client</option>
                                            @foreach($clients as $client)
                                                <option value="{{ $client->id }}">{{ $client->business_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Choose Project <span class="text-danger">*</span></label>
                                        <select class="form-control select2" id="client_project_id" name="client_project_id" required>
                                            <option value="">Select Project</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Start Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Cycle Type <span class="text-danger">*</span></label>
                                        <select class="form-control" name="cycle_type" id="cycle_type" required>
                                            <option value="">Select Cycle</option>
                                            <option value="1">Monthly</option>
                                            <option value="2">Yearly</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6" id="end_date_div">
                                    <div class="form-group">
                                        <label>Deadline <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" readonly required>
                                    </div>
                                </div>
                                <div class="col-md-6 d-none">
                                    <div class="form-group" style="margin-top: 35px; margin-left: 20px;">
                                        <input type="checkbox" class="form-control-input" id="is_auto" name="is_auto" value="1">
                                        <label>Auto Renewal</label>
                                    </div>
                                </div>

                              <div class="col-md-6">
                                  <div class="form-group">
                                      <label>Amount <span class="text-danger">*</span></label>
                                      <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                                  </div>
                              </div>
                                <div class="col-md-6" id="service_renewal_date_div">
                                    <div class="form-group">
                                        <label>Third Party Service Renewal Date</label>
                                        <input type="date" class="form-control" id="service_renewal_date" name="service_renewal_date">
                                    </div>
                                </div>
                           </div>

                            <div class="form-group">
                                <label>Note</label>
                                <textarea class="form-control" id="note" name="note" rows="3" placeholder="Optional note"></textarea>
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
                        <h3 class="card-title">All Services</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table cell-border table-striped">
                            <thead>
                                <tr>
                                    {{-- <th>Sl</th> --}}
                                    <th>Service</th>
                                    <th>Project</th>
                                    <th>Client</th>
                                    {{-- <th>Start</th> --}}
                                    {{-- <th>Deadline</th> --}}
                                    <th>Due Date</th>
                                    {{-- <th>Renewal</th> --}}
                                    <th>Amount</th>
                                    {{-- <th>Note</th> --}}
                                    <th>Status</th>
                                    {{-- <th>Mail</th> --}}
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            @if (request()->client_id || request()->project_service_id || request()->status|| request()->due )
            <div class="col-3 mb-3">
                <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
            </div>
            @endif
        </div>
    </div>
</section>

<style>
  #example1_wrapper .dt-buttons {
      float: right;
  }
  #example1_wrapper .dt-buttons .btn + .btn {
      margin-left: 2px;
  }

  #example1_filter {
      float: left !important;
  }
</style>

@endsection

@section('script')
<script>
  $(document).ready(function () {

      $("#client_id").select2();
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

      var url = "/admin/project-services";
      var upurl = "/admin/project-services/:id";

      function calculateEndDate() {
          var start = $("#start_date").val();
          var cycleType = $("#cycle_type").val();
          if (!start || !cycleType) return;

          var startDate = new Date(start);
          var endDate = new Date(startDate);

          if (cycleType == "1") {
              endDate.setMonth(endDate.getMonth() + 1);

              if (endDate.getDate() !== startDate.getDate()) {
                  endDate.setDate(0);
              }

          } else if (cycleType == "2") {
              endDate.setFullYear(endDate.getFullYear() + 1);

              if (endDate.getMonth() !== startDate.getMonth()) {
                  endDate.setDate(0);
              }
          }

          endDate.setDate(endDate.getDate() - 1);

          var formattedDate = endDate.toISOString().split('T')[0];
          $("#end_date").val(formattedDate);
      }

      $("#start_date, #cycle_type").change(function() {
          calculateEndDate();
      });

      function toggleEndDate() {
          if ($("#type").val() == "1") { 
              $("#end_date_div").hide();
              $("#service_renewal_date_div").show();
          } else {
              $("#end_date_div").show();
              $("#service_renewal_date_div").hide();
          }
      }

      toggleEndDate();

      $("#type").change(function () {
          toggleEndDate();
      });

      $("#addThisFormContainer").on('click','#addBtn', function(){
          var form_data = new FormData();
          form_data.append("service_type_id", $("#service_type_id").val());
          form_data.append("client_id", $("#client_id").val());
          form_data.append("client_project_id", $("#client_project_id").val());
          form_data.append("start_date", $("#start_date").val());
          form_data.append("end_date", $("#end_date").val());
          form_data.append("service_renewal_date", $("#service_renewal_date").val());
          form_data.append("amount", $("#amount").val());
          form_data.append("note", $("#note").val());
          form_data.append("cycle_type", $("#cycle_type").val());
          form_data.append("type", $("#type").val());
          form_data.append("is_auto", $("#is_auto").is(":checked") ? 1 : 0);

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
                      if(xhr.responseJSON && xhr.responseJSON.errors)
                          error(Object.values(xhr.responseJSON.errors)[0][0]);
                      else
                          error();
                  }
              });
          } else {
              // Update
              form_data.append("codeid", $("#codeid").val());
              var updateUrl = upurl.replace(':id', $("#codeid").val());

              $.ajax({
                  url: updateUrl,
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
                      if(xhr.responseJSON && xhr.responseJSON.errors)
                          error(Object.values(xhr.responseJSON.errors)[0][0]);
                      else
                          error();
                  }
              });
          }
      });

      // Edit
      $("#contentContainer").on('click','.edit', function(){
          var codeid = $(this).data('id');
          var info_url = "/admin/client-project-service-detail/" + codeid + "/edit";
          $.get(info_url, {}, function(d){
            console.log(d);
              populateForm(d);
          });
      });

      function populateForm(data){
          $("#start_date").val(data.start_date.split(' ')[0]).prop('readonly', true);
          $("#cycle_type").val(data.cycle_type).prop('disabled', true);

          $("#service_type_id").val(data.project_service_id).trigger('change').prop('disabled', false);
          $("#client_id").val(data.client_id).trigger('change').prop('disabled', false);

          $.ajax({
              url: '/admin/clients/' + data.client_id + '/projects',
              method: 'GET',
              success: function(projects) {
                  let projectSelect = $('#client_project_id');
                  projectSelect.empty().append('<option value="">Select Project</option>');
                  projects.forEach(project => {
                      projectSelect.append('<option value="'+project.id+'">'+project.title+'</option>');
                  });
                  projectSelect.val(data.client_project_id).trigger('change').prop('disabled', false);
                  $('#projectDiv').show();
              }
          });

          $("#service_renewal_date").val(data.service_renewal_date);
          $("#end_date").val(data.end_date);
          $("#amount").val(data.amount);
          $("#note").val(data.note);
          $("#codeid").val(data.id);
          $("#type").val(data.type);
          $("#is_auto").prop('checked', data.is_auto == 1).prop('disabled', false);

          if (data.type == 1) {
            $("#end_date_div").hide();
            $("#service_renewal_date_div").show();
          } else {
            $("#end_date_div").show();
            $("#service_renewal_date_div").hide();
          }

          $("#addBtn").val('Update').html('Update');
          $("#addThisFormContainer").show(300);
          $("#newBtnSection").hide(100);
      }

      function clearform(){
          $('#createThisForm')[0].reset();
          $("#service_type_id").prop('disabled', false).val('').trigger('change');
          $("#client_id").prop('disabled', false).val('').trigger('change');
          $("#client_project_id").prop('disabled', false).val('').trigger('change');
          $("#cycle_type").prop('disabled', false).val('');
          $("#start_date").prop('readonly', false).val('');
          $("#is_auto").prop('checked', false).prop('disabled', false);

          $("#addBtn").val('Create').html('Create');
          $("#addThisFormContainer").slideUp(200);
          $("#newBtnSection").slideDown(200);
          $("#end_date_div").show();
      }

      // Status toggle
      $(document).on('change', '.toggle-status', function() {
          var checkbox = $(this);
          var detail_id = checkbox.data('id');
          var status = checkbox.prop('checked') ? 1 : 0;
          var toggleUrl = "/admin/client-project-service-detail/" + detail_id + "/toggle-status";

          var confirmed = confirm("Are you sure you want to change the status?");
          
          if (!confirmed) {
              checkbox.prop('checked', !checkbox.prop('checked'));
              return;
          }

          $.ajax({
              url: toggleUrl,
              method: "POST",
              data: {
                  status: status,
                  _token: "{{ csrf_token() }}"
              },
              success: function(res) {
                  success(res.message);
                  reloadTable();
              },
              error: function(xhr) {
                  console.error(xhr.responseText);
                  error('Failed to update status');
                  checkbox.prop('checked', !checkbox.prop('checked'));
              }
          });
      });

      // Delete
      $("#contentContainer").on('click','.delete', function(){
          if(!confirm('Are you sure you want to delete this detail?')) return;
          var codeid = $(this).data('id');
          var info_url = "/admin/client-project-service-detail/" + codeid;
          $.ajax({
              url: info_url,
              method: "DELETE",
              data: {
                  _token: "{{ csrf_token() }}"
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
                  if(xhr.responseJSON && xhr.responseJSON.errors)
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
              url: "{{ route('project-services.index') }}" + window.location.search,
              type: "GET",
              data: function (d) {
                  d.client_filter_id = $('#clientFilter').val();
                  d.project_filter_id = $('#projectFilter').val();
                  d.service_filter_type_id = $('#serviceTypeFilter').val();
                  d.service_id = $('#serviceFilter').val();
              },
              error: function (xhr, status, error) {
                  console.error(xhr.responseText);
              }
          },
          columns: [
              // {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
              {data: 'service_type', name: 'service_type'},
              {data: 'project_title', name: 'project_title'},
              {data: 'client_name', name: 'client_name'},
              // {data: 'start_date', name: 'start_date'},
              // {data: 'end_date', name: 'end_date'},
              {data: 'due_date', name: 'due_date'},
              // {data: 'next_renewal', name: 'next_renewal'},
              {data: 'amount', name: 'amount', orderable: false, searchable: false},
              // {data: 'note', name: 'note', orderable: false, searchable: false},
              {data: 'status', name: 'status', orderable: false, searchable: false},
              // { data: 'checkbox', orderable: false, searchable: false }, 
              {data: 'action', name: 'action', orderable: false, searchable: false},
          ],
          responsive: true,
          lengthChange: true,
          pageLength: 10,
          lengthMenu: [[10,25,50,100,-1],[10,25,50,100,"All"]],
          autoWidth: false,
          dom: 'Blfrtip',
          buttons: [
              {
                  extend: 'excel',
                  text: 'Export Excel',
                  className: 'me-2',
                  exportOptions: { columns: [0,1,2,3,4] }
              },
              {
                  extend: 'pdf',
                  text: 'Export PDF',
                  exportOptions: { columns: [0,1,2,3,4] }
              }
          ]
      });

      function reloadTable() {
        table.ajax.reload(null, false);
      }

      $('#clientFilter, #projectFilter, #serviceTypeFilter, #serviceFilter').on('change', function() {
          reloadTable();
      });

      $(document).on('shown.bs.modal', '.modal', function () {
          $(this).find('.bill-select').select2({ dropdownParent: $(this) });
      });

      $(document).on('change', '.bill-select', function () {
          let total = 0;
          $(this).find('option:selected').each(function () {
              total += parseFloat($(this).data('amount'));
          });
          $(this).closest('.modal-body').find('.total-amount').val('£' + total.toFixed(2));
      });

      // Submit form via AJAX
      $(document).on('submit', '.receive-form', function(e) {
          e.preventDefault();
          if (!confirm('Are You sure?')) return;

          let form = $(this);
          $.ajax({
              url: form.attr('action'),
              method: 'POST',
              data: form.serialize(),
              success(res) {
                  success(res.message ?? 'Received successfully!');
                  form.closest('.modal').modal('hide');
                  reloadTable();
              },
              error(xhr) {
                  console.error(xhr.responseText);
              }
          });
      });

      $(document).on('submit', '.renew-form', function(e) {
          e.preventDefault();
          if (!confirm('Are you sure to renew?')) return;

          let form = $(this);
          $.ajax({
              url: form.attr('action'),
              method: 'POST',
              data: form.serialize(),
              success(res) {
                  success(res.message ?? 'Renewed successfully!');
                  form.closest('.modal').modal('hide');
                  reloadTable();
              },
              error(xhr) {
                  console.error(xhr.responseText);
              }
          });
      });

      $(document).on('submit', '.edit-form', function(e) {
          e.preventDefault();
          if (!confirm('Are you sure to update this service?')) return;

          let form = $(this);
          let modal = form.closest('.modal');

          $.ajax({
              url: form.attr('action'),
              method: 'POST',
              data: form.serialize(),
              success(res) {
                  success(res.message ?? 'Service updated successfully!');

                  modal.modal('hide');

                  modal.on('hidden.bs.modal', function () {
                      $('.modal-backdrop').remove();
                      $('body').removeClass('modal-open');
                      $('body').css('padding-right', '');
                  });

                  reloadTable();
              },
              error(xhr) {
                  console.error(xhr.responseText);
                  alert('Something went wrong while updating!');
              }
          });
      });

      $(document).on('submit', '.advance-receive-form', function(e) {
          e.preventDefault();

          if (!confirm('Are you sure to receive advance payment?')) return;

          let form = $(this);
          $.ajax({
              url: form.attr('action'),
              method: 'POST',
              data: form.serialize(),
              success(res) {
                  success(res.message ?? 'Advance payment received successfully!');
                  form.closest('.modal').modal('hide');
                  reloadTable();
              },
              error(xhr) {
                  console.error(xhr.responseText);
              }
          });
      });

      $('#client_id').on('change', function() {
          let clientId = $(this).val();
          let projectSelect = $('#client_project_id');

          projectSelect.empty().append('<option value="">Select Project</option>');

          if(clientId) {
              $.ajax({
                  url: '/admin/clients/' + clientId + '/projects',
                  method: 'GET',
                  success: function(data) {
                      if(data.length > 0) {
                          data.forEach(project => {
                              projectSelect.append('<option value="'+project.id+'">'+project.title+'</option>');
                          });
                          $('#projectDiv').show();
                      } else {
                          $('#projectDiv').hide();
                      }
                      projectSelect.trigger('change.select2');
                  }
              });
          } else {
              $('#projectDiv').hide();
              projectSelect.trigger('change.select2');
          }
      });

      $('#newBtnSection').on('click', '#sendMail', function(e) {
          e.preventDefault();

          let rowIds = [];
          $('.row-checkbox:checked').each(function() {
              rowIds.push($(this).val());
          });

          let clientId = $('#selectedClientId').val();

          if(rowIds.length === 0) {
              error("Please select at least one row!");
              return;
          }

          let query = rowIds.map(id => 'service_ids[]=' + id).join('&');

          window.location.href = `/admin/client-mail/${clientId}?${query}`;
      });

      let selectedClientId = null;

      $(document).on('change', '.row-checkbox', function() {
          let clientId = $(this).data('client_id');

          if(this.checked){
              if(!selectedClientId) {
                  selectedClientId = clientId;
              } else if(selectedClientId !== clientId) {
                  this.checked = false;
                  error("Cannot select rows from different clients!");
                  return;
              }
          } else {
              if($('.row-checkbox:checked').length === 0) {
                  selectedClientId = null;
              }
          }

          // store in hidden input
          $('#selectedClientId').val(selectedClientId);

          if($('.row-checkbox:checked').length > 0){
              $('#sendMail').show();
          } else {
              $('#sendMail').hide();
          }
      });

      $(document).on('shown.bs.modal', '.modal', function () {
          $(this).find('.data-table').DataTable().destroy();
          $(this).find('.data-table').DataTable({
              ordering: false
          });
      });


    $(document).on('click', '.deleteBill', function() {
        var billId = $(this).attr('id');
        if (!confirm('Are you sure you want to delete this bill?')) return;

        $.ajax({
            url: '/admin/client-project-service-detail/' + billId,
            method: 'DELETE',
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(res) {
                success(res.message ?? 'Bill deleted successfully!');
                // reloadTable();
                window.location.reload();
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                error('Failed to delete bill');
            }
        });
    });

  });
</script>
@endsection