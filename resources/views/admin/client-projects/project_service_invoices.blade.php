@extends('admin.master')

@section('content')
<section class="content pt-3">
  
    <a href="{{ route('project-services.index') }}" class="btn btn-secondary mb-3 ml-2">Back</a>

    @if ($receivedTransactions->count() > 0)
    <button type="button" class="btn btn-success ml-2 mb-3" data-toggle="modal" data-target="#createInvoiceModal">
        Create New Invoice
    </button>
    @endif

    <div class="modal fade" id="createInvoiceModal" tabindex="-1" role="dialog">
      <div class="modal-dialog">
        <form id="createInvoiceForm">
          @csrf
          <input type="hidden" name="service_id" value="{{ $row->id }}">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Select Payment Dates</h5>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              <select name="payment_date" class="form-control select2" required>
                @foreach($receivedTransactions as $t)
                  <option value="{{ $t->date }}">{{ \Carbon\Carbon::parse($t->date)->format('d-m-Y') }}</option>
                @endforeach
              </select>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-success">Create Invoice</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">{{ $info }}</h3>
                    </div>
                    <div class="card-body">
                        <table id="invoiceTable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    {{-- <th>Txn</th> --}}
                                    {{-- <th>Duration</th> --}}
                                    {{-- <th>Status</th> --}}
                                    <th>Invoice</th>
                                    <th>Payment Date</th>
                                    <th>Amount</th>
                                    <th>Method</th>
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
  $(document).ready(function() {
    $('#invoiceTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('service-invoices', $row->id) }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false}, 
            // {data: 'duration', name: 'duration', orderable: false, searchable: false},
            {data: 'invoice', name: 'invoice', orderable: false, searchable: false},
            {data: 'payment_date', name: 'payment_date', orderable: false, searchable: false},
            {data: 'amount', name: 'amount'},
            // {data: 'status', name: 'status', orderable: false, searchable: false},
            {data: 'method', name: 'method', orderable: false, searchable: false},
            // {data: 'txn', name: 'txn', orderable: false, searchable: false},
        ]
    });
  });

  $(document).on('submit', '#createInvoiceForm', function(e) {
      e.preventDefault();
      if (!confirm('Are you sure?')) return;

      let form = $(this);

      $.ajax({
          url: "{{ route('create-service-invoice') }}",
          method: 'POST',
          data: form.serialize(),
          success(res) {
              success(res.message ?? 'Invoice created successfully!');
              form.closest('.modal').modal('hide');
              location.reload();
          },
          error(xhr) {
              error('Something went wrong.');
              console.error(xhr.responseText);
          }
      });
  });

</script>
@endsection