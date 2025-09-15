@extends('admin.master')

@section('content')

@if (!(request()->project_id || request()->client_id))
<section class="content">
    <div class="container-fluid">
        <div class="row pt-3">
            <div class="col-4 d-flex">
                <select id="clientFilter" class="form-control ml-2 select2">
                    <option value="">Choose Client</option>
                    @foreach ($clients as $client)
                      <option value="{{ $client->id }}">{{ $client->business_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-4 d-flex">
                <select id="projectFilter" class="form-control ml-2 select2">
                    <option value="">Choose Project</option>
                    @foreach ($projects as $project)
                      <option value="{{ $project->id }}">{{ $project->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-4 d-flex">
                <select id="serviceFilter" class="form-control ml-2 select2">
                    <option value="">Choose Service</option>
                    @foreach ($services as $service)
                      <option value="{{ $service->id }}">{{ $service->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</section>
@endif

<section class="content pt-3">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">All Transactions</h3>
                    </div>
                    <div class="card-body">
                        <table id="transactionsTable" class="table cell-border table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Client</th>
                                    <th>Invoice No.</th>
                                    <th>Project</th>
                                    <th>Service</th>
                                    <th>Duration</th>
                                    <th>Payment Date</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th>Txn</th>
                                    <th>Note</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            @if (request()->project_id || request()->client_id)
            <div class="col-3 mb-3">
                <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection

@section('script')
<script>
  $(document).ready(function () {
      let transactionsTable = $('#transactionsTable').DataTable({
          processing: true,
          serverSide: true,
          ajax: {
              url: "{{ route('transactions.index') }}" + window.location.search,
              type: "GET",
              data: function(d) {
                  d.client_filter_id  = $('#clientFilter').val();
                  d.project_filter_id = $('#projectFilter').val();
                  d.service_filter_id = $('#serviceFilter').val();
              },
              error: function (xhr) {
                  console.error(xhr.responseText);
              }
          },
          columns: [
              {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
              { data: 'client_name', name: 'client_name' },
              { data: 'invoice_no', name: 'invoice_no' },
              { data: 'project', name: 'project' },
              { data: 'service', name: 'service' },
              { data: 'duration', name: 'duration' },
              { data: 'payment_date', name: 'payment_date' },
              { data: 'amount', name: 'amount', orderable: false, searchable: false },
              { data: 'method', name: 'method' },
              { data: 'status', name: 'status', orderable: false, searchable: false },
              { data: 'txn', name: 'txn' },
              { data: 'note', name: 'note', orderable: false, searchable: false },
          ],
          responsive: true,
          lengthChange: true,
          autoWidth: false,
      });

      function reloadTable() {
          transactionsTable.ajax.reload(null, false);
      }

      $('#clientFilter, #projectFilter, #serviceFilter').on('change', function() {
          reloadTable();
      });
  });
</script>
@endsection