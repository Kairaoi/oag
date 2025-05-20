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
    <div class="table-responsive shadow-lg rounded-3" style="background: linear-gradient(145deg, #f1f1f1, #ffffff); border: 2px solid #007bff; border-radius: 20px;">
        <table class="table table-striped table-hover" id="criminal-case-table">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Case File Number</th>
                    <th>Case Name</th>
                    <th>Date File Received</th>
                    <th>Date of Incident</th>
                    <th>Place of Incident</th>
                    <th>Council Name</th>
                    <th>Reviewer Action</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Rejection Modal -->
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
<!-- DataTables Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.2.2/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/vfs_fonts.js"></script>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- Custom Styles -->
<style>
    /* General table styling */
    #criminal-case-table {
        font-size: 0.9rem;
    }
    
    /* Button Styling */
    .btn-action {
        transition: all 0.3s ease;
        border-radius: 20px;
        font-weight: 500;
        box-shadow: 0 2px 5px rgba(0,0,0,0.15);
        padding: 0.375rem 0.75rem;
    }
    
    .btn-accept {
        background-color: #28a745; 
        color: white; 
    }
    
    .btn-accept:hover {
        background-color: #218838; 
        transform: translateY(-2px); 
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    
    .btn-reject {
        background-color: #dc3545; 
        color: white; 
    }
    
    .btn-reject:hover {
        background-color: #c82333; 
        transform: translateY(-2px); 
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    
    /* Dropdown styling */
    .dropdown-menu {
        z-index: 9999 !important;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        border: none;
        border-radius: 0.5rem;
    }
    
    .dropdown-item {
        padding: 0.65rem 1.5rem;
        transition: all 0.2s ease;
    }
    
    .dropdown-item:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
    }
    
    .dropdown-item i {
        margin-right: 0.5rem;
        width: 1rem;
        text-align: center;
    }
    
    .dropdown-divider {
        margin: 0.25rem 0;
    }
    
    .dropdown-header {
        background-color: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 0.75rem 1rem;
        font-weight: 600;
    }
    
    /* Related Records Button & Panel */
    .btn-related {
        background: linear-gradient(135deg, #4299e1, #3182ce);
        border: none;
        color: white;
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(50, 50, 93, 0.11);
    }
    
    .btn-related:hover {
        box-shadow: 0 7px 14px rgba(50, 50, 93, 0.15);
        transform: translateY(-1px);
    }
    
    .record-item {
        transition: all 0.2s ease;
        border-radius: 0;
        border-left: 3px solid transparent;
    }
    
    .record-item:hover {
        background-color: #f7fafc;
        border-left-color: #4299e1;
    }
    
    .icon-wrapper {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        flex-shrink: 0;
    }
    
    /* Badge styling */
    .badge {
        font-weight: normal;
        padding: 0.35em 0.65em;
        border-radius: 10px;
    }
    
    /* Responsive handling */
    @media (max-width: 992px) {
        .dropdown-menu {
            max-width: 100vw;
            min-width: 280px !important;
        }
        
        .table-responsive {
            overflow-x: auto !important;
        }
        
        .action-btn-container {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .action-btn-container .btn {
            width: 100%;
        }
    }
    
    /* Fix for dropdown positioning */
    .dropdown-menu-end {
        right: 0;
        left: auto !important;
    }
    
    /* Make table scrollable on small screens */
    @media (max-width: 768px) {
        .table-responsive {
            max-width: 100%;
            overflow-x: auto;
        }
        
        #criminal-case-table {
            min-width: 800px;
        }
    }
    
    /* Fix for dropdown positioning on bottom of table */
    .dropup .dropdown-menu {
        bottom: 100%;
        top: auto !important;
        margin-bottom: 0.125rem;
    }
</style>

<script>
    const userRoles = {
        canCaseReview: {{ auth()->user()->hasRole('cm.user') ? 'true' : 'false' }},
        canCourtCase: {{ auth()->user()->hasRole('cm.user') ? 'true' : 'false' }},
        canReallocate: {{ auth()->user()->hasRole('cm.admin') ? 'true' : 'false' }},
        canAppeal: {{ auth()->user()->hasRole('cm.user') ? 'true' : 'false' }},
        canallocate: {{ auth()->user()->hasRole('cm.admin') ? 'true' : 'false' }},
    };
</script>

<!-- Bootstrap 5 JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function () {
    // Check screen size and adjust dropdown positions
    function positionDropdowns() {
        const screenHeight = $(window).height();
        
        $('#criminal-case-table tbody tr').each(function() {
            const $tr = $(this);
            const position = $tr.position();
            const dropdowns = $tr.find('.dropdown');
            
            dropdowns.each(function() {
                const $dropdown = $(this);
                // Reset classes
                $dropdown.removeClass('dropup');
                
                // Check if dropdown is near bottom of screen
                if ((screenHeight - position.top) < 300) {
                    $dropdown.addClass('dropup');
                }
            });
        });
    }
    
    // DataTable initialization
    const table = $('#criminal-case-table').DataTable({
        processing: true,
        serverSide: true,
        destroy: true, 
        ajax: '{{ route('crime.criminalCase.datatables') }}',
        responsive: true,
        columns: [
            { data: 'id' },
            { data: 'case_file_number' },
            { data: 'case_name' },
            { data: 'date_file_received', render: data => data ? new Date(data).toLocaleDateString() : '' },
            { data: 'date_of_incident', render: data => data ? new Date(data).toLocaleDateString() : '' },
            { data: 'island_name', defaultContent: '' },
            { data: 'lawyer_name', defaultContent: '' },
            {
                data: null,
                render: function(row) {
                    let statusLabel = '';
                    let actionButtons = '';
                    let status = row.status || '';

                    @if (!auth()->user()->hasRole('cm.user'))
                        if (status === 'accepted') {
                            statusLabel = '<span class="badge bg-success">Accepted</span>';
                        } else if (status === 'rejected') {
                            statusLabel = '<span class="badge bg-danger">Rejected</span>';
                        } else if (status === 'allocated') {
                            statusLabel = '<span class="badge bg-primary">Allocated</span>';
                        } else if (status === 'reallocated') {
                            statusLabel = '<span class="badge bg-info">Reallocated</span>';
                        } else if (status === 'closed') {
                            statusLabel = '<span class="badge bg-secondary">Closed</span>';
                        } else {
                            statusLabel = '<span class="badge bg-warning">Pending</span>';
                        }
                    @else
                        if (status === 'accepted') {
                            statusLabel = '<span class="badge bg-success">Accepted</span>';
                        } else if (status === 'rejected') {
                            statusLabel = '<span class="badge bg-danger">Rejected</span>';
                        } else if (status === 'allocated') {
                            statusLabel = '<span class="badge bg-primary">Allocated</span>';
                        } else if (status === 'reallocated') {
                            statusLabel = '<span class="badge bg-primary">Reallocated</span>';
                        } else if (status === 'closed') {
                            statusLabel = '<span class="badge bg-secondary">Closed</span>';
                        } else {
                            statusLabel = '<span class="badge bg-secondary">N/A</span>';
                        }
                    @endif

                    @if(auth()->user()->hasRole('cm.user'))
                        if (status !== 'accepted' && status !== 'rejected' && status !== 'closed') {
                            actionButtons = `
                                <div class="d-flex gap-2 mt-2">
                                    <button class="btn btn-action btn-accept" onclick="handleCaseAction(${row.id}, 'accept')">
                                        <i class="fas fa-check"></i> Accept
                                    </button>
                                    <button class="btn btn-action btn-reject" data-bs-toggle="modal" data-bs-target="#rejectionModal" data-bs-case-id="${row.id}">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </div>`;
                        }
                    @endif

                    return statusLabel + actionButtons;
                }
            },
            {
                data: null,
                className: 'position-relative',
                render: function(row) {
                    // Actions dropdown
                    let actions = `
                    <div class="d-md-flex gap-2">
                        <!-- Main Actions Dropdown -->
                        <div class="dropdown mb-2 mb-md-0">
                            <button class="btn btn-primary dropdown-toggle" type="button" id="actionsDropdown${row.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-cog me-1"></i> Actions
                            </button>
                            <ul class="dropdown-menu shadow" aria-labelledby="actionsDropdown${row.id}">
                                <li><a class="dropdown-item" href="${@json(route('crime.criminalCase.edit', ':id')).replace(':id', row.id)}">
                                    <i class="fas fa-edit text-primary"></i> Edit
                                </a></li>
                                <li><a class="dropdown-item" href="${@json(route('crime.criminalCase.show', ':id')).replace(':id', row.id)}">
                                    <i class="fas fa-eye text-info"></i> Show
                                </a></li>`;

                    if (userRoles.canCaseReview && row.status === 'accepted') {
                        actions += `<li>
                            <a class="dropdown-item d-flex justify-content-between align-items-center" href="${@json(route('crime.CaseReview.create', ':id')).replace(':id', row.id)}">
                                <span><i class="fas fa-clipboard-check text-success"></i> Case Review</span>
                                ${row.reviewed_count > 0 ? '<span class="badge bg-success">✓</span>' : ''}
                            </a>
                        </li>`;
                    }

                    if (userRoles.canCourtCase && row.status === 'accepted') {
                        actions += `<li>
                            <a class="dropdown-item d-flex justify-content-between align-items-center" href="${@json(route('crime.CourtCase.create', ':id')).replace(':id', row.id)}">
                                <span><i class="fas fa-gavel text-warning"></i> Court Case</span>
                                ${row.court_case_count > 0 ? '<span class="badge bg-warning">✓</span>' : ''}
                            </a>
                        </li>`;
                    }

                    if (userRoles.canallocate && row.status === 'pending') {
                        actions += `<li><a class="dropdown-item" href="${@json(route('crime.criminalCase.allocateForm', ':id')).replace(':id', row.id)}">
                            <i class="fas fa-user-check text-primary"></i> Case Allocation
                        </a></li>`;
                    }

                    if (userRoles.canReallocate && row.status === 'rejected') {
                        actions += `<li><a class="dropdown-item" href="${@json(route('crime.criminalCase.showReallocationForm', ':id')).replace(':id', row.id)}">
                            <i class="fas fa-exchange-alt text-info"></i> Case Reallocate
                        </a></li>`;
                    }

                    if (userRoles.canAppeal) {
                        actions += `<li>
                            <a class="dropdown-item d-flex justify-content-between align-items-center" href="${@json(route('crime.appeal.create', ':id')).replace(':id', row.id)}">
                                <span><i class="fas fa-balance-scale text-danger"></i> Appeal</span>
                                ${row.appeal_count > 0 ? '<span class="badge bg-danger">✓</span>' : ''}
                            </a>
                        </li>`;
                    }

                    actions += `
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); if(confirm('Are you sure?')) document.getElementById('delete-form-${row.id}').submit();">
                                        <i class="fas fa-trash-alt text-danger"></i> Delete
                                    </a>
                                    <form id="delete-form-${row.id}" action="${@json(route('crime.criminalCase.destroy', ':id')).replace(':id', row.id)}" method="POST" class="d-none">
                                        @csrf @method('DELETE')
                                    </form>
                                </li>
                            </ul>
                        </div>

                        <!-- Related Records Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-related dropdown-toggle" type="button" id="relatedRecordsDropdown${row.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-folder-open me-1"></i> Related Records
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="relatedRecordsDropdown${row.id}" style="min-width: 280px;">
                                <li class="dropdown-header">
                                    <span class="fw-bold">Case ID: ${row.id}</span>
                                </li>
                                
                                <li>
                                    <a href="${@json(route('crime.casereview.reviewed', ':id')).replace(':id', row.id)}" 
                                    class="dropdown-item record-item py-2 d-flex align-items-center">
                                        <div class="icon-wrapper me-2" style="background-color: rgba(66, 153, 225, 0.15);">
                                            <i class="fas fa-clipboard-check text-primary"></i>
                                        </div>
                                        <div>
                                            <span class="fw-medium d-block">Reviewed Cases</span>
                                            <small class="text-muted">View case review history</small>
                                        </div>
                                        ${row.reviewed_count > 0 ? '<span class="badge bg-success ms-auto">✓</span>' : ''}
                                    </a>
                                </li>
                                
                                <li><hr class="dropdown-divider"></li>
                                
                                <li>
                                    <a href="${@json(route('crime.courtcase', ':id')).replace(':id', row.id)}" 
                                    class="dropdown-item record-item py-2 d-flex align-items-center">
                                        <div class="icon-wrapper me-2" style="background-color: rgba(236, 201, 75, 0.15);">
                                            <i class="fas fa-gavel text-warning"></i>
                                        </div>
                                        <div>
                                            <span class="fw-medium d-block">Court Cases</span>
                                            <small class="text-muted">View court proceedings</small>
                                        </div>
                                        ${row.court_case_count > 0 ? '<span class="badge bg-warning ms-auto">✓</span>' : ''}
                                    </a>
                                </li>
                                
                                <li><hr class="dropdown-divider"></li>
                                
                                <li>
                                    <a href="${@json(route('crime.appealcase', ':id')).replace(':id', row.id)}" 
                                    class="dropdown-item record-item py-2 d-flex align-items-center">
                                        <div class="icon-wrapper me-2" style="background-color: rgba(237, 100, 100, 0.15);">
                                            <i class="fas fa-balance-scale text-danger"></i>
                                        </div>
                                        <div>
                                            <span class="fw-medium d-block">Appeal Cases</span>
                                            <small class="text-muted">View case appeals</small>
                                        </div>
                                        ${row.appeal_count > 0 ? '<span class="badge bg-danger ms-auto">✓</span>' : ''}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>`;

                    return actions;
                }
            }
        ],
        dom: 'lBfrtip',
        buttons: [
            {
                extend: 'copy',
                className: 'btn btn-sm btn-secondary'
            },
            {
                extend: 'csv',
                className: 'btn btn-sm btn-secondary'
            },
            {
                extend: 'excel',
                className: 'btn btn-sm btn-secondary'
            },
            {
                extend: 'pdf',
                className: 'btn btn-sm btn-secondary'
            }
        ],
        drawCallback: function() {
            positionDropdowns();
        },
        language: {
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
        }
    });

    // Reposition dropdowns on window resize
    $(window).resize(function() {
        positionDropdowns();
    });

    // Reposition dropdowns on scroll
    $('.table-responsive').on('scroll', function() {
        positionDropdowns();
    });

    // Rejection modal setup
    $('#rejectionModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const caseId = button.data('bs-case-id');
        $('#case_id').val(caseId);
        $('#rejectionForm').attr('action', "{{ route('crime.criminalCase.reject', ':id') }}".replace(':id', caseId));
    });
    
    // Touch screen optimization
    if ('ontouchstart' in document.documentElement) {
        $('.dropdown-toggle').on('click', function() {
            $('.dropdown-menu').not($(this).siblings('.dropdown-menu')).removeClass('show');
        });
    }
    
    // Fix dropdown position on long tables with scrolling
    $('.dataTables_scrollBody').on('scroll', function() {
        $('.dropdown-menu.show').removeClass('show');
    });
});

// Accept action
function handleCaseAction(id, action) {
    if (action === 'accept' && confirm('Accept this case?')) {
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

function reallocateCase(caseId) {
    if (confirm('Are you sure you want to reallocate this case?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('crime.criminalCase.reallocate', ':id') }}`.replace(':id', caseId);
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
</script>
@endpush