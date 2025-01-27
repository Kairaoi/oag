@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('draft.counsels.index') }}">Counsel</a></li>
            <li class="breadcrumb-item active" aria-current="page">Counsel DataTable</li>
        </ol>
    </nav>
    <!-- Heading -->
    <h2 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #f8f9fa; text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.4); border-bottom: 4px double #ff6f61; padding-bottom: 20px;">
        Accused DataTable
    </h2>
    
    <!-- Create New Accused Button -->
    <div class="text-center mb-3">
        <a href="{{ route('draft.counsels.create') }}" class="btn btn-lg px-5" style="background: radial-gradient(circle at top left, #ff6f61, #de1c1c); color: #fff; border-radius: 30px; font-weight: bold; box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3); transition: background 0.4s, transform 0.3s; transform: perspective(1px) translateZ(0);">
            Create New Counsel
        </a>
    </div>
    
    <!-- DataTables Table -->
    <div class="table-responsive shadow-lg rounded-3 overflow-hidden" style="background: linear-gradient(145deg, #f1f1f1, #ffffff); border: 2px solid #007bff; border-radius: 20px;">
        <table class="table table-striped table-hover" id="accused-table" style="border-collapse: separate; border-spacing: 0; border-radius: 20px; overflow: hidden;">
            <thead class="thead-dark" style="background: #343a40; color: #f8f9fa; text-transform: uppercase; letter-spacing: 1px;">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Active</th>
                    <th>Max Assignments</th>
                    
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

<script>
$(document).ready(function() {
    $('#accused-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('draft.counsels.datatables') }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'position', name: 'position' },
            { data: 'is_active', name: 'is_active', render: function(data) {
                return data ? 'Yes' : 'No';
            }},
            { data: 'max_assignments', name: 'max_assignments' },
           
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
                                    <a class="dropdown-item" href="{{ route('draft.counsels.edit', ':id') }}".replace(':id', row.id)">Edit</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('draft.counsels.show', ':id') }}".replace(':id', row.id)">Show</a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this item?')) document.getElementById('delete-form-${row.id}').submit();">Delete</a>
                                    <form id="delete-form-${row.id}" action="{{ route('draft.counsels.destroy', ':id') }}".replace(':id', row.id) method="POST" class="d-none">
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
                text: 'Export to CSV',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6, 7]
                }
            },
            {
                extend: 'excel',
                text: 'Export to Excel',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6, 7]
                }
            },
            {
                extend: 'pdf',
                text: 'Export to PDF',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6, 7]
                }
            }
        ]
    });
});
</script>
@endpush
@endsection
