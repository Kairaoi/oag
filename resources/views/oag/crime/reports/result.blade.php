@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-5 custom-title">Executive Report</h1>

    @if(!empty($results) && (is_array($results) ? count($results) > 0 : $results->count() > 0))
    <div id="reportResults" class="mb-5">
        <div class="table-container">
            <table id="reportTable" class="table table-striped table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        @php
                        $firstRow = is_array($results) ? (object) reset($results) : $results->first();
                        $headers = array_keys((array) $firstRow);
                        @endphp
                        @foreach($headers as $header)
                        <th class="custom-header">{{ ucfirst(str_replace('_', ' ', $header)) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($results as $result)
                    <tr>
                        @php
                        $row = (array) (is_object($result) ? $result : (object) $result);
                        @endphp
                        @foreach($row as $value)
                        <td>{{ $value }}</td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if(is_array($results))
        <div class="pagination-container mt-4">
            {{-- Pagination handling for arrays can be added if needed --}}
        </div>
        @else
        <div class="pagination-container mt-4">
            {{ $results->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>
    @else
    <p class="text-center text-muted">No results found.</p>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<script>
    $(document).ready(function() {
        $('#reportTable').DataTable({
            "paging": true,
            "searching": true,
            "pagingType": "full_numbers",
            "dom": 'lBfrtip',
            "buttons": [
                'copy',
                {
                    "extend": 'csv',
                    "text": '{{ __("Export to CSV") }}',
                    "className": "btn btn-custom"
                },
                {
                    "extend": 'excel',
                    "text": '{{ __("Export to Excel") }}',
                    "className": "btn btn-custom"
                }
            ],
            "responsive": true
        });

        $('#reportResults').hide().slideDown(1000);
    });
</script>
@endpush

@push('styles')
<style>
    .custom-title {
        font-size: 3.5rem;
        color: #00274d;
        text-shadow: 4px 4px 8px rgba(0, 0, 0, 0.4);
        border-bottom: 5px solid #0033a0;
        padding-bottom: 20px;
        margin-bottom: 50px;
        font-weight: bold;
    }

    .table-container {
        position: relative;
        background: linear-gradient(135deg, #f0f4f8, #e0e8f0);
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3);
        border: 3px solid #0033a0;
    }

    .table {
        border-radius: 12px;
        border: none;
        background: #ffffff;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        animation: fadeIn 1.5s ease-in-out;
    }

    .table th, .table td {
        text-align: center;
        vertical-align: middle;
        padding: 16px;
    }

    .thead-dark th {
        background-color: #0033a0;
        color: #ffffff;
        font-weight: bold;
        border-bottom: 3px solid #00274d;
    }

    .custom-header {
        font-size: 1.3rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .btn-custom {
        border: 2px solid #0033a0;
        color: #ffffff;
        background-color: #0033a0;
        font-weight: bold;
        padding: 8px 16px;
        margin: 0 5px;
    }

    .btn-custom:hover {
        background-color: #00274d;
        border-color: #00274d;
    }

    .pagination-container .pagination {
        justify-content: center;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>
@endpush
