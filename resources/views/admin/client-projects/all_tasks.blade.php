@extends('admin.master')

@section('content')

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Tasks List</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table cell-border table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Date</th>
                                    <th>Project</th>
                                    <th>Task</th>
                                    <th>Assigned To</th>
                                    <th>Priority</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-3">
              @if(request()->status)
                <a href="{{ url()->previous() }}" class="btn btn-secondary my-3">Back</a>
              @endif
            </div>
        </div>
    </div>
</section>

@endsection

@section('script')
<script>
    $(document).ready(function () {
        $(document).on('click', '.status-change', function(e) {
            e.preventDefault();

            var taskId = $(this).data('id');
            var status = $(this).data('status');
            var toggleUrl = "/admin/client-projects-task/" + taskId + "/toggle-status";
            
            $.ajax({
                url: toggleUrl,
                method: "POST",
                data: {
                    task_id: taskId,
                    status: status,
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    success(res.message);
                    reloadTable();
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    error('Failed to update task status.');
                }
            });
        });

        //Delete
        $("#contentContainer").on('click','.delete', function(){
            if(!confirm('Are you sure you want to delete this task?')) return;
            codeid = $(this).data('id');
            var info_url = "/admin/client-projects-task/" + codeid;
            $.ajax({
                url: info_url,
                method: "DELETE",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
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
                url: "{{ route('tasks.all') }}" + window.location.search,
                type: "GET",
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'date', name: 'date'},
                {data: 'project_title', name: 'project_title'},
                {data: 'title', name: 'title'},
                {data: 'employee_name', name: 'employee_name'},
                {data: 'priority', name: 'priority', orderable: false, searchable: false},
                {data: 'due_date', name: 'due_date'},
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