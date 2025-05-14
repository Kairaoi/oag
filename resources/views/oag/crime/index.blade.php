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
    <div class="table-responsive shadow-lg rounded-3 overflow-visible" style="background: linear-gradient(145deg, #f1f1f1, #ffffff); border: 2px solid #007bff; border-radius: 20px;">
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

<!-- Custom Styles -->
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
    .dropdown-menu {
        z-index: 9999 !important;
    }
    .table-responsive {
        overflow: visible !important;
        position: relative;
    }
    table.dataTable tbody td {
        overflow: visible !important;
        position: relative;
    }
     /* For arrow rotation on panel toggle */
     .collapse.show + button .fa-chevron-down {
        transform: rotate(180deg);
    }
    
    /* Hover effects for panel items */
    .list-group-item-action:hover {
        background-color: #f8fafc;
        transform: translateX(5px);
        transition: all 0.2s ease;
    }
    
    /* Animation for panel open/close */
    .collapse {
        transition: height 0.3s ease;
    }
</style>
<script>
    const userRoles = {
        canCaseReview: {{ auth()->user()->hasRole('cm.user') ? 'true' : 'false' }},
        canCourtCase: {{ auth()->user()->hasRole('cm.user') ? 'true' : 'false' }},
        canReallocate: {{ auth()->user()->hasRole('cm.admin') ? 'true' : 'false' }},
        canAppeal: {{ auth()->user()->hasRole('cm.user') ? 'true' : 'false' }}
    };
</script>
<!-- Bootstrap 5 JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function () {
    const table = $('#criminal-case-table').DataTable({
        processing: true,
        serverSide: true,
        destroy: true, 
        ajax: '{{ route('crime.criminalCase.datatables') }}',
        columns: [
            { data: 'id' },
            { data: 'case_file_number' },
            { data: 'case_name' },
            { data: 'date_file_received', render: data => data ? new Date(data).toLocaleDateString() : '' },
            { data: 'date_of_allocation', render: data => data ? new Date(data).toLocaleDateString() : '' },
            { data: 'reason_description', defaultContent: 'N/A' },
            { data: 'island_name', defaultContent: 'N/A' },
            { data: 'lawyer_name', defaultContent: 'N/A' },
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
            } else if (status === 'closed') {
                statusLabel = '<span class="badge bg-secondary">Closed</span>';
            } else {
                statusLabel = '<span class="badge bg-secondary">N/A</span>';
            }
        @endif

        @if(auth()->user()->hasRole('cm.user'))
            if (status !== 'accepted' && status !== 'rejected' && status !== 'closed') {
                actionButtons = `
                    <div class="d-flex justify-content-around mt-2">
                        <button class="btn btn-accept me-2" onclick="handleCaseAction(${row.id}, 'accept')">
                            <i class="fas fa-check"></i> Accept
                        </button>
                        <button class="btn btn-reject" data-bs-toggle="modal" data-bs-target="#rejectionModal" data-bs-case-id="${row.id}">
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
            render: function(row) {
                let actions = `
                    <div>
                        <!-- Dropdown Menu -->
                        <div class="dropdown d-inline">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton${row.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                Actions
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton${row.id}">
                                <li><a class="dropdown-item" href="${@json(route('crime.criminalCase.edit', ':id')).replace(':id', row.id)}">Edit</a></li>
                                <li><a class="dropdown-item" href="${@json(route('crime.criminalCase.show', ':id')).replace(':id', row.id)}">Show</a></li>`;

                                if (userRoles.canCaseReview && row.status === 'accepted') {
    actions += `<li>
        <a class="dropdown-item d-flex justify-content-between align-items-center" href="${@json(route('crime.CaseReview.create', ':id')).replace(':id', row.id)}">
            Case Review
            ${row.is_reviewed ? '<i class="fas fa-check text-success ms-2"></i>' : ''}
        </a>
    </li>`;
}


                if (userRoles.canCourtCase && row.status === 'accepted') {
                    actions += `<li><a class="dropdown-item" href="${@json(route('crime.CourtCase.create', ':id')).replace(':id', row.id)}">Court Case</a></li>`;
                }

                if (userRoles.canReallocate && row.status === 'rejected') {
                    actions += `<li><a class="dropdown-item" href="${@json(route('crime.criminalCase.showReallocationForm', ':id')).replace(':id', row.id)}">Case Reallocate</a></li>`;
                }

                if (userRoles.canAppeal) {
                    actions += `<li><a class="dropdown-item" href="${@json(route('crime.appeal.create', ':id')).replace(':id', row.id)}">Appeal</a></li>`;
                }

                actions += `
                                <li>
                                    <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); if(confirm('Are you sure?')) document.getElementById('delete-form-${row.id}').submit();">Delete</a>
                                    <form id="delete-form-${row.id}" action="${@json(route('crime.criminalCase.destroy', ':id')).replace(':id', row.id)}" method="POST" class="d-none">
                                        @csrf @method('DELETE')
                                    </form>
                                </li>
                            </ul>
                        </div>

                      <!-- Replace the Collapsible Panel with this Dropdown Panel -->
<div class="mt-2" id="caseRecordsDropdown${row.id}">
    <div class="dropdown d-inline">
        <button class="btn btn-info dropdown-toggle" 
                type="button" 
                id="caseRecordsButton${row.id}" 
                data-bs-toggle="dropdown" 
                aria-expanded="false"
                style="background: linear-gradient(to right, #4299e1, #3182ce); border: none; box-shadow: 0 4px 6px rgba(50, 50, 93, 0.11); color: white; font-weight: 500; padding: 0.5rem 1rem; border-radius: 0.5rem;">
            <i class="fas fa-folder-open me-2"></i> Related Records
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" 
            aria-labelledby="caseRecordsButton${row.id}"
            style="min-width: 280px; border-radius: 0.5rem; overflow: hidden;">
            
            <li class="dropdown-header py-2 px-3" style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                <span class="fw-bold">Case ID: ${row.id}</span>
            </li>
            
            <li>
                <a href="${@json(route('crime.casereview.reviewed', ':id')).replace(':id', row.id)}" 
                   class="dropdown-item py-3 d-flex align-items-center">
                    <div class="icon-wrapper me-3 d-flex justify-content-center align-items-center" 
                         style="width: 35px; height: 35px; background-color: rgba(66, 153, 225, 0.15); border-radius: 8px; flex-shrink: 0;">
                        <i class="fas fa-clipboard-check text-primary"></i>
                    </div>
                    <div>
                        <span class="fw-medium d-block">Reviewed Cases</span>
                        <small class="text-muted">View case review history</small>
                    </div>
                </a>
            </li>
            
            <li><hr class="dropdown-divider" style="margin: 0;"></li>
            
            <li>
                <a href="${@json(route('crime.courtcase', ':id')).replace(':id', row.id)}" 
                   class="dropdown-item py-3 d-flex align-items-center">
                    <div class="icon-wrapper me-3 d-flex justify-content-center align-items-center" 
                         style="width: 35px; height: 35px; background-color: rgba(236, 201, 75, 0.15); border-radius: 8px; flex-shrink: 0;">
                        <i class="fas fa-gavel text-warning"></i>
                    </div>
                    <div>
                        <span class="fw-medium d-block">Court Cases</span>
                        <small class="text-muted">View court proceedings</small>
                    </div>
                </a>
            </li>
            
            <li><hr class="dropdown-divider" style="margin: 0;"></li>
            
            <li>
                <a href="${@json(route('crime.appealcase', ':id')).replace(':id', row.id)}" 
                   class="dropdown-item py-3 d-flex align-items-center">
                    <div class="icon-wrapper me-3 d-flex justify-content-center align-items-center" 
                         style="width: 35px; height: 35px; background-color: rgba(237, 100, 100, 0.15); border-radius: 8px; flex-shrink: 0;">
                        <i class="fas fa-balance-scale text-danger"></i>
                    </div>
                    <div>
                        <span class="fw-medium d-block">Appeal Cases</span>
                        <small class="text-muted">View case appeals</small>
                    </div>
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Add these styles to your existing style section -->
<style>
    /* Enhanced dropdown styling */
    .dropdown-item {
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
    }
    
    .dropdown-item:hover {
        background-color: #f8fafc;
        border-left: 3px solid #3182ce;
        transform: translateX(5px);
    }
    
    .dropdown-menu {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    .dropdown-header {
        font-size: 0.85rem;
        color: #4a5568;
    }
</style>

                    </div>`;

                return actions;
            }
        }



        ],
        dom: 'lBfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf']
    });

    // Dropdown fix for bottom rows
    $('#criminal-case-table').on('draw.dt', function () {
        $('#criminal-case-table tbody tr').each(function () {
            const $dropdown = $(this).find('.dropdown');
            $dropdown.removeClass('dropup');
            if ($(window).height() - $(this).offset().top < 200) {
                $dropdown.addClass('dropup');
            }
        });
    });

    $('#rejectionModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const caseId = button.data('bs-case-id');
        $('#case_id').val(caseId);
        $('#rejectionForm').attr('action', "{{ route('crime.criminalCase.reject', ':id') }}".replace(':id', caseId));
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
    $('[data-bs-toggle="collapse"]').on('click', function() {
        $(this).find('.fa-chevron-down').toggleClass('rotate-180');
    });
    
    // Add the rotate class
    $('.rotate-180').css('transform', 'rotate(180deg)');

</script>
@endpush
