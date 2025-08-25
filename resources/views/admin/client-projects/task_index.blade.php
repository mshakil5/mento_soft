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
                <button type="button" class="btn btn-secondary my-3" data-toggle="modal" 
                          data-target="#tasksModal"
                          onclick="openTaskModal()">
                          Add Task
                </button>
            </div>
            <div class="col-3 my-3 d-flex">
                <select id="projectFilter" class="form-control ml-2 select2">
                    <option value="">All</option>
                    @foreach ($clientProjects as $project)
                      <option value="{{ $project->id }}">{{ $project->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-3 my-3 d-flex">   
                <input type="text" id="searchInput" placeholder="Search all tasks..." class="form-control">
            </div>
        </div>
    </div>
</section>

<section class="content" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-4">
                        <div class="card card-secondary">
                            <div class="card-header">To Do</div>
                            <div class="card-body">
                                <table id="todoTable" class="table table-striped">
                                    <thead><tr><th>Task</th></tr></thead>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card card-secondary">
                            <div class="card-header">In Progress</div>
                            <div class="card-body">
                                <table id="progressTable" class="table table-striped">
                                    <thead><tr><th>Task</th></tr></thead>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card card-secondary">
                            <div class="card-header">Done</div>
                            <div class="card-body">
                                <table id="doneTable" class="table table-striped">
                                    <thead><tr><th>Task</th></tr></thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
table.dataTable {
    table-layout: fixed;
    width: 100% !important;
}

</style>

@endsection

@section('script')
<script>
    $(document).ready(function () {
      
        function loadTable(tableId, status) {
            return $('#' + tableId).DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('tasks.index') }}",
                    data: function (d) {
                        d.status = status;
                        d.search = $('#searchInput').val();
                        d.client_project_id = $('#projectFilter').val();
                    }
                },
                columns: [{ data: 'task', name: 'task' }],
                paging: true,
                pageLength: 10,
                lengthChange: false,
                searching: false,
                info: false,
                ordering: false,
                dom: 'tip'
            });
        }

        let todoTable     = loadTable('todoTable', 1);
        let progressTable = loadTable('progressTable', 2);
        let doneTable     = loadTable('doneTable', 3);

        $('#searchInput, #projectFilter').on('keyup change', function () {
            todoTable.ajax.reload();
            progressTable.ajax.reload();
            doneTable.ajax.reload();
        });
    });
</script>
@endsection