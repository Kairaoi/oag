@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <nav aria-label="breadcrumb">
        {{ Breadcrumbs::render() }}
    </nav>

    <h2 class="text-center mb-4">Appeal Cases</h2>

    <div class="table-responsive shadow-lg rounded-3">
        <table class="table table-striped table-hover" id="appeal-details-table">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Case Name</th>
                    <th>Appeal Case No.</th>
                    <th>Filing Date</th>
                    <th>Verdict</th>
                    <th>Outcome</th>
                    <th>Judgment Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function () {
    const routeUrls = {
        edit: @json(route('crime.appeal.edit', ':id')),
        destroy: @json(route('crime.appeal.destroy', ':id')),
    };

    const table = $('#appeal-details-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('crime.appeal.datatables') }}',
        columns: [
            { data: 'id' },
            { data: 'case_name', defaultContent: '' },
            { data: 'appeal_case_number', defaultContent: '' },
            { data: 'appeal_filing_date', render: data => data ? new Date(data).toLocaleDateString() : '' },
            { data: 'verdict', render: data => data ? data.charAt(0).toUpperCase() + data.slice(1).replace('_', ' ') : '' },
            { data: 'court_outcome', render: data => data ? data.charAt(0).toUpperCase() + data.slice(1) : '' },
            { data: 'judgment_delivered_date', render: data => data ? new Date(data).toLocaleDateString() : '' },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (row) {
                    return `
                        <a href="${routeUrls.edit.replace(':id', row.id)}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteAppeal(${row.id})">
                            <i class="fas fa-trash-alt"></i> Delete
                        </button>
                    `;
                }
            }
        ]
    });

    window.deleteAppeal = function (id) {
        if (!confirm('Delete this appeal record?')) {
            return;
        }

        $.ajax({
            url: routeUrls.destroy.replace(':id', id),
            method: 'POST',
            data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
            success: function () {
                table.ajax.reload(null, false);
            },
            error: function () {
                alert('Failed to delete the appeal record.');
            }
        });
    };
});
</script>
@endpush
