@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Criminal Cases</li>
        </ol>
    </nav>

    <!-- Heading -->
    <h2 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #f8f9fa; text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.4); border-bottom: 4px double #ff6f61; padding-bottom: 20px;">
        Criminal Cases DataTable
    </h2>
    
    <!-- Create New Criminal Case Button -->
    <div class="text-center mb-3">
        <a href="{{ route('crime.criminalCase.create') }}" class="btn btn-lg px-5" style="background: radial-gradient(circle at top left, #ff6f61, #de1c1c); color: #fff; border-radius: 30px; font-weight: bold; box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3); transition: background 0.4s, transform 0.3s; transform: perspective(1px) translateZ(0);">
            Create New Criminal Case
        </a>
    </div>
    
    <!-- DataTables Table -->
    <div class="table-responsive shadow-lg rounded-3 overflow-hidden" style="background: linear-gradient(145deg, #f1f1f1, #ffffff); border: 2px solid #007bff; border-radius: 20px;">
        <table class="table table-striped table-hover" id="criminal-case-table" style="border-collapse: separate; border-spacing: 0; border-radius: 20px; overflow: hidden;">
            <thead class="thead-dark" style="background: #343a40; color: #f8f9fa; text-transform: uppercase; letter-spacing: 1px;">
                <tr>
                    <th>ID</th>
                    <th>Case File Number</th>
                    <th>Case Name</th>
                    <th>Date File Received</th>
                    <th>Date File Closed</th>
                    <th>Date of Allocation</th>
                    <th>Reason for Closure</th>
                    <th>Island Name</th>
                    <th>Council Name</th>
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
<!-- DataTables Buttons CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">

<style>
    /* Add your custom styles here */
</style>

<script>
$(document).ready(function() {
    if ($.fn.DataTable.isDataTable('#criminal-case-table')) {
        $('#criminal-case-table').DataTable().clear().destroy();
    }

    $('#criminal-case-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('crime.criminalCase.datatables') }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'case_file_number', name: 'case_file_number' },
            { data: 'case_name', name: 'case_name' },
            {
                data: 'date_file_received',
                name: 'date_file_received',
                render: function(data, type, row) {
                    // Convert ISO 8601 date to a more readable format
                    return data ? new Date(data).toLocaleDateString() : '';
                }
            },
            {
    data: 'date_file_closed',
    name: 'date_file_closed',
    defaultContent: 'Not Close', // Set default content for null or undefined values
    render: function(data, type, row) {
        // Convert ISO 8601 date to a more readable format or show 'N/A' if empty
        return data ? new Date(data).toLocaleDateString() : 'N/A';
    }
},

            {
                data: 'date_of_allocation',
                name: 'date_of_allocation',
                render: function(data, type, row) {
                    // Convert ISO 8601 date to a more readable format
                    return data ? new Date(data).toLocaleDateString() : '';
                }
            },
            { data: 'reason_description', name: 'reason_description', defaultContent: 'N/A' },
            { data: 'island_name', name: 'island_name', defaultContent: 'N/A' },
            { data: 'lawyer_name', name: 'lawyer_name', defaultContent: 'N/A' },
            {
    data: null,
    title: 'Actions',
    render: function(data, type, row) {
        return `
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton${row.id}" data-bs-toggle="dropdown" aria-expanded="false">
                    Actions
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton${row.id}">
                    <li>
                        <a class="dropdown-item" href="${@json(route('crime.criminalCase.edit', ':id')).replace(':id', row.id)}">Edit</a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="${@json(route('crime.criminalCase.show', ':id')).replace(':id', row.id)}">Show</a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="${@json(route('crime.CaseReview.create', ':id')).replace(':id', row.id)}">Case Review</a>
                    </li>
                    <li>
                        <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this item?')) document.getElementById('delete-form-${row.id}').submit();">Delete</a>
                        <form id="delete-form-${row.id}" action="${@json(route('crime.criminalCase.destroy', ':id')).replace(':id', row.id)}" method="POST" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
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
                text: '{{ __("Export to CSV") }}',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7]
                }
            },
            {
                extend: 'excel',
                text: '{{ __("Export to Excel") }}',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7]
                }
            },
            {
                extend: 'pdf',
                text: '{{ __("Export to PDF") }}',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7]
                }
            }
        ]
    });
});
</script>
@endpush
@endsection
