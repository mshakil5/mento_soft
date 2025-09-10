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
                 @can('add invoice')
                <button type="button" class="btn btn-secondary my-3" id="newBtn">Add new</button>
                @endcan
            </div>
            @if (!(request()->status))
            <div class="col-4 my-3 d-flex">
                <select id="statusFilter" class="form-control ml-2 select2">
                    <option value="">All</option>
                    <option value="due">Due</option>
                    <option value="received">Received</option>
                </select>
            </div>
            @endif
        </div>
    </div>
</section>

<input type="hidden" id="projectInfo">

<section class="content pt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-10">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Create New Invoice</h3>
                    </div>
                    <div class="card-body">
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" class="form-control" id="codeid" name="codeid">
                            <input type="hidden" name="send_email" id="send_email" value="0">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Invoice Number</label>
                                        <input type="text" class="form-control" id="invoice_number" name="invoice_number" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Due Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="invoice_date" name="invoice_date" required value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>
                                            Invoice To <span class="text-danger">*</span>
                                            <span class="badge bg-success" style="cursor: pointer;" data-toggle="modal" data-target="#newClientModal">
                                                Add new
                                            </span>
                                        </label>
                                        <select class="form-control select2" id="client_id" name="client_id" style="width: 100%;" required>
                                            <option value="">Select Client</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Project</label>
                                        <select class="form-control select2" id="project_id" style="width: 100%;">
                                            <option value="">Select Project</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Client Information</h3>
                                        </div>
                                        <div class="card-body" id="clientInfoContainer">
                                            <p class="text-muted">Select a client to view information</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-primary" id="addProjectBtn"> <i class="fas fa-arrow-up me-1"></i> Add This Project</button>
                                    <button type="button" class="btn btn-success" id="addCustomItemBtn">
                                        <i class="fas fa-plus"></i> Custom Project
                                    </button>
                                </div>
                                   
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Invoice Items</h3>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-bordered" id="invoiceItemsTable">
                                                <thead>
                                                    <tr>
                                                        <th>Project</th>
                                                        <th>Description</th>
                                                        <th width="10%">Qty</th>
                                                        <th width="15%">Price</th>
                                                        <th width="10%">VAT %</th>
                                                        <th width="15%">Total (Excl VAT)</th>
                                                        <th width="5%">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Items will be added here dynamically -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Message on Invoice</label>
                                        <textarea class="form-control" id="description" name="description" rows="5"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="form-group row">
                                                <label class="col-sm-6 col-form-label">Subtotal:</label>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="subtotal" name="subtotal" readonly value="0.00">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-6 col-form-label">Total VAT:</label>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="vat_amount" name="vat_amount" readonly value="0.00">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-6 col-form-label">Discount %:</label>
                                                <div class="col-sm-6">
                                                    <input type="number" class="form-control" id="discount_percent" name="discount_percent" min="0" max="100" value="0">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-6 col-form-label">Discount Amount:</label>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="discount_amount" name="discount_amount" readonly value="0.00">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-6 col-form-label"><strong>Net Total:</strong></label>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="net_amount" name="net_amount" readonly value="0.00">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <button type="submit" id="addBtn" class="btn btn-secondary" value="Create">Save as pdf</button>
                        <button type="button" id="emailBtn" class="btn btn-primary" value="Create">Email as pdf</button>
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
                        <h3 class="card-title">Invoices</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table cell-border table-striped">
                            <thead>
                                <tr>
                                    <th>Invoice No</th>
                                    <th>Client</th>
                                    <th>Project</th>
                                    <th>Amount</th>
                                    <th>Due</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('admin.modals.new_client')

@endsection

@section('script')
<script>
    $(document).ready(function () {

      $("#addThisFormContainer").hide();
          $("#newBtnSection").show();

          function openNewForm() {
              clearform();
              $("#newBtnSection").hide(100);
              $("#addThisFormContainer").show(300);

              $("#invoice_number").val('Loading...');
              return loadClients(); // return the promise so we can wait
          }

          // Bind click
          $("#newBtn").off('click').on('click', function() {
              openNewForm();
          });

          if (localStorage.getItem('autoclickNewBtn') === '1') {
              localStorage.removeItem('autoclickNewBtn');
              $.when(openNewForm()).done(function() {
                  console.log('Form opened and clients loaded.');
              });
          }

          $("#FormCloseBtn").click(function() {
              $("#addThisFormContainer").hide(200);
              $("#newBtnSection").show(100);
              clearform();
          });

          function loadClients() {
              return $.ajax({
                  url: "{{URL::to('/admin/invoices')}}/create",
                  method: "GET",
                  success: function(res) {
                      $('#client_id').empty().append('<option value="">Select Client</option>');
                      $.each(res.clients, function(key, value) {
                          $('#client_id').append('<option value="'+value.id+'">'+value.business_name+'</option>');
                      });

                      $('#client_id').select2({ width: '100%' });

                      $("#invoice_number").val(res.invoice_number);
                  }
              });
          }

        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        
        var url = "{{URL::to('/admin/invoices')}}";
        var upurl = "{{URL::to('/admin/invoices/update')}}";

        $('#newClientForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: '/admin/clients',
                method: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    if (res.status === 200) {
                        $('#newClientModal').modal('hide');
                        success(res.message);
                        loadClients();
                        $('#newClientForm')[0].reset();
                    }
                },
                error: function(err) {
                    if (err.status === 422) {
                        $.each(err.responseJSON.errors, function(key, value) {
                            toastr.error(value[0]);
                        });
                    } else {
                        toastr.error('Something went wrong.');
                    }
                }
            });
        });

        // Client change event
        $('#client_id').change(function() {
            var clientId = $(this).val();
            if (clientId) {
                // Load client info
                $.get(url + '/client-info/' + clientId, function(data) {
                    var html = `
                        <p><strong>Name:</strong> ${data.business_name || ''}</p>
                        <p><strong>Email:</strong> ${data.email || ''}</p>
                        <p><strong>Phone:</strong> ${data.phone1 || ''} ${data.phone2 ? ', ' + data.phone2 : ''}</p>
                        <p><strong>Address:</strong> ${data.address || ''}</p>
                    `;
                    $('#clientInfoContainer').html(html);
                });
                
                // Load client projects
                $.get(url + '/client-projects/' + clientId, function(data) {
                    $('#project_id').empty().append('<option value="">Select Project</option>');
                    $.each(data, function(key, value) {
                        $('#project_id').append('<option value="'+value.id+'">'+value.title+'</option>');
                    });
                });
            } else {
                $('#clientInfoContainer').html('<p class="text-muted">Select a client to view information</p>');
                $('#project_id').empty().append('<option value="">Select Project</option>');
            }
        });
        
        // Project change event
        $('#project_id').change(function() {
            var projectId = $(this).val();
            if (projectId) {
                $.get(url + '/project-info/' + projectId, function(data) {
                    // This data can be used when adding to the table
                    $('#projectInfo').data('info', data);
                    console.log(data);
                });
            }
        });
        
        // Add project to invoice items table
        // $('#addProjectBtn').click(function() {
        //     var projectId = $('#project_id').val();
        //     var projectName = $('#project_id option:selected').text();
            
        //     if (!projectId) {
        //         alert('Please select a project first');
        //         return;
        //     }
            
        //     // Check if project already exists in table
        //     // var exists = false;
        //     // $('#invoiceItemsTable tbody tr').each(function() {
        //     //     if ($(this).data('project-id') == projectId) {
        //     //         exists = true;
        //     //         return false;
        //     //     }
        //     // });
            
        //     // if (exists) {
        //     //     alert('This project is already added to the invoice');
        //     //     return;
        //     // }
            
        //     // Get project info (description from project if available)
        //     var description = '';
        //     if ($('#projectInfo').data('info')) {
        //         description = $('#projectInfo').data('info').description || '';
        //     }
            
        //     // Add row to table
        //     addRowToInvoiceTable({
        //         client_project_id: projectId,
        //         project_name: projectName,
        //         description: description,
        //         qty: 1,
        //         unit_price: 0,
        //         vat_percent: 0
        //     });
            
        //     // Reset project select
        //     $('#project_id').val('').trigger('change');
        // });

        $('#addProjectBtn').click(function() {
            var projectId = $('#project_id').val();
            var projectName = $('#project_id option:selected').text();

            if (!projectId) {
                alert('Please select a project first');
                return;
            }

            // Get project info (already stored in #projectInfo by the change event)
            var projectInfo = $('#projectInfo').data('info');

            if (!projectInfo) {
                alert('Project info not loaded yet. Please try again.');
                return;
            }

            // Loop through all dueServiceDetails and add them as rows
            if (projectInfo.dueServiceDetails && projectInfo.dueServiceDetails.length > 0) {
                projectInfo.dueServiceDetails.forEach(function(service) {
                    addRowToInvoiceTable({
                        id: service.id, // service id
                        client_project_id: projectId,
                        project_name: projectName,
                        description: service.note || projectInfo.description || '',
                        qty: 1,
                        unit_price: parseFloat(service.amount) || 0,
                        vat_percent: 0
                    });
                });
            } else {
                // If no due service details, just add project row with description
                addRowToInvoiceTable({
                    client_project_id: projectId,
                    project_name: projectName,
                    description: projectInfo.description || '',
                    qty: 1,
                    unit_price: 0,
                    vat_percent: 0
                });
            }

            // Reset project select
            $('#project_id').val('').trigger('change');
        });


        // Add custom item to invoice items table
        $('#addCustomItemBtn').click(function() {
            addRowToInvoiceTable({
                client_project_id: null,
                project_name: '',
                description: '',
                qty: 1,
                unit_price: 0,
                vat_percent: 0
            });
        });
        
        // Function to add row to invoice table
        function addRowToInvoiceTable(data) {
            var rowId = 'row_' + Math.floor(Math.random() * 1000000);
            var totalExcVat = data.qty * data.unit_price;
            var vatAmount = totalExcVat * (data.vat_percent / 100);
            
            var html = `
                <tr id="${rowId}" data-project-id="${data.client_project_id || ''}">
                    <input type="hidden" name="projects[${rowId}][client_project_id]" value="${data.client_project_id || ''}">
                    <input type="hidden" name="projects[${rowId}][id]" value="${data.id || ''}">
                    <td>
                        <input type="text" class="form-control" name="projects[${rowId}][project_name]" value="${data.project_name || 'Custom Item'}" required>
                    </td>
                    <td>
                        <textarea class="form-control" name="projects[${rowId}][description]">${data.description || ''}</textarea>
                    </td>
                    <td>
                        <input type="number" class="form-control qty" name="projects[${rowId}][qty]" min="1" value="${data.qty}" required>
                    </td>
                    <td>
                        <input type="number" class="form-control unit_price" name="projects[${rowId}][unit_price]" min="0" step="0.01" value="${data.unit_price}" required>
                    </td>
                    <td>
                        <input type="number" class="form-control vat_percent" name="projects[${rowId}][vat_percent]" min="0" max="100" value="${data.vat_percent}">
                    </td>
                    <td>
                        <input type="text" class="form-control total_exc_vat" value="${totalExcVat.toFixed(2)}" readonly>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger removeRow"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
            
            $('#invoiceItemsTable tbody').append(html);
            pageMiddle();
            calculateTotals();
        }

        $(document).on('keypress', '.qty, .unit_price, .vat_percent, #discount_percent', function(e) {
            if (e.key === '-') {
                e.preventDefault();
            }
        });

        $(document).on('change', '.qty', function () {
            let val = parseInt($(this).val());
            if (isNaN(val) || val < 1) {
                $(this).val(1);
            }
        });

        $(document).on('change', '.unit_price, .vat_percent, #discount_percent', function () {
            let val = parseFloat($(this).val());
            if (isNaN(val) || val < 0) {
                $(this).val(0);
            }
        });

        // Remove row from invoice table
        $(document).on('click', '.removeRow', function() {
            $(this).closest('tr').remove();
            calculateTotals();
        });
        
        // Recalculate totals when qty, price or vat changes
        $(document).on('change', '.qty, .unit_price, .vat_percent', function() {
            var row = $(this).closest('tr');
            var qty = parseFloat(row.find('.qty').val()) || 0;
            var unitPrice = parseFloat(row.find('.unit_price').val()) || 0;
            var vatPercent = parseFloat(row.find('.vat_percent').val()) || 0;
            
            var totalExcVat = qty * unitPrice;
            row.find('.total_exc_vat').val(totalExcVat.toFixed(2));
            
            calculateTotals();
        });
        
        // Discount percent change
        $('#discount_percent').change(function() {
            calculateTotals();
        });
        
        // Calculate all totals
        function calculateTotals() {
            var subtotal = 0;
            var totalVat = 0;
            
            $('#invoiceItemsTable tbody tr').each(function() {
                var qty = parseFloat($(this).find('.qty').val()) || 0;
                var unitPrice = parseFloat($(this).find('.unit_price').val()) || 0;
                var vatPercent = parseFloat($(this).find('.vat_percent').val()) || 0;
                
                var rowTotalExcVat = qty * unitPrice;
                var rowVat = rowTotalExcVat * (vatPercent / 100);
                
                subtotal += rowTotalExcVat;
                totalVat += rowVat;
            });
            
            var discountPercent = parseFloat($('#discount_percent').val()) || 0;
            var discountAmount = subtotal * (discountPercent / 100);
            var netAmount = (subtotal + totalVat) - discountAmount;
            
            $('#subtotal').val(subtotal.toFixed(2));
            $('#vat_amount').val(totalVat.toFixed(2));
            $('#discount_amount').val(discountAmount.toFixed(2));
            $('#net_amount').val(netAmount.toFixed(2));
        }
        
        // Save/Update invoice
        $("#addBtn").click(function(){
            $('#send_email').val(sendEmail ? 1 : 0);
            sendEmail = false;
            if($(this).val() == 'Create') {
                var formData = new FormData();
                var form = $('#createThisForm').serializeArray();
                
                $.each(form, function(key, input) {
                    formData.append(input.name, input.value);
                });
                
                // Add projects data
                var projects = [];
                $('#invoiceItemsTable tbody tr').each(function(index) {
                    var project = {
                        client_project_id: $(this).find('input[name*="[client_project_id]"]').val(),
                        project_name: $(this).find('input[name*="[project_name]"]').val(),
                        description: $(this).find('textarea[name*="[description]"]').val(),
                        qty: $(this).find('input[name*="[qty]"]').val(),
                        unit_price: $(this).find('input[name*="[unit_price]"]').val(),
                        vat_percent: $(this).find('input[name*="[vat_percent]"]').val()
                    };
                    
                    if ($(this).find('input[name*="[id]"]').length) {
                        project.id = $(this).find('input[name*="[id]"]').val();
                    }
                    
                    projects.push(project);
                });
                
                formData.append('projects', JSON.stringify(projects));
                
                $.ajax({
                    url: url,
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                      clearform();
                      success(res.message);
                      if (res.redirect) {
                          window.open(res.redirect, '_blank');
                      }
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
                    },
                    complete: function () {
                        $("#emailBtn").prop('disabled', false);
                        $("#addBtn").prop('disabled', false); 
                    }
                });
            }
            
            if($(this).val() == 'Update') {
                var formData = new FormData();
                var form = $('#createThisForm').serializeArray();
                
                $.each(form, function(key, input) {
                    formData.append(input.name, input.value);
                });
                
                // Add projects data
                var projects = [];
                $('#invoiceItemsTable tbody tr').each(function(index) {
                    var project = {
                        client_project_id: $(this).find('input[name*="[client_project_id]"]').val(),
                        project_name: $(this).find('input[name*="[project_name]"]').val(),
                        description: $(this).find('textarea[name*="[description]"]').val(),
                        qty: $(this).find('input[name*="[qty]"]').val(),
                        unit_price: $(this).find('input[name*="[unit_price]"]').val(),
                        vat_percent: $(this).find('input[name*="[vat_percent]"]').val()
                    };
                    
                    if ($(this).find('input[name*="[id]"]').length) {
                        project.id = $(this).find('input[name*="[id]"]').val();
                    }
                    
                    projects.push(project);
                });
                
                formData.append('projects', JSON.stringify(projects));
                
                $.ajax({
                    url: upurl,
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                      clearform();
                      success(res.message);
                      if (res.redirect) {
                          window.open(res.redirect, '_blank');
                      }
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
                    },
                    complete: function () {
                        $("#emailBtn").prop('disabled', false);
                        $("#addBtn").prop('disabled', false); 
                    }
                });
            }
        });

        let sendEmail = false;
        $("#emailBtn").click(function () {
            sendEmail = true;
            $(this).prop('disabled', true);
            $("#addBtn").prop('disabled', true).click();
        });

        //Edit
        $("#contentContainer").on('click','.edit', function(){
            $("#cardTitle").text('Update Invoice');
            
            codeid = $(this).data('id');
            info_url = url + '/'+codeid+'/edit';
            
            $.get(info_url,{},function(data){
                populateForm(data.invoice);
                loadClients().then(function() {
                    $('#client_id').val(data.invoice.client_id).trigger('change.select2');
                    $("#invoice_number").val(data.invoice.invoice_number);
                });

                $.get(url + '/client-projects/' + data.invoice.client_id, function(projects) {
                    $('#project_id').empty().append('<option value="">Select Project</option>');
                    $.each(projects, function(key, value) {
                        $('#project_id').append('<option value="'+value.id+'">'+value.title+'</option>');
                    });
                    
                    $('#invoiceItemsTable tbody').empty();
                    $.each(data.invoice.details, function(key, detail) {
                        addRowToInvoiceTable(detail);
                    });
                    
                    calculateTotals();
                });
            });
        });

        function populateForm(data){
            pageTop();
            $("#codeid").val(data.id);
            $("#invoice_date").val(data.invoice_date);
            $("#description").val(data.description);
            $("#vat_percent").val(data.vat_percent);
            $("#discount_percent").val(data.discount_percent);
            $("#subtotal").val(data.subtotal);
            $("#vat_amount").val(data.vat_amount);
            $("#discount_amount").val(data.discount_amount);
            $("#net_amount").val(data.net_amount);
            
            $("#addBtn").val('Update');
            $("#addBtn").html('Save as pdf');
            $("#addThisFormContainer").show(300);
            $("#newBtnSection").hide(100);
        }
        
        function clearform(){
            $('#createThisForm')[0].reset();
            $("#codeid").val('');
            $("#invoiceItemsTable tbody").empty();
            $("#clientInfoContainer").html('<p class="text-muted">Select a client to view information</p>');
            $('#client_id').empty().append('<option value="">Select Client</option>');
            $('#project_id').empty().append('<option value="">Select Project</option>');
            
            $("#addBtn").val('Create');
            $("#addBtn").html('Save as pdf');
            $("#addThisFormContainer").slideUp(200);
            $("#newBtnSection").slideDown(200);
            $("#cardTitle").text('Create New Invoice');
            
            // Reset totals
            $("#subtotal").val('0.00');
            $("#vat_amount").val('0.00');
            $("#discount_amount").val('0.00');
            $("#net_amount").val('0.00');
        }

        //Delete
        $("#contentContainer").on('click','.delete', function(){
            if(!confirm('Are you sure you want to delete this invoice?')) return;
            codeid = $(this).data('id');
            info_url = url + '/'+codeid;
            $.ajax({
                url: info_url,
                method: "DELETE",
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

        let ajaxUrl = "{{ route('invoices.index') }}";

        var table = $('#example1').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: ajaxUrl + window.location.search,
                type: "GET",
                data: function (d) {
                    d.status_filter = $('#statusFilter').val();
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                }
            },
            columns: [
                // {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'invoice_number', name: 'invoice_number', orderable: false},
                {data: 'client_name', name: 'client_name'},
                {data: 'project', name: 'project'},
                {data: 'net_amount', name: 'net_amount', render: function(data) {
                    return '£' + parseFloat(data).toFixed(2);
                }},
                {data: 'date', name: 'date'},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            responsive: true,
            lengthChange: false,
            autoWidth: false,
        });

        function reloadTable() {
          table.ajax.reload(null, false);
        }

        $('#statusFilter').on('change', function() {
            table.ajax.reload();
        });

        $(document).on('click', '.send-email', function () {
            let $btn = $(this);
            let id = $btn.data('id');
            let spinner = $btn.find('.spinner-border');

            spinner.removeClass('d-none');
            $btn.prop('disabled', true);

            $.ajax({
                url: '/admin/invoices/send-email/' + id,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    success(res.message ?? 'Email sent successfully!');
                    reloadTable();
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON?.message ?? 'Email sending failed.';
                    error(msg);
                },
                complete: function () {
                    spinner.addClass('d-none');
                    $btn.prop('disabled', false);
                }
            });
        });

        $(document).on('submit', '.receive-form', function(e) {
            e.preventDefault();
            if (!confirm('Mark as received?')) return;

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
                error() {
                    error('Something went wrong.');
                }
            });
        });

    });
    
</script>
@endsection