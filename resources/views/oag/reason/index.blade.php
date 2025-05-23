@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        
        {{ Breadcrumbs::render() }}
        
    </nav>

    <!-- Heading -->
    <h2 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #f8f9fa; text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.4); border-bottom: 4px double #ff6f61; padding-bottom: 20px;">
        Reasons For Closure DataTable
    </h2>
    
    <!-- Create New Reason Button -->
    <div class="text-center mb-3">
        <a href="{{ route('crime.reason.create') }}" class="btn btn-lg px-5" style="background: radial-gradient(circle at top left, #ff6f61, #de1c1c); color: #fff; border-radius: 30px; font-weight: bold; box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3); transition: background 0.4s, transform 0.3s; transform: perspective(1px) translateZ(0);">
            Create New Reason for Closure
        </a>
    </div>
    
    <!-- DataTables Table -->
    <div class="table-responsive shadow-lg rounded-3 overflow-hidden" style="background: linear-gradient(145deg, #f1f1f1, #ffffff); border: 2px solid #007bff; border-radius: 20px;">
        <table class="table table-striped table-hover" id="accused-table" style="border-collapse: separate; border-spacing: 0; border-radius: 20px; overflow: hidden;">
            <thead class="thead-dark" style="background: #343a40; color: #f8f9fa; text-transform: uppercase; letter-spacing: 1px;">
                <tr>
                    <th>ID</th>
                    <th>Reason Description</th>
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
    if ($.fn.DataTable.isDataTable('#accused-table')) {
        $('#accused-table').DataTable().clear().destroy();
    }

    $('#accused-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('crime.reason.datatables') }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'reason_description', name: 'reason_description' },
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
                                    <a class="dropdown-item" href="${"{{ route('crime.reason.edit', ':id') }}".replace(':id', row.id)}">Edit</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="${"{{ route('crime.reason.show', ':id') }}".replace(':id', row.id)}">Show</a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this item?')) document.getElementById('delete-form-${row.id}').submit();">Delete</a>
                                    <form id="delete-form-${row.id}" action="${"{{ route('crime.reason.destroy', ':id') }}".replace(':id', row.id)}" method="POST" class="d-none">
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
                    columns: [0, 1]
                }
            },
            {
                extend: 'excel',
                text: '{{ __("Export to Excel") }}',
                exportOptions: {
                    columns: [0, 1]
                }
            },
            {
                extend: 'pdf',
                text: '{{ __("Export to PDF") }}',
                exportOptions: {
                    columns: [0, 1]
                }
            }
        ]
    });
});
</script>
@endpush
@endsection
