@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('draft.bills.index') }}">Legal Tasks</a></li>
            <li class="breadcrumb-item active" aria-current="page">Legal Tasks DataTable</li>
        </ol>
    </nav>

    <!-- Heading -->
    <h2 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #f8f9fa; text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.4); border-bottom: 4px double #ff6f61; padding-bottom: 20px;">
        Legal Tasks DataTable
    </h2>

    <!-- Create New Task Button -->
    <div class="text-center mb-3">
        <a href="{{ route('draft.bills.create') }}" class="btn btn-lg px-5" style="background: radial-gradient(circle at top left, #ff6f61, #de1c1c); color: #fff; border-radius: 30px; font-weight: bold; box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3); transition: background 0.4s, transform 0.3s; transform: perspective(1px) translateZ(0);">
            Create New Task
        </a>
    </div>

    <!-- DataTables Table -->
    <div class="table-responsive shadow-lg rounded-3 overflow-hidden" style="background: linear-gradient(145deg, #f1f1f1, #ffffff); border: 2px solid #007bff; border-radius: 20px;">
        <table class="table table-striped table-hover" id="legal-tasks-table" style="border-collapse: separate; border-spacing: 0; border-radius: 20px; overflow: hidden;">
            <thead class="thead-dark" style="background: #343a40; color: #f8f9fa; text-transform: uppercase; letter-spacing: 1px;">
                <tr>
                    <th>Date</th>
                    <th>Task</th>
                    <th>Ministry</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Comments</th>
                    <th>Target Completion Date</th>
                    <th>Actual Completion Date</th>
                    <th>Reciept Year</th>
                    <th>Version</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be inserted here by DataTables -->
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<!-- DataTables Buttons JS -->
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<!-- JSZip for CSV export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.2.2/jszip.min.js"></script>
<!-- DataTables Buttons HTML5 JS -->
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<!-- PDFMake for PDF export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/vfs_fonts.js"></script>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">

<script>
$(document).ready(function() {
    // Check if the table is already initialized
    if ($.fn.dataTable.isDataTable('#legal-tasks-table')) {
        // If it is, destroy the old DataTable instance before initializing a new one
        $('#legal-tasks-table').DataTable().clear().destroy();
    }

    // Initialize the DataTable
    $('#legal-tasks-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('draft.bills.datatables') }}', // Replace with your actual route if different
            dataSrc: 'data', // Assuming the 'data' key contains the task array in the response
        },
        columns: [
            { data: 'receipt_date', name: 'receipt_date', render: function(data) { return formatDate(data); } },
            { data: 'name', name: 'name' },
            { data: 'ministry_name', name: 'ministry_name' },
            { data: 'status', name: 'status' },
            { data: 'priority', name: 'priority' },
            { data: 'comments', name: 'comments' },
            { data: 'target_completion_date', name: 'target_completion_date', render: function(data) { return formatDate(data); } },
            { data: 'actual_completion_date', name: 'actual_completion_date', render: function(data) { return formatDate(data); } },
            { data: 'receipt_year', name: 'receipt_year' },
            { data: 'version', name: 'version' },
            {
                data: null,
                title: 'Actions',
                render: function(data, type, row) {
                    return `
                        <div class="action-buttons">
                            <a href="${"{{ route('draft.bills.edit', ':id') }}".replace(':id', row.id)}" class="action-button">Edit</a>
                            <a href="${"{{ route('draft.bills.show', ':id') }}".replace(':id', row.id)}" class="action-button">Show</a>
                            <a href="#" class="action-button text-danger" onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this item?')) document.getElementById('delete-form-${row.id}').submit();">Delete</a>
                            <form id="delete-form-${row.id}" action="${"{{ route('draft.bills.destroy', ':id') }}".replace(':id', row.id)}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    `;
                }
            }
        ],
        dom: 'lBfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf']
    });
});

// Helper function to format date
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', options);
}
</script>
@endpush

@endsection
