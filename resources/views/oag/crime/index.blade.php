@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        
        {{ Breadcrumbs::render() }}
        
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
                    <th>Police Case File Number</th>
                    <th>Case Name</th>
                    <th>Date File Received</th>
                    <th>Date of Incident</th>
                    <th>Island</th>
                    <th>Counsel</th>
                    <th>Accused</th>
                    <th>Victim</th>
                    <th>Reviewer Action</th>
                    <th>Case Status</th>
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

    .btn-workflow {
        background: linear-gradient(135deg, #38a169, #2f855a);
        border: none;
        color: white;
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(50, 50, 93, 0.11);
    }

    .btn-workflow:hover {
        color: white;
        box-shadow: 0 7px 14px rgba(50, 50, 93, 0.15);
        transform: translateY(-1px);
    }

    .btn-actions-compact {
        padding: 0.3rem 0.6rem;
        font-size: 0.85rem;
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
    
    /* Custom badge colors */
    .bg-purple {
        background-color: #6f42c1 !important;
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
    
</style>

<script>
    const userRoles = {
        canCaseReview: {{ auth()->user()->hasRole('cm.user') ? 'true' : 'false' }},
        canCourtCase: {{ auth()->user()->hasRole('cm.user') ? 'true' : 'false' }},
        canReallocate: {{ auth()->user()->hasRole('cm.admin') ? 'true' : 'false' }},
        canAppeal: {{ auth()->user()->hasRole('cm.user') ? 'true' : 'false' }},
        canallocate: {{ auth()->user()->hasRole('cm.admin') ? 'true' : 'false' }},
        canSubmitToAg: {{ auth()->user()->hasRole('cm.user') ? 'true' : 'false' }},
        canDispatch: {{ auth()->user()->hasRole('cm.registrar') ? 'true' : 'false' }},
        canViewRelatedRecords: {{ (auth()->user()->hasRole('cm.user') || auth()->user()->hasRole('cm.admin')) ? 'true' : 'false' }},
    };

    // Define route URLs for cleaner code
    const routeUrls = {
        edit: @json(route('crime.criminalCase.edit', ':id')),
        show: @json(route('crime.criminalCase.show', ':id')),
        caseReview: @json(route('crime.CaseReview.create', ':id')),
        courtCase: @json(route('crime.CourtCase.create', ':id')),
        allocate: @json(route('crime.criminalCase.allocateForm', ':id')),
        reallocate: @json(route('crime.criminalCase.showReallocationForm', ':id')),
        appeal: @json(route('crime.appeal.create', ':id')),
        courtOfAppeal: @json(route('crime.courtOfAppeal.create', ':id')),
        agReview: @json(route('crime.AgReview.create', ':id')),
        registryDispatch: @json(route('crime.RegistryDispatch.create', ':id')),
        destroy: @json(route('crime.criminalCase.destroy', ':id')),
        reviewedCases: @json(route('crime.casereview.reviewed', ':id')),
        courtCases: @json(route('crime.courtcase', ':id')),
        appealCases: @json(route('crime.appealcase', ':id')),
        courtOfAppealCases: @json(route('crime.courtofappealcase', ':id')),
        relatedRecords: @json(route('crime.relatedRecords', ':id'))
    };
</script>

<script>
$(document).ready(function () {
    // The table sits inside a scrollable .table-responsive container, which
    // clips any dropdown-menu positioned with Bootstrap's default "absolute"
    // Popper strategy the moment it would overflow that container's bounds
    // (horizontally or vertically). Rendering the menu with "fixed" strategy
    // instead positions it relative to the viewport, so it floats freely above
    // the table and its scrollbars — Popper also handles flipping it above/below
    // and repositioning on scroll/resize automatically, so no manual dropup
    // class juggling is needed anymore.
    function initDropdowns() {
        document.querySelectorAll('#criminal-case-table [data-bs-toggle="dropdown"]').forEach(function (toggleEl) {
            const existing = bootstrap.Dropdown.getInstance(toggleEl);
            if (existing) {
                existing.dispose();
            }

            new bootstrap.Dropdown(toggleEl, {
                popperConfig: (defaultBsPopperConfig) => ({
                    ...defaultBsPopperConfig,
                    strategy: 'fixed',
                }),
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
                data: 'accused_names',
                render: function(names) {
                    if (!names) {
                        return '<span class="badge bg-danger" title="No accused added yet">None added</span>';
                    }
                    return names;
                }
            },
            {
                data: 'victim_names',
                defaultContent: '<span class="text-muted">&mdash;</span>',
                render: function(names) {
                    return names ? names : '<span class="text-muted">&mdash;</span>';
                }
            },
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
                        if (status === 'allocated' || status === 'reallocated') {
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
                data: 'case_status',
                render: function(caseStatus) {
                    const badgeClasses = {
                        'Completed': 'bg-success',
                        'Judgment Delivered': 'bg-warning',
                        'Pending in Court': 'bg-primary',
                        'Appealed': 'bg-purple',
                        'Court of Appeal': 'bg-danger',
                        'Appeal Withdrawn': 'bg-secondary',
                        'Rejected': 'bg-danger',
                        'Under Review': 'bg-info',
                        'Reviewed': 'bg-info',
                        'Allocated': 'bg-primary',
                        'Pending Allocation': 'bg-warning',
                        'Returned — Further Action Required': 'bg-warning',
                        'Closed — Insufficient Evidence': 'bg-secondary',
                        'Submitted to AG': 'bg-info',
                        'Returned for Revision': 'bg-danger',
                        'AG Approved': 'bg-success',
                        'Dispatched': 'bg-primary',
                    };
                    const badgeClass = badgeClasses[caseStatus] || 'bg-secondary';
                    return `<span class="badge ${badgeClass}">${caseStatus}</span>`;
                }
            },
            {
                data: null,
                className: 'position-relative',
                orderable: false,
                searchable: false,
                render: function(row) {
                    const canReviewNow = userRoles.canCaseReview && row.status === 'accepted';
                    // Court Case now also requires the case to have been
                    // dispatched by the Registry (AG-approved first) — see
                    // AuthorizesCriminalCase::assertCaseIsDispatched().
                    const canCourtCaseNow = userRoles.canCourtCase && row.status === 'accepted' && row.registry_dispatch_count > 0;
                    const canAllocateNow = userRoles.canallocate && row.status === 'pending';
                    const canReallocateNow = userRoles.canReallocate && row.status === 'rejected';
                    // Appeal / Court of Appeal previously showed for any
                    // accepted case, with no link to the case's own progress —
                    // a lawyer could file an "Appeal" before the AG had even
                    // approved the case for court. Now gated the same way as
                    // "Dispatch to Court": the AG must have approved it.
                    const canAppealNow = userRoles.canAppeal && row.status === 'accepted' && row.latest_ag_decision === 'approved';
                    // A case is ready for AG submission once reviewed with
                    // sufficient evidence, and either has no submission yet or
                    // its last one was rejected (the lawyer can revise and
                    // resubmit — see AgReviewController).
                    const canSubmitToAgNow = userRoles.canSubmitToAg && row.status === 'accepted' && row.reviewed_count > 0
                        && (!row.latest_ag_decision || row.latest_ag_decision === 'rejected');
                    const canDispatchNow = userRoles.canDispatch && row.latest_ag_decision === 'approved' && !row.registry_dispatch_count;
                    const hasWorkflowItems = canReviewNow || canCourtCaseNow || canAllocateNow || canReallocateNow
                        || canAppealNow || canSubmitToAgNow || canDispatchNow;

                    // Record actions dropdown: Edit / Show / Delete only
                    let actions = `<div class="d-md-flex gap-2">`;

                    // Edit/Show/Delete are hidden until the case is accepted,
                    // matching the Workflow dropdown — nothing here to act on
                    // for a case that's still pending/allocated/rejected.
                    if (row.status === 'accepted') {
                        actions += `
                        <!-- Record Actions Dropdown -->
                        <div class="dropdown mb-2 mb-md-0">
                            <button class="btn btn-primary btn-sm dropdown-toggle btn-actions-compact" type="button" id="actionsDropdown${row.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-cog me-1"></i> Actions
                            </button>
                            <ul class="dropdown-menu shadow" aria-labelledby="actionsDropdown${row.id}">
                                <li><a class="dropdown-item" href="${routeUrls.edit.replace(':id', row.id)}">
                                    <i class="fas fa-edit text-primary"></i> Edit
                                </a></li>
                                <li><a class="dropdown-item" href="${routeUrls.show.replace(':id', row.id)}">
                                    <i class="fas fa-eye text-info"></i> Show
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); if(confirm('Are you sure?')) document.getElementById('delete-form-${row.id}').submit();">
                                        <i class="fas fa-trash-alt text-danger"></i> Delete
                                    </a>
                                    <form id="delete-form-${row.id}" action="${routeUrls.destroy.replace(':id', row.id)}" method="POST" class="d-none">
                                        @csrf @method('DELETE')
                                    </form>
                                </li>
                            </ul>
                        </div>`;
                    }

                    // Workflow Actions Dropdown: Case Review / Court Case / Allocate / Reallocate / Appeal / Court of Appeal
                    if (hasWorkflowItems) {
                        actions += `
                        <div class="dropdown mb-2 mb-md-0">
                            <button class="btn btn-workflow dropdown-toggle" type="button" id="workflowDropdown${row.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-diagram-project me-1"></i> Workflow
                            </button>
                            <ul class="dropdown-menu shadow" aria-labelledby="workflowDropdown${row.id}">`;

                        if (canReviewNow) {
                            actions += `<li>
                                <a class="dropdown-item d-flex justify-content-between align-items-center" href="${routeUrls.caseReview.replace(':id', row.id)}">
                                    <span><i class="fas fa-clipboard-check text-success"></i> Case Review</span>
                                    ${row.reviewed_count > 0 ? '<span class="badge bg-success">✓</span>' : ''}
                                </a>
                            </li>`;
                        }

                        if (canSubmitToAgNow) {
                            actions += `<li>
                                <a class="dropdown-item d-flex justify-content-between align-items-center" href="${routeUrls.agReview.replace(':id', row.id)}">
                                    <span><i class="fas fa-landmark text-info"></i> Submit to AG</span>
                                    ${row.latest_ag_decision ? '<span class="badge bg-info">✓</span>' : ''}
                                </a>
                            </li>`;
                        }

                        if (canDispatchNow) {
                            actions += `<li>
                                <a class="dropdown-item d-flex justify-content-between align-items-center" href="${routeUrls.registryDispatch.replace(':id', row.id)}">
                                    <span><i class="fas fa-paper-plane text-primary"></i> Dispatch to Court</span>
                                    ${row.registry_dispatch_count > 0 ? '<span class="badge bg-primary">✓</span>' : ''}
                                </a>
                            </li>`;
                        }

                        if (canCourtCaseNow) {
                            actions += `<li>
                                <a class="dropdown-item d-flex justify-content-between align-items-center" href="${routeUrls.courtCase.replace(':id', row.id)}">
                                    <span><i class="fas fa-gavel text-warning"></i> Court Case</span>
                                    ${row.court_case_count > 0 ? '<span class="badge bg-warning">✓</span>' : ''}
                                </a>
                            </li>`;
                        }

                        if (canAllocateNow) {
                            actions += `<li><a class="dropdown-item" href="${routeUrls.allocate.replace(':id', row.id)}">
                                <i class="fas fa-user-check text-primary"></i> Case Allocation
                            </a></li>`;
                        }

                        if (canReallocateNow) {
                            actions += `<li><a class="dropdown-item" href="${routeUrls.reallocate.replace(':id', row.id)}">
                                <i class="fas fa-exchange-alt text-info"></i> Case Reallocate
                            </a></li>`;
                        }

                        if (canAppealNow) {
                            actions += `<li>
                                <a class="dropdown-item d-flex justify-content-between align-items-center" href="${routeUrls.appeal.replace(':id', row.id)}">
                                    <span><i class="fas fa-balance-scale text-danger"></i> Appeal</span>
                                    ${row.appeal_count > 0 ? '<span class="badge bg-danger">✓</span>' : ''}
                                </a>
                            </li>`;
                        }

                        if (canAppealNow) {
                            actions += `<li>
                                <a class="dropdown-item d-flex justify-content-between align-items-center" href="${routeUrls.courtOfAppeal.replace(':id', row.id)}">
                                    <span><i class="fas fa-balance-scale text-danger"></i> Court of Appeal</span>
                                    ${row.court_of_appeal_count > 0 ? '<span class="badge bg-danger">✓</span>' : ''}
                                </a>
                            </li>`;
                        }

                        actions += `
                            </ul>
                        </div>`;
                    }

                    if (row.status === 'accepted' && userRoles.canViewRelatedRecords) {
                        actions += `
                        <a href="${routeUrls.relatedRecords.replace(':id', row.id)}" class="btn btn-related">
                            <i class="fas fa-folder-open me-1"></i> Related Records
                        </a>`;
                    }

                    actions += `</div>`;

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
            initDropdowns();
        },
        language: {
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
        }
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