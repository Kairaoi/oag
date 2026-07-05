@extends('layouts.app')

@section('content')
<div class="container mt-4 mb-5">
    <nav aria-label="breadcrumb">
        {{ Breadcrumbs::render() }}
    </nav>

    <h2 class="mb-1">Related Records</h2>
    <p class="text-muted mb-3">Case #{{ $case->id }} &mdash; {{ $case->case_name }}</p>

    <div class="mb-3">
        <a href="{{ route('crime.caseTimeline', $case->id) }}" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-stream"></i> View Case Timeline
        </a>
    </div>

    <ul class="nav nav-tabs" id="relatedRecordsTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="reviewed-tab" data-bs-toggle="tab" data-bs-target="#reviewed-pane" type="button" role="tab">
                <i class="fas fa-clipboard-check text-primary me-1"></i> Reviewed Cases
                @if($caseReviews->count() > 0)<span class="badge bg-success ms-1">{{ $caseReviews->count() }}</span>@endif
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="court-tab" data-bs-toggle="tab" data-bs-target="#court-pane" type="button" role="tab">
                <i class="fas fa-gavel text-warning me-1"></i> Court Cases
                @if($courtCases->count() > 0)<span class="badge bg-warning ms-1">{{ $courtCases->count() }}</span>@endif
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="appeal-tab" data-bs-toggle="tab" data-bs-target="#appeal-pane" type="button" role="tab">
                <i class="fas fa-balance-scale text-danger me-1"></i> Appeal Cases
                @if($appealDetails->count() > 0)<span class="badge bg-danger ms-1">{{ $appealDetails->count() }}</span>@endif
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="coa-tab" data-bs-toggle="tab" data-bs-target="#coa-pane" type="button" role="tab">
                <i class="fas fa-gavel text-success me-1"></i> Court of Appeal Cases
                @if($courtOfAppeals->count() > 0)<span class="badge bg-success ms-1">{{ $courtOfAppeals->count() }}</span>@endif
            </button>
        </li>
    </ul>

    <div class="tab-content border border-top-0 p-3" id="relatedRecordsTabsContent">
        {{-- Reviewed Cases --}}
        <div class="tab-pane fade show active" id="reviewed-pane" role="tabpanel">
            <table class="table table-striped table-hover" id="reviewed-table">
                <thead>
                    <tr>
                        <th>Review Date</th>
                        <th>Evidence Status</th>
                        <th>Reviewed By</th>
                        <th>Offences</th>
                        <th>Date Closed</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($caseReviews as $review)
                    <tr>
                        <td>{{ $review->review_date ? \Carbon\Carbon::parse($review->review_date)->format('d/m/Y') : '' }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $review->evidence_status)) }}</td>
                        <td>{{ $review->created_by_name }}</td>
                        <td>{{ $review->offence_names }}</td>
                        <td>{{ $review->date_file_closed ? \Carbon\Carbon::parse($review->date_file_closed)->format('d/m/Y') : '' }}</td>
                        <td>
                            <a href="{{ route('crime.CaseReview.edit', $review->id) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Court Cases --}}
        <div class="tab-pane fade" id="court-pane" role="tabpanel">
            <table class="table table-striped table-hover" id="court-table">
                <thead>
                    <tr>
                        <th>Charge Filed</th>
                        <th>High Court Case No.</th>
                        <th>Verdict</th>
                        <th>Outcome</th>
                        <th>Judgment Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($courtCases as $courtCase)
                    <tr>
                        <td>{{ $courtCase->charge_file_dated ? \Carbon\Carbon::parse($courtCase->charge_file_dated)->format('d/m/Y') : '' }}</td>
                        <td>{{ $courtCase->high_court_case_number }}</td>
                        <td>{{ $courtCase->verdict ? ucfirst(str_replace('_', ' ', $courtCase->verdict)) : '' }}</td>
                        <td>{{ $courtCase->court_outcome ? ucfirst($courtCase->court_outcome) : '' }}</td>
                        <td>{{ $courtCase->judgment_delivered_date ? \Carbon\Carbon::parse($courtCase->judgment_delivered_date)->format('d/m/Y') : '' }}</td>
                        <td>
                            <a href="{{ route('crime.court-cases.edit', $courtCase->id) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Appeal Cases --}}
        <div class="tab-pane fade" id="appeal-pane" role="tabpanel">
            <table class="table table-striped table-hover" id="appeal-table">
                <thead>
                    <tr>
                        <th>Appeal Case No.</th>
                        <th>Filing Date</th>
                        <th>Verdict</th>
                        <th>Outcome</th>
                        <th>Judgment Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($appealDetails as $appeal)
                    <tr>
                        <td>{{ $appeal->appeal_case_number }}</td>
                        <td>{{ $appeal->appeal_filing_date ? \Carbon\Carbon::parse($appeal->appeal_filing_date)->format('d/m/Y') : '' }}</td>
                        <td>{{ $appeal->verdict ? ucfirst(str_replace('_', ' ', $appeal->verdict)) : '' }}</td>
                        <td>{{ $appeal->court_outcome ? ucfirst($appeal->court_outcome) : '' }}</td>
                        <td>{{ $appeal->judgment_delivered_date ? \Carbon\Carbon::parse($appeal->judgment_delivered_date)->format('d/m/Y') : '' }}</td>
                        <td>
                            <a href="{{ route('crime.appeal.edit', $appeal->id) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Court of Appeal Cases --}}
        <div class="tab-pane fade" id="coa-pane" role="tabpanel">
            <table class="table table-striped table-hover" id="coa-table">
                <thead>
                    <tr>
                        <th>Appeal Case No.</th>
                        <th>Filing Date</th>
                        <th>Outcome</th>
                        <th>Judgment Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($courtOfAppeals as $coa)
                    <tr>
                        <td>{{ $coa->appeal_case_number }}</td>
                        <td>{{ $coa->appeal_filing_date ? \Carbon\Carbon::parse($coa->appeal_filing_date)->format('d/m/Y') : '' }}</td>
                        <td>{{ $coa->court_outcome ? ucfirst($coa->court_outcome) : '' }}</td>
                        <td>{{ $coa->judgment_delivered_date ? \Carbon\Carbon::parse($coa->judgment_delivered_date)->format('d/m/Y') : '' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('crime.criminalCase.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to All Cases
        </a>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
    .nav-tabs .nav-link.active {
        font-weight: 600;
        border-bottom-color: #fff;
    }
    .nav-tabs .badge {
        font-size: 0.7rem;
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
$(document).ready(function () {
    const dtConfig = {
        dom: 'Bfrtip',
        buttons: [
            { extend: 'copy', className: 'btn btn-sm btn-secondary' },
            { extend: 'csv', className: 'btn btn-sm btn-secondary' },
            { extend: 'excel', className: 'btn btn-sm btn-secondary' },
            { extend: 'pdf', className: 'btn btn-sm btn-secondary' },
        ],
    };

    // Only the active tab's table is initialized eagerly; the rest are
    // initialized the first time their tab is shown (DataTables can't size
    // a table correctly while its container is display:none).
    const initialized = {};

    function initTable(tableId, extraConfig) {
        if (initialized[tableId]) {
            return;
        }
        $('#' + tableId).DataTable($.extend(true, {}, dtConfig, extraConfig || {}));
        initialized[tableId] = true;
    }

    // Both tables have a trailing Actions column of raw HTML links — excluded
    // from sorting and from the Copy/CSV/Excel/PDF export buttons.
    const actionsColumnConfig = {
        columnDefs: [{ targets: -1, orderable: false }],
        buttons: [
            { extend: 'copy', className: 'btn btn-sm btn-secondary', exportOptions: { columns: ':not(:last-child)' } },
            { extend: 'csv', className: 'btn btn-sm btn-secondary', exportOptions: { columns: ':not(:last-child)' } },
            { extend: 'excel', className: 'btn btn-sm btn-secondary', exportOptions: { columns: ':not(:last-child)' } },
            { extend: 'pdf', className: 'btn btn-sm btn-secondary', exportOptions: { columns: ':not(:last-child)' } },
        ],
    };

    initTable('reviewed-table', actionsColumnConfig);

    const tablesWithActionsColumn = ['court-table', 'appeal-table'];

    document.querySelectorAll('#relatedRecordsTabs button[data-bs-toggle="tab"]').forEach(function (tabEl) {
        tabEl.addEventListener('shown.bs.tab', function (event) {
            const paneId = event.target.getAttribute('data-bs-target').replace('#', '');
            const tableId = paneId.replace('-pane', '-table');
            initTable(tableId, tablesWithActionsColumn.includes(tableId) ? actionsColumnConfig : null);
        });
    });
});
</script>
@endpush
