@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        
        {{ Breadcrumbs::render() }}
        
    </nav>

    <!-- Heading -->
    <h2 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #333;">Criminal Cases DataTable</h2>
    
    <!-- Create New Case Button -->
    <div class="text-center mb-3">
        <a href="{{ route('crime.criminalCase.create') }}" class="btn btn-gradient btn-lg px-5">Create New Case</a>
    </div>
    
    <!-- DataTables Table -->
    <div class="table-responsive shadow-lg rounded">
        <table class="table table-striped table-hover" id="cases-table">
            <thead>
                <tr class="bg-gradient">
                    <th>Case ID</th>
                    <th>Case File Number</th>
                    <th>Case Name</th>
                    <th>Date File Received</th>
                    <th>Date File Closed</th>
                    <th>Reason for Closure</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be inserted here by DataTables -->
            </tbody>
        </table>
    </div>
</div>

@push('styles')
<style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #fafafa; /* Light background for contrast */
    }
    h2 {
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
    }
    .table {
        background-color: #fff;
        border-radius: 20px;
        overflow: hidden;
        border: 2px solid #007bff; /* Border for the table */
    }
    .bg-gradient {
        background: linear-gradient(90deg, #007bff, #00c6ff);
        color: white; /* White text for headers */
        font-weight: bold;
        text-align: center;
    }
    .table td {
        padding: 1rem;
        vertical-align: middle;
        text-align: center;
        border-bottom: 2px solid #e0e0e0; /* Bottom border for rows */
    }
    .table-hover tbody tr:hover {
        background-color: #e0f7fa; /* Light blue on hover */
        transition: background-color 0.3s;
    }
    .btn {
        border-radius: 30px; /* Rounded button corners */
        font-weight: bold;
    }
    .btn-gradient {
        background: linear-gradient(90deg, #ff416c, #ff4b2b); /* Gradient button */
        color: white;
        transition: background 0.3s;
    }
    .btn-gradient:hover {
        background: linear-gradient(90deg, #ff4b2b, #ff416c); /* Reverse gradient on hover */
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.5em 1em;
        margin: 0 0.2em;
        border: none;
        border-radius: 50px; /* Circular pagination buttons */
        background-color: #007bff;
        color: white;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background-color: #0056b3; /* Darker blue on hover */
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background-color: #0056b3;
        color: white;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
        background-color: #f1f1f1;
        color: #ccc;
        cursor: not-allowed;
    }
    .dataTables_wrapper .dataTables_info {
        margin-top: 1em;
        text-align: center; /* Centered info text */
    }
    .dropdown-menu {
        min-width: 150px;
    }
    .action-buttons {
        display: flex;
        justify-content: center;
        gap: 10px; /* Space between action buttons */
    }
    .action-button {
        background-color: #007bff;
        color: white;
        padding: 10px 15px;
        border-radius: 20px; /* Rounded corners for action buttons */
        text-align: center;
        transition: background-color 0.3s;
    }
    .action-button:hover {
        background-color: #0056b3; /* Darker blue on hover */
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.2.2/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/vfs_fonts.js"></script>
<script>
$(document).ready(function() {
    $('#cases-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('crime.criminalCase.datatables') }}',
        columns: [
            { data: 'case_id', name: 'case_id' },
            { data: 'case_file_number', name: 'case_file_number' },
            { data: 'case_name', name: 'case_name' },
            { 
                data: 'date_file_received', 
                name: 'date_file_received',
                render: function(data) {
                    return data ? new Date(data).toLocaleDateString() : 'N/A';
                }
            },
            { 
                data: 'date_file_closed', 
                name: 'date_file_closed',
                render: function(data) {
                    return data ? new Date(data).toLocaleDateString() : 'N/A';
                }
            },
            { data: 'reason_for_closure', name: 'reason_for_closure' },
            { 
                data: 'created_at', 
                name: 'created_at',
                render: function(data) {
                    return data ? new Date(data).toLocaleDateString() : 'N/A';
                }
            },
            { 
                data: 'updated_at', 
                name: 'updated_at',
                render: function(data) {
                    return data ? new Date(data).toLocaleDateString() : 'N/A';
                }
            },
            {
                data: null,
                title: 'Actions',
                render: function(data, type, row) {
                    return `
                        <div class="action-buttons">
                            <a class="action-button" href="${"{{ route('crime.criminalCase.edit', ':id') }}".replace(':id', row.case_id)}">Edit</a>
                            <a class="action-button" href="${"{{ route('crime.criminalCase.show', ':id') }}".replace(':id', row.case_id)}">Show</a>
                            <a class="action-button text-danger" href="#" onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this item?')) document.getElementById('delete-form-${row.case_id}').submit();">Delete</a>
                            <form id="delete-form-${row.case_id}" action="${"{{ route('crime.criminalCase.destroy', ':id') }}".replace(':id', row.case_id)}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    `;
                }
            }
        ],
        dom: '<"top"Bfr>rt<"bottom"lp><"clear">',
        pagingType: 'full_numbers',
        language: {
            paginate: {
                previous: '&laquo;',
                next: '&raquo;',
                first: '&lt;&lt;',
                last: '&gt;&gt;'
            },
            info: 'Showing _START_ to _END_ of _TOTAL_ entries'
        },
        buttons: [
            'copy',
            {
                "extend": 'csv',
                "text": '{{ __("Export to CSV") }}',
                "exportOptions": {
                    "columns": [0, 1, 2, 3, 4, 5, 6, 7]
                }
            },
            {
                extend: 'excel',
                text: '{{ __("Export to Excel") }}',
                exportOptions: {
                    "columns": [0, 1, 2, 3, 4, 5, 6, 7]
                }
            },
            {
                extend: 'pdf',
                text: '{{ __("Export to PDF") }}',
                exportOptions: {
                    "columns": [0, 1, 2, 3, 4, 5, 6, 7]
                }
            }
        ]
    });
});
</script>
@endpush
