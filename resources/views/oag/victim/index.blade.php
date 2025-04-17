@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb" style="background: #f8f9fa; border-radius: 25px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color: #007bff; text-decoration: none;">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page" style="color: #6c757d;">Victim DataTable</li>
        </ol>
    </nav>

    <!-- Heading -->
    <h2 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #f8f9fa; text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.4); border-bottom: 4px double #ff6f61; padding-bottom: 20px;">
        Victim DataTable
    </h2>
    
    <!-- Create New Victim Button -->
    <div class="text-center mb-3">
        <a href="{{ route('crime.victim.create') }}" class="btn btn-lg px-5" style="background: radial-gradient(circle at top left, #ff6f61, #de1c1c); color: #fff; border-radius: 30px; font-weight: bold; box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3); transition: background 0.4s, transform 0.3s; transform: perspective(1px) translateZ(0);">
            Create New Victim
        </a>
    </div>
    
    <!-- DataTables Table -->
    <div class="table-responsive shadow-lg rounded-3 overflow-hidden" style="background: linear-gradient(145deg, #f1f1f1, #ffffff); border: 2px solid #007bff; border-radius: 20px;">
        <table class="table table-striped table-hover" id="victim-table" style="border-collapse: separate; border-spacing: 0; border-radius: 20px; overflow: hidden;">
            <thead class="thead-dark" style="background: #343a40; color: #f8f9fa; text-transform: uppercase; letter-spacing: 1px;">
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Gender</th>
                    <th>Date of Birth</th>
                    <th>Age</th>
                    <th>Age Group</th>
                    <th>Created By</th>
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
    /* Pagination Buttons */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.6em 1.2em;
        border-radius: 50%;
        border: 1px solid #007bff;
        background: radial-gradient(circle, #007bff, #0056b3);
        color: #fff;
        font-weight: bold;
        margin: 0 4px;
        transition: background 0.3s, transform 0.3s;
        transform: scale(1);
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: radial-gradient(circle, #0056b3, #003d7a);
        color: #fff;
        transform: scale(1.1);
    }

    /* Filter Input */
    .dataTables_wrapper .dataTables_filter input {
        border: 2px solid #007bff;
        border-radius: 25px;
        padding: 0.6em;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        transition: border-color 0.3s, box-shadow 0.3s;
    }

    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #0056b3;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
    }

    /* Length Select */
    .dataTables_wrapper .dataTables_length select {
        border: 2px solid #007bff;
        border-radius: 25px;
        padding: 0.6em;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    }

    /* Action Buttons */
    .action-buttons .action-button {
        display: inline-block;
        padding: 0.6em 1.2em;
        margin: 0 6px;
        border-radius: 8px;
        font-size: 1em;
        text-decoration: none;
        color: #fff;
        background: radial-gradient(circle, #007bff, #0056b3);
        transition: background 0.4s, transform 0.3s;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
    }

    .action-buttons .action-button:hover {
        background: radial-gradient(circle, #0056b3, #003d7a);
        transform: translateY(-2px);
    }

    .action-buttons .action-button.text-danger {
        background: radial-gradient(circle, #dc3545, #c82333);
    }

    .action-buttons .action-button.text-danger:hover {
        background: radial-gradient(circle, #c82333, #b21f2d);
    }
</style>

<script>
$(document).ready(function() {
    if ($.fn.DataTable.isDataTable('#victim-table')) {
        $('#victim-table').DataTable().clear().destroy();
    }

    $('#victim-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('crime.victim.datatables') }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'first_name', name: 'first_name' },
            { data: 'last_name', name: 'last_name' },
            { data: 'gender', name: 'gender' },
            { data: 'date_of_birth', name: 'date_of_birth' },
            { data: 'age', name: 'age' },
            { data: 'age_group', name: 'age_group' },
            { data: 'created_by_name', name: 'created_by_name' },
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
                                    <a class="dropdown-item" href="${"{{ route('crime.victim.edit', ':id') }}".replace(':id', row.id)}">Edit</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="${"{{ route('crime.victim.show', ':id') }}".replace(':id', row.id)}">View</a>
                                </li>
                                <li>
                                    <form action="${"{{ route('crime.victim.destroy', ':id') }}".replace(':id', row.id)}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </li>
                            </ul>
                        </div>`;
                },
                orderable: false,
                searchable: false
            }
        ],
        language: {
            processing: '<i class="fa fa-spinner fa-spin"></i> Processing...',
            lengthMenu: 'Show _MENU_ entries',
            zeroRecords: 'No matching records found',
            info: 'Showing _START_ to _END_ of _TOTAL_ entries',
            infoEmpty: 'Showing 0 to 0 of 0 entries',
            infoFiltered: '(filtered from _MAX_ total entries)',
            search: 'Search:',
            paginate: {
                first: 'First',
                last: 'Last',
                next: 'Next',
                previous: 'Previous'
            }
        }
    });
});
</script>
@endpush
@endsection
