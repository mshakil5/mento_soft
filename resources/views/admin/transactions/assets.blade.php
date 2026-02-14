@extends('admin.master')

@section('content')

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div id="alert-container"></div>

                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Asset</h3>
                        <div class="card-tools">
                            <button class="btn btn-lg btn-success" data-toggle="modal" data-target="#chartModal" data-purpose="0">+ Add New Asset</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <form class="form-inline" role="form" method="POST" action="{{ route('admin.asset.filter') }}">
                                {{ csrf_field() }}
                                
                                <div class="form-group mx-sm-3">
                                    <label class="sr-only">Start Date</label>
                                    <input type="date" class="form-control" name="start_date" value="{{ request()->input('start_date') }}">
                                </div>
                                
                                <div class="form-group mx-sm-3">
                                    <label class="sr-only">End Date</label>
                                    <input type="date" class="form-control" name="end_date" value="{{ request()->input('end_date') }}">
                                </div>
                                
                                <div class="form-group mx-sm-3">
                                    <label class="sr-only">Account</label>
                                    <select class="form-control select2" name="account_name">
                                        <option value="">Select Account..</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->account_name }}" {{ request()->input('account_name') == $account->account_name ? 'selected' : '' }}>
                                                {{ $account->account_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Search</button>
                            </form>
                        </div>
                        @component('components.table')
                            @slot('tableID')
                                expenseTBL
                            @endslot
                            @slot('head')
                                <th>ID</th>
                                <th>Date</th>
                                <th>Account</th>
                                <th>Ref</th>
                                <th>Description</th>
                                <th>Transaction Type</th>
                                <th>Payment Type</th>
                                <th>Gross Amount</th>
                                {{-- <th>Tax Rate</th> --}}
                                <th>Vat Amount</th>
                                <th>Net Amount</th>
                                <th>Action</th>
                            @endslot
                        @endcomponent
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    #chartModal .modal-content {
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    #chartModal .modal-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #eee;
        border-radius: 12px 12px 0 0;
        padding: 1.5rem;
    }
    #chartModal .modal-title {
        font-weight: 700;
        color: #334155;
        letter-spacing: -0.025em;
    }
    #chartModal .modal-body {
        padding: 2rem;
    }
    #chartModal .form-group label {
        font-weight: 600;
        font-size: 0.875rem;
        color: #64748b;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }
    #chartModal .form-control {
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        padding: 0.6rem 1rem;
        transition: all 0.2s ease;
        font-size: 0.95rem;
    }
    #chartModal .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        outline: none;
    }
    #chartModal .modal-footer {
        background-color: #f8f9fa;
        border-top: 1px solid #eee;
        padding: 1.25rem;
        border-radius: 0 0 12px 12px;
    }
    .save-btn {
        background-color: #2563eb;
        border: none;
        padding: 0.6rem 2rem;
        font-weight: 600;
        border-radius: 8px;
        transition: background 0.2s;
    }
    .save-btn:hover {
        background-color: #1d4ed8;
    }
    /* Sectioning styling */
    .form-section-title {
        font-size: 0.75rem;
        color: #94a3b8;
        border-bottom: 1px solid #f1f5f9;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        font-weight: 700;
    }
</style>

<div class="modal fade" id="chartModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex align-items-center">
                <h4 class="modal-title">
                    <i class="fas fa-file-invoice-dollar mr-2 text-primary"></i> Asset Management
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form id="customer-form">
                <div class="modal-body">
                    {{csrf_field()}}
                    <div id="alert-container1"></div>

                    <div class="form-section-title">PRIMARY DETAILS</div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date">Date</label>
                                <input type="date" name="date" class="form-control" id="date" value="{{date('Y-m-d')}}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="chart_of_account_id">Chart of Account</label>
                                <select class="form-control custom-select" id="chart_of_account_id" name="chart_of_account_id">
                                    <option value="">Select account...</option>
                                    @php
                                        use App\Models\ChartOfAccount;
                                        $accounts = ChartOfAccount::where('sub_account_head', 'Account Payable')->get(['account_name', 'id']);
                                        $recivible = ChartOfAccount::where('sub_account_head', 'Account Receivable')->get(['account_name', 'id']);
                                        $assets = ChartOfAccount::where('account_head', 'Assets')->get();
                                    @endphp
                                    @foreach($assets as $asset)
                                        <option value="{{ $asset->id }}" data-type="{{ $asset->sub_account_head }}" data-accname="{{ $asset->account_name }}">{{ $asset->account_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ref">Reference Number</label>
                                <input type="text" name="ref" class="form-control" id="ref" placeholder="e.g. REF-001">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="transaction_type">Transaction Type</label>
                                <select class="form-control custom-select" id="transaction_type" name="transaction_type">
                                    </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-section-title mt-3">FINANCIAL BREAKDOWN</div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="amount">Base Amount</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="amount" class="form-control" id="amount" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="vat_rate">Tax (%)</label>
                                <input type="number" name="vat_rate" class="form-control" id="vat_rate" placeholder="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="vat_amount">Tax Amount</label>
                                <input type="text" name="vat_amount" class="form-control bg-light" id="vat_amount" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-1">
                            <div class="form-group p-3 bg-light rounded shadow-sm">
                                <label for="at_amount" class="text-primary">Grand Total Amount</label>
                                <input type="text" name="at_amount" class="form-control form-control-lg border-primary text-primary font-weight-bold" id="at_amount" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="form-section-title">PAYMENT DETAILS</div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" id="payment_type_container">
                                <label for="payment_type">Payment Type</label>
                                <select class="form-control custom-select" id="payment_type" name="payment_type">
                                    <option value="">Select type...</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Bank">Bank</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 d-none" id="employeeDiv">
                            <div class="form-group" id="employee_id_container">
                                <label for="employee_id">Employee</label>
                                <select class="form-control custom-select" id="employee_id" name="employee_id">
                                    <option value="">Select Employee</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group d-none animated fadeIn" id="showpayable">
                                <label for="payable_holder_id">Payable Holder Name</label>
                                <select class="form-control" id="payable_holder_id" name="payable_holder_id">
                                    <option value="">Select holder...</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->account_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group d-none animated fadeIn" id="showreceivable">
                                <label for="recivible_holder_id">Receivable Holder Name</label>
                                <select class="form-control" id="recivible_holder_id" name="recivible_holder_id">
                                    <option value="">Select holder...</option>
                                    @foreach($recivible as $rec)
                                        <option value="{{ $rec->id }}">{{ $rec->account_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Memo / Description</label>
                        <textarea class="form-control" id="description" rows="3" placeholder="Additional notes about this asset transaction..." name="description"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light font-weight-bold mr-2" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary submit-btn save-btn shadow-sm">
                        <i class="fas fa-check-circle mr-1"></i> Save Asset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
    
@section('script')

<script>
    $(document).ready(function() {
        $("#transaction_type").change(function () {
            var transaction_type = $(this).val();
            if (transaction_type == "Purchase") {
                $("#payment_type_container").show();
                $("#payment_type").html("<option value=''>Please Select</option><option value='Cash'>Cash</option><option value='Bank'>Bank</option><option value='Account Payable'>Account Payable</option>");
            } else if (transaction_type == "Receipt") {
                $("#showpayable, #showreceivable").hide();
                $("#payment_type_container").show();
                $("#payment_type").html("<option value=''>Please Select</option><option value='Cash'>Cash</option><option value='Bank'>Bank</option>");
                clearPayableHolder();
            } else if (transaction_type == "Payment") {
                $("#showpayable , #showreceivable").hide();
                $("#payment_type_container").show();
                $("#payment_type").html("<option value=''>Please Select</option><option value='Cash'>Cash</option><option value='Bank'>Bank</option>");
                clearPayableHolder();
            } else if (transaction_type == "Depreciation") {
                $('#payment_type').val('');
                $("#payment_type_container").hide();
            } else if (transaction_type == "Sold") {
                $("#showpayable , #showreceivable").hide();
                $("#payment_type_container").show();
                $("#payment_type").html("<option value=''>Please Select</option><option value='Cash'>Cash</option><option value='Bank'>Bank</option> <option value='Account Receivable'>Account Receivable</option>");
                clearPayableHolder();
            }
        });

        $("#payment_type").change(function(){
            $(this).find("option:selected").each(function(){
                var val = $(this).val();
                if( val == "Account Payable" ){
                    $("#showpayable").show();
                } else if( val == "Account Receivable" ){
                    $("#showreceivable").show();
                } else{
                    $("#showpayable, #showreceivable").hide();
                    clearPayableHolder();
                }
            });
        }).change();

        function clearPayableHolder() {
            $("#payable_holder_id, #recivible_holder_id").val('');
        }

        $('#chart_of_account_id').change(function() {
            var accountType = $(this).find(':selected').data('type');
            var transactionTypeDropdown = $('#transaction_type');
            var accountName = $(this).find(':selected').data('accname') || "";

            transactionTypeDropdown.empty();
            var $employeeDiv = $('#employeeDiv');

            if(accountType === 'Fixed Asset') {
                transactionTypeDropdown.append('<option value="">Select transaction type</option>');
                transactionTypeDropdown.append('<option value="Purchase">Purchase</option>');
                transactionTypeDropdown.append('<option value="Sold">Sold</option>');
                transactionTypeDropdown.append('<option value="Depreciation">Depreciation</option>');
            } else {
                transactionTypeDropdown.append('<option value="">Select transaction type</option>');
                transactionTypeDropdown.append('<option value="Received">Received</option>');
                transactionTypeDropdown.append('<option value="Payment">Payment</option>');
            }

            if (accountName.toLowerCase().includes('employee')) {
                // Show the div (remove d-none)
                $employeeDiv.removeClass('d-none').hide().fadeIn();
                // Optionally make the employee field required if shown
                $('#employee_id').attr('required', true);
            } else {
                // Hide the div
                $employeeDiv.fadeOut(function() {
                    $(this).addClass('d-none');
                });
                // Clear selection and remove required attribute
                $('#employee_id').val('').removeAttr('required');
            }
        });
    });
</script>

<!-- Amount and tax rate calculation -->
<script>
    function calculateTotal() {
        var amount = parseFloat(document.getElementById('amount').value) || 0;
        var taxRate = parseFloat(document.getElementById('vat_rate').value) || 0;

        var taxAmount = amount * (taxRate / 100);
        document.getElementById('vat_amount').value = taxAmount.toFixed(2);

        var totalAmount = amount + taxAmount;
        document.getElementById('at_amount').value = totalAmount.toFixed(2);
    }

    document.getElementById('amount').addEventListener('input', calculateTotal);
    document.getElementById('vat_rate').addEventListener('input', calculateTotal);

    calculateTotal();
</script>

<!-- Main script -->
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });

    var charturl = "{{URL::to('/admin/asset')}}";
    var customerTBL = $('#expenseTBL').DataTable({
        order: [],
        processing: true,
        serverSide: true,
        ajax: {
        url: charturl,
        type: 'GET',
        data: function (d) {
            d.start_date = $('input[name="start_date"]').val();
            d.end_date = $('input[name="end_date"]').val();
            d.account_name = $('select[name="account_name"]').val();
        },
        error: function (xhr, error, thrown) {
            console.log(xhr.responseText);
        }
        },
        deferRender: true,
        columns: [
            {data: 'tran_id', name: 'tran_id'},
            {data: 'date', name: 'date'},
            {data: 'chart_of_account', name: 'chart_of_account'},
            {data: 'ref', name: 'ref'},
            {data: 'description', name: 'description'},
            {data: 'transaction_type', name: 'transaction_type'},
            {data: 'payment_type', name: 'payment_type'},
            {data: 'amount', name: 'amount'},
            // {data: 'vat_rate', name: 'vat_rate'},
            {data: 'vat_amount', name: 'vat_amount'},
            {data: 'at_amount', name: 'at_amount'},
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    let button = `<button type="button" class="btn btn-warning btn-xs edit-btn" data-toggle="modal" data-target="#chartModal" value="${row.id}" title="Edit" data-purpose='1'><i class="fa fa-edit" aria-hidden="true"></i> Edit</button>`;
                    if (row.amount < 0) {
                    }
                    return button;
                }
            },
        ]
    });

    $('form').on('submit', function(e) {
        e.preventDefault();
        customerTBL.ajax.reload();
    });

    // modal

    $('#chartModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        let purpose = button.data('purpose');
        var modal = $(this);
        if (purpose) {
            let id = button.val();
            $.ajax({
                url: charturl +'/' + id,
                type: 'GET',
                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                },
                success: function (response) {
                    // console.log(response);
                    $('#date').val(response.date);
                    $('#ref').val(response.ref);
                    $('#transaction_type').val(response.transaction_type);
                    $('#amount').val(response.amount);
                    $('#vat_rate').val(response.vat_rate);
                    $('#vat_amount').val(response.vat_amount);
                    $('#at_amount').val(response.at_amount);
                    $('#payment_type').val(response.payment_type);
                    $('#description').val(response.description);
                    $('#employee_id').val(response.employee_id);

                    $('#chart_of_account_id').val(response.chart_of_account_id);

                    var accountType = response.chart_of_account_type;

                    var transactionTypeDropdown = $('#transaction_type');
                    var $employeeDiv = $('#employeeDiv');
                    $employeeDiv.removeClass('d-none').hide().fadeIn();

                    transactionTypeDropdown.empty();

                    if(accountType === 'Fixed Asset') {
                        transactionTypeDropdown.append('<option value="">Select transaction type</option>');
                        transactionTypeDropdown.append('<option value="Purchase">Purchase</option>');
                        transactionTypeDropdown.append('<option value="Sold">Sold</option>');
                        transactionTypeDropdown.append('<option value="Depreciation">Depreciation</option>');
                        $('#transaction_type').val(response.transaction_type);
                    } else {
                        transactionTypeDropdown.append('<option value="">Select transaction type</option>');
                        transactionTypeDropdown.append('<option value="Received">Received</option>');
                        transactionTypeDropdown.append('<option value="Payment">Payment</option>');
                        $('#transaction_type').val(response.transaction_type);
                    }     

                    if (response.transaction_type == 'Purchase') {

                        if(response.payment_type == 'Account Payable') {
                           $('#showpayable').show();
                        }

                        $('#showpayable').show();
                        $("#payment_type").html("<option value=''>Please Select</option><option selected value='Account Payable'>Account Payable</option><option value='Cash'>Cash</option><option value='Bank'>Bank</option>");
                        $('#payment_type').val(response.payment_type);
                        $('#showreceivable').hide();
                        
                    } else if (response.transaction_type == 'Sold') {
                        if(response.payment_type == 'Account Receivable') {
                            $('#showreceivable').show();
                        }
                        $("#payment_type").html("<option value=''>Please Select</option><option selected value='Account Receivable'>Account Receivable</option><option value='Cash'>Cash</option><option value='Bank'>Bank</option>");
                        $('#payment_type').val(response.payment_type);
                        $('#showpayable').hide();
                    } 
                    else if (response.transaction_type == 'Depreciation') {
                        $('#payment_type_container').hide();
                    }
                    else {
                        $("#payment_type").html("<option value=''>Please Select</option>" + "<option value='Cash'>Cash</option>" + "<option value='Bank'>Bank</option>");
                        $('#payment_type').val(response.payment_type);
                        $('#showpayable, #showreceivable').hide();     
                    }

                    var payableHolderId = response.payable_holder_id;
                    $('#payable_holder_id').val(payableHolderId);

                    var receivableHolderId = response.recivible_holder_id;
                    $('#recivible_holder_id').val(receivableHolderId);

                    $('#chartModal .submit-btn').removeClass('save-btn').addClass('update-btn').text('Update').val(response.id);
                }
            });
        } else {
            $('#customer-form').trigger('reset');
            $('#customer-form textarea').text('');
            $('#chartModal .submit-btn').removeClass('update-btn').addClass('save-btn').text('Save').val("");
        }
    });

    // save button event

    $(document).on('click', '.save-btn', function () {
        let formData = $('#customer-form').serialize();
        let formDataArray = $('#customer-form').serializeArray();

        // formDataArray.forEach(function(item) {
        //     console.log(item.name + ": " + item.value);
        // });


        $.ajax({
            url: charturl,
            type: 'POST',
            data: formData,
            beforeSend: function (request) {
                request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
            },
            success: function (response) {
                // console.log(response);
                if (response.status === 200) {
                    $('#chartModal').modal('toggle');
                    success(response.message);
                    customerTBL.draw();
                } else if (response.status === 303) {
                    let alertMessage = `<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>${response.message}</b></div>`;
                    $('#alert-container1').html(alertMessage);
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });

    // update button event

    $(document).on('click', '.update-btn', function () {
        let formData = $('#customer-form').serialize();
        let id = $(this).val();
        // console.log(id);
        $.ajax({
            url: charturl + '/' + id,
            type: 'PUT',
            data: formData,
            beforeSend: function (request) {
                request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
            },
            success: function (response) {
                if (response.status === 200) {
                    $('#chartModal').modal('toggle');
                    success(response.message);
                    customerTBL.draw();
                } else if (response.status === 303) {
                    let alertMessage = `<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>${response.message}</b></div>`;
                    $('#alert-container1').html(alertMessage);
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });

</script>

<script>
    $('#chartModal').on('hidden.bs.modal', function (e) {
        $('#customer-form')[0].reset(); 
        $('#customer-form textarea').text(''); 
        $('#chartModal .submit-btn').removeClass('update-btn').addClass('save-btn').text('Save').val("");
        $('#payment_type_container').show();
        $('#payment_type').html("<option value=''>Please Select</option>" + 
                                "<option value='Cash'>Cash</option>" + 
                                "<option value='Bank'>Bank</option>");
        $('#showpayable, #showreceivable').hide();
        $('#payable_holder_id').val('');
        $('#recivible_holder_id').val('');
        var transactionTypeDropdown = $('#transaction_type');
        transactionTypeDropdown.empty();
        transactionTypeDropdown.append('<option value="">Select transaction type</option>');
        transactionTypeDropdown.append('<option value="Received">Received</option>');
        transactionTypeDropdown.append('<option value="Payment">Payment</option>');
        transactionTypeDropdown.append('<option value="Purchase">Purchase</option>');
        transactionTypeDropdown.append('<option value="Sold">Sold</option>');
        transactionTypeDropdown.append('<option value="Depreciation">Depreciation</option>');
    });
</script>

@endsection