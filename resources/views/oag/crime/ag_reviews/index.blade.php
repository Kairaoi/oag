@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <nav aria-label="breadcrumb">
        {{ Breadcrumbs::render() }}
    </nav>

    <h2 class="text-center mb-4">AG Reviews</h2>

    <div class="table-responsive shadow-lg rounded-3">
        <table class="table table-striped table-hover" id="ag-review-table">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Case Name</th>
                    <th>Submitted At</th>
                    <th>Submitted By</th>
                    <th>AG Decision</th>
                    <th>Decision Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function () {
    const routeUrls = {
        edit: @json(route('crime.ag-reviews.edit', ':id')),
        show: @json(route('crime.ag-reviews.show', ':id')),
        destroy: @json(route('crime.ag-reviews.destroy', ':id')),
    };

    const table = $('#ag-review-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('crime.ag-reviews.datatables') }}',
        columns: [
            { data: 'id' },
            { data: 'case_name', defaultContent: '' },
            { data: 'submitted_at', render: data => data ? new Date(data).toLocaleDateString() : '' },
            { data: 'submitted_by_name', defaultContent: '' },
            {
                data: 'ag_decision',
                render: function (decision) {
                    const badgeClasses = { pending: 'bg-warning', approved: 'bg-success', rejected: 'bg-danger' };
                    const badgeClass = badgeClasses[decision] || 'bg-secondary';
                    return `<span class="badge ${badgeClass}">${decision ? decision.charAt(0).toUpperCase() + decision.slice(1) : ''}</span>`;
                }
            },
            { data: 'decision_date', render: data => data ? new Date(data).toLocaleDateString() : '' },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (row) {
                    return `
                        <a href="${routeUrls.show.replace(':id', row.id)}" class="btn btn-sm btn-info">Show</a>
                        <a href="${routeUrls.edit.replace(':id', row.id)}" class="btn btn-sm btn-primary">Edit</a>
                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteAgReview(${row.id})">Delete</button>
                    `;
                }
            }
        ]
    });

    window.deleteAgReview = function (id) {
        if (!confirm('Delete this AG review record?')) {
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
                alert('Failed to delete the AG review.');
            }
        });
    };
});
</script>
@endpush
