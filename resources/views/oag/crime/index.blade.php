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

    <!-- Create Button -->
    <div class="text-center mb-3">
        <a href="{{ route('crime.criminalCase.create') }}" class="btn btn-lg px-5" style="background: radial-gradient(circle at top left, #ff6f61, #de1c1c); color: #fff; border-radius: 30px; font-weight: bold; box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3); transition: background 0.4s, transform 0.3s;">
            Create New Criminal Case
        </a>
    </div>

    <!-- Table -->
    <div class="table-responsive shadow-lg rounded-3 overflow-hidden" style="background: linear-gradient(145deg, #f1f1f1, #ffffff); border: 2px solid #007bff; border-radius: 20px;">
        <table class="table table-striped table-hover" id="criminal-case-table">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Case File Number</th>
                    <th>Case Name</th>
                    <th>Date File Received</th>
                    
                    <th>Date of Allocation</th>
                    <th>Reason for Closure</th>
                    <th>Island Name</th>
                    <th>Council Name</th>
                    <th>Reviewer Action</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Rejection Reason Modal -->
<div class="modal fade" id="rejectionModal" tabindex="-1" aria-labelledby="rejectionModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" id="rejectionForm">
        @csrf
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Case</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label for="rejection_reason">Reason for Rejection:</label>
                <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="4" required></textarea>
                <input type="hidden" name="case_id" id="case_id">
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger">Submit Rejection</button>
            </div>
        </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.2.2/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/vfs_fonts.js"></script>

<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">

<style>
    .btn-accept {
        background-color: #28a745; color: white; border-radius: 20px; padding: 5px 15px; font-weight: bold;
    }
    .btn-accept:hover {
        background-color: #218838; transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    .btn-reject {
        background-color: #dc3545; color: white; border-radius: 20px; padding: 5px 15px; font-weight: bold;
    }
    .btn-reject:hover {
        background-color: #c82333; transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
</style>

<script>
$(document).ready(function() {
    $('#criminal-case-table').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        ajax: '{{ route('crime.criminalCase.datatables') }}',
        columns: [
            { data: 'id' },
            { data: 'case_file_number' },
            { data: 'case_name' },
            {
                data: 'date_file_received',
                render: data => data ? new Date(data).toLocaleDateString() : ''
            },
           
            {
                data: 'date_of_allocation',
                render: data => data ? new Date(data).toLocaleDateString() : ''
            },
            { data: 'reason_description', defaultContent: 'N/A' },
            { data: 'island_name', defaultContent: 'N/A' },
            { data: 'lawyer_name', defaultContent: 'N/A' },
            {
                data: null,
                render: function(row) {
                    let statusLabel = '';
                    let actionButtons = '';

                    // Status can be undefined if not in the SQL select
                    let status = '';
                    
                    // If status exists in the data, use it
                    if ('status' in row && row.status) {
                        status = row.status;
                    }
                    
                    @if(!auth()->user()->hasRole('cm.user'))
                        // Only show status label for non-cm.user users
                        if (status === 'accepted') {
                            statusLabel = '<span class="badge bg-success">Accepted</span>';
                        } else if (status === 'rejected') {
                            statusLabel = '<span class="badge bg-danger">Rejected</span>';
                        } else {
                            // Default to pending for any other case
                            statusLabel = '<span class="badge bg-warning">Pending</span>';
                        }
                    @else
                        // For cm.user, only show accepted or rejected badges
                        if (status === 'accepted') {
                            statusLabel = '<span class="badge bg-success">Accepted</span>';
                        } else if (status === 'rejected') {
                            statusLabel = '<span class="badge bg-danger">Rejected</span>';
                        }
                        // Don't show a badge for pending status
                    @endif

                    // For cm.user roles - show actions UNLESS status is accepted/rejected
                    @if(auth()->user()->hasRole('cm.user'))
                        if (status !== 'accepted' && status !== 'rejected') {
                            actionButtons = `
                                <div class="d-flex justify-content-around mt-2">
                                    <button class="btn btn-accept me-2" onclick="handleCaseAction(${row.id}, 'accept')">
                                        <i class="fas fa-check"></i> Accept
                                    </button>
                                    <button class="btn btn-reject" data-bs-toggle="modal" data-bs-target="#rejectionModal" data-bs-case-id="${row.id}">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </div>
                            `;
                        }
                    @endif

                    // Return both status label and action buttons
                    return statusLabel + actionButtons;
                }
            },
            {
    data: null,
    title: 'Actions',
    render: function(data, type, row) {
        let actions = `
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
                    </li>`;

        // Show Case Review only if user is cm.user and status is accepted
        @if(auth()->user()->hasRole('cm.user'))
            if (row.status === 'accepted') {
                actions += `
                    <li>
                        <a class="dropdown-item" href="${@json(route('crime.CaseReview.create', ':id')).replace(':id', row.id)}">Case Review</a>
                    </li>`;
            }
        @endif

        actions += `
                    <li>
                        <a class="dropdown-item" href="${@json(route('crime.criminalCase.createAppeal', ':id')).replace(':id', row.id)}">Appeal</a>
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

        return actions;
    }
}



        ],
        dom: 'lBfrtip',
        buttons: [
            'copy',
            {
                extend: 'csv',
                text: '{{ __("Export to CSV") }}',
                exportOptions: { columns: ':not(:last-child)' }
            },
            {
                extend: 'excel',
                text: '{{ __("Export to Excel") }}',
                exportOptions: { columns: ':not(:last-child)' }
            },
            {
                extend: 'pdf',
                text: '{{ __("Export to PDF") }}',
                exportOptions: { columns: ':not(:last-child)' }
            }
        ]
    });

    // Handle the rejection modal
    $('#rejectionModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const caseId = button.data('bs-case-id');
        $('#case_id').val(caseId);
        $('#rejectionForm').attr('action', "{{ route('crime.criminalCase.reject', ':id') }}".replace(':id', caseId));
    });
});

// Handle Accept/Reject Actions
function handleCaseAction(id, action) {
    if (action === 'accept') {
        if (confirm('Are you sure you want to accept this case?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ route('crime.criminalCase.accept', ':id') }}`.replace(':id', id);
            form.style.display = 'none';

            const token = document.createElement('input');
            token.type = 'hidden';
            token.name = '_token';
            token.value = '{{ csrf_token() }}';
            form.appendChild(token);

            document.body.appendChild(form);
            form.submit();
        }
    }
    // For reject, we use the modal instead
}
</script>
@endpush