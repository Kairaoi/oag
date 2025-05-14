@extends('layouts.app')

@section('content')
<div class="container pt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="breadcrumb bg-transparent p-0 mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-muted">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Civil2 Cases</li>
        </ol>
    </nav>

    <!-- Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-dark fw-bold mb-0">Civil2 Cases</h2>
        <a href="{{ route('civil2.cases.create') }}" class="btn btn-primary neumorphism-btn shadow-lg">
            <i class="bi bi-plus-circle me-2"></i> Create New Case
        </a>
    </div>

    <!-- DataTable -->
    <div class="table-responsive neumorphism-bg p-3">
        <table class="table table-bordered table-hover" id="civil2-case-table">
            <thead class="table-light">
                <tr>
                    <th>Case File No</th>
                    <th>Court Case No</th>
                    <th>Case Name</th>
                    <th>Counsel</th>
                    <th>Court Type</th>
                    <th>Cause of Action</th>
                    <th>Case Status</th>
                    <th>Pending Status</th>
                    <th>Origin Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

@push('scripts')
<!-- jQuery and DataTables + Export -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.2.2/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/vfs_fonts.js"></script>

<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">

<script>
$(document).ready(function () {
    // Destroy existing table if re-initialized
    if ($.fn.DataTable.isDataTable('#civil2-case-table')) {
        $('#civil2-case-table').DataTable().clear().destroy();
    }

    $('#civil2-case-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("civil2.case.datatables") }}',
        columns: [
            { data: 'case_file_no', name: 'case_file_no' },
            { data: 'court_case_no', name: 'court_case_no' },
            { data: 'case_name', name: 'case_name' },
            { data: 'counsel_name', name: 'counsel_name' },
            { data: 'court_type', name: 'court_type' },
            { data: 'cause_of_action', name: 'cause_of_action' },
            { data: 'case_status', name: 'case_status' },
            { data: 'pending_status', name: 'pending_status' },
            { data: 'origin_type', name: 'origin_type' },
            // single render function:
            {
                data: 'id',
                orderable: false,
                searchable: false,
                render: function(id) {
    return `
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdown-${id}" data-bs-toggle="dropdown" aria-expanded="false">
                Actions
            </button>
            <ul class="dropdown-menu p-3" aria-labelledby="dropdown-${id}" style="min-width: 250px;">
                <li>
                    <a class="dropdown-item" href="${'{{ route('civil2.cases.show', '') }}'}/${id}">
                        Show
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="${'{{ route('civil2.cases.edit', '') }}'}/${id}">
                        Edit
                    </a>
                </li>
                <li>
                    <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); if(confirm('Are you sure?')) document.getElementById('delete-form-${id}').submit();">
                        Delete
                    </a>
                    <form id="delete-form-${id}" action="${'{{ route('civil2.cases.destroy', '') }}'}/${id}" method="POST" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li class="mb-2 fw-bold text-muted px-2">Lawyer Tasks</li>
                <li class="form-check px-3">
                    <input class="form-check-input" type="radio" name="task-${id}" id="review-${id}" onclick="location.href='${'{{ route('civil2.cases.review', '') }}'}/${id}'">
                    <label class="form-check-label" for="review-${id}">Review Case</label>
                </li>
                <li class="form-check px-3">
                    <input class="form-check-input" type="radio" name="task-${id}" id="close-${id}" onclick="location.href='${'{{ route('civil2.close.force', '') }}'}/${id}'">
                    <label class="form-check-label" for="close-${id}">Close Case</label>
                </li>
                <li class="form-check px-3">
                    <input class="form-check-input" type="radio" name="task-${id}" id="reopen-${id}" onclick="location.href='${'{{ route('civil2.close.reopen', '') }}'}/${id}'">
                    <label class="form-check-label" for="reopen-${id}">Reopen Case</label>
                </li>
            </ul>
        </div>
    `;
}
            }
        ],
        dom: 'lBfrtip',
        buttons: [
            'copy',
            {
                extend: 'csv',
                text: 'Export CSV',
                exportOptions: { columns: ':not(:last-child)' }
            },
            {
                extend: 'excel',
                text: 'Export Excel',
                exportOptions: { columns: ':not(:last-child)' }
            },
            {
                extend: 'pdf',
                text: 'Export PDF',
                exportOptions: { columns: ':not(:last-child)' }
            }
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search civil cases..."
        },
        responsive: true
    });
});
</script>

@endpush
@push('styles')
<style>
    .breadcrumb {
        font-size: 1rem;
        background-color: #ffffff;
    }

    .breadcrumb a {
        color: #007bff;
    }

    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }

    .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }

    .table th {
        font-size: 1rem;
        background-color: #e9ecef;
        font-weight: 600;
    }

    .table tbody tr:hover {
        background-color: #f1f1f1;
    }

    .table-info {
        background-color: #d9ecf8;
    }
</style>
@endpush
@endsection
