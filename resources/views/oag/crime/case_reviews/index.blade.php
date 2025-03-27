@extends('layouts.app')

@section('content')
    @if(session('caseid'))
        @php
            $caseid = session('caseid');
        @endphp
    @endif

    <div class="container mt-5">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('crime.CaseReview.index') }}">Case Reviews</a></li>
                <li class="breadcrumb-item active" aria-current="page">Case Review DataTable</li>
            </ol>
        </nav>

        <!-- Heading -->
        <h2 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #f8f9fa; text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.4); border-bottom: 4px double #ff6f61; padding-bottom: 20px;">
            Case Review DataTable
        </h2>

        <!-- DataTables Table -->
        <div class="table-responsive shadow-lg rounded-3 overflow-hidden" style="background: linear-gradient(145deg, #f1f1f1, #ffffff); border: 2px solid #007bff; border-radius: 20px;">
            <table class="table table-striped table-hover" id="case-review-table" style="border-collapse: separate; border-spacing: 0; border-radius: 20px; overflow: hidden;">
                <thead class="thead-dark" style="background: #343a40; color: #f8f9fa; text-transform: uppercase; letter-spacing: 1px;">
                    <tr>
                        <th>ID</th>
                        <th>Case Name</th>
                        <th>Current Lawyer</th>
                        <th>Action Type</th>
                        <th>Evidence Status</th>
                        <th>Review Notes</th>
                        <th>Review Date</th>
                        <th>Reallocation Details</th>
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
        
        /* Status badges */
        .status-badge {
            padding: 6px 12px;
            border-radius: 30px;
            font-weight: bold;
            display: inline-block;
            text-align: center;
        }
        
        .status-pending {
            background-color: #ffc107;
            color: #212529;
        }
        
        .status-sufficient {
            background-color: #28a745;
            color: white;
        }
        
        .status-insufficient {
            background-color: #dc3545;
            color: white;
        }
        
        .status-returned {
            background-color: #6c757d;
            color: white;
        }
        
        /* Action type badges */
        .action-badge {
            padding: 6px 12px;
            border-radius: 30px;
            font-weight: bold;
            display: inline-block;
        }
        
        .action-review {
            background-color: #17a2b8;
            color: white;
        }
        
        .action-reallocate {
            background-color: #fd7e14;
            color: white;
        }
        
        .action-court {
            background-color: #6610f2;
            color: white;
        }
    </style>

    <script>
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('#case-review-table')) {
            $('#case-review-table').DataTable().clear().destroy();
        }

        $('#case-review-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('crime.CaseReview.datatables') }}',
            columns: [
                { data: 'id', name: 'id' },
                { 
                    data: 'case_name', 
                    name: 'case_name',
                    render: function(data, type, row) {
                        if (row.case && row.case.court_case_number) {
                            return `<strong>${data}</strong><br>
                                    <small class="text-muted">Court Case #: ${row.case.court_case_number}</small>`;
                        }
                        return data;
                    }
                },
                { 
                    data: null, 
                    name: 'current_lawyer',
                    render: function(data, type, row) {
                        if (row.action_type === 'reallocate' && row.new_lawyer_name) {
                            return `<span class="text-success">${row.new_lawyer_name}</span>
                                   <br><small class="text-muted">Reassigned from: ${row.created_by.name || 'N/A'}</small>`;
                        }
                        return row.created_by.name || 'N/A';
                    }
                },
                { 
                    data: 'action_type', 
                    name: 'action_type',
                    render: function(data, type, row) {
                        if (data === 'reallocate') {
                            return '<span class="action-badge action-reallocate">Reallocation</span>';
                        } else if (data === 'review') {
                            return '<span class="action-badge action-review">Review</span>';
                        } else if (data === 'court_update') {
                            return '<span class="action-badge action-court">Court Update</span>';
                        }
                        return data || 'N/A';
                    }
                },
                { 
                    data: 'evidence_status', 
                    name: 'evidence_status',
                    render: function(data, type, row) {
                        let badgeClass = '';
                        let displayText = '';
                        
                        switch(data) {
                            case 'pending_review':
                                badgeClass = 'status-badge status-pending';
                                displayText = 'Pending Review';
                                break;
                            case 'sufficient_evidence':
                                badgeClass = 'status-badge status-sufficient';
                                displayText = 'Sufficient Evidence';
                                break;
                            case 'insufficient_evidence':
                                badgeClass = 'status-badge status-insufficient';
                                displayText = 'Insufficient Evidence';
                                break;
                            case 'returned_to_police':
                                badgeClass = 'status-badge status-returned';
                                displayText = 'Returned to Police';
                                break;
                            default:
                                badgeClass = 'status-badge';
                                displayText = data;
                        }
                        
                        return `<span class="${badgeClass}">${displayText}</span>`;
                    }
                },
                { 
                    data: 'review_notes', 
                    name: 'review_notes',
                    render: function(data, type, row) {
                        if (data && data.length > 50) {
                            return data.substring(0, 50) + '...';
                        }
                        return data || 'No notes';
                    }
                },
                { 
                    data: 'review_date', 
                    name: 'review_date',
                    render: function(data, type, row) {
                        // Format date nicely for display
                        let date = new Date(data);
                        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                    }
                },
                { 
                    data: null, 
                    name: 'reallocation_details',
                    render: function(data, type, row) {
                        if (row.action_type === 'reallocate' && row.reallocation_reason) {
                            return row.reallocation_reason;
                        }
                        return 'N/A';
                    }
                },
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
                                        <a class="dropdown-item" href="${"{{ route('crime.CaseReview.edit', ':id') }}".replace(':id', row.id)}">Edit</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="${"{{ route('crime.CaseReview.show', ':id') }}".replace(':id', row.id)}">Show</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this item?')) document.getElementById('delete-form-${row.id}').submit();">Delete</a>
                                        <form id="delete-form-${row.id}" action="${"{{ route('crime.CaseReview.destroy', ':id') }}".replace(':id', row.id)}" method="POST" class="d-none">
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
            ],
        });
    });
    </script>
    @endpush
@endsection