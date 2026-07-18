@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <nav aria-label="breadcrumb">
        {{ Breadcrumbs::render() }}
    </nav>

    <h2 class="text-center mb-4">Registry Dispatches</h2>

    <div class="table-responsive shadow-lg rounded-3">
        <table class="table table-striped table-hover" id="registry-dispatch-table">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Case Name</th>
                    <th>Date Dispatched</th>
                    <th>Dispatched To</th>
                    <th>Dispatched By</th>
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
        show: @json(route('crime.registry-dispatches.show', ':id')),
        certificate: @json(route('crime.registry-dispatches.certificate', ':id')),
        destroy: @json(route('crime.registry-dispatches.destroy', ':id')),
    };

    const table = $('#registry-dispatch-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('crime.registry-dispatches.datatables') }}',
        columns: [
            { data: 'id' },
            { data: 'case_name', defaultContent: '' },
            { data: 'date_dispatched', render: data => data ? new Date(data).toLocaleDateString() : '' },
            { data: 'dispatched_to', defaultContent: '' },
            { data: 'dispatched_by_name', defaultContent: '' },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (row) {
                    return `
                        <a href="${routeUrls.show.replace(':id', row.id)}" class="btn btn-sm btn-info">Show</a>
                        <a href="${routeUrls.certificate.replace(':id', row.id)}" class="btn btn-sm btn-outline-dark">Certificate</a>
                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteRegistryDispatch(${row.id})">Delete</button>
                    `;
                }
            }
        ]
    });

    window.deleteRegistryDispatch = function (id) {
        if (!confirm('Delete this dispatch record?')) {
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
                alert('Failed to delete the dispatch record.');
            }
        });
    };
});
</script>
@endpush
