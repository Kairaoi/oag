@extends('layouts.app')

@section('content')
    <div class="container">
    @breadcrumbs
        <h1 class="animate__animated animate__bounceInDown animate__delay-1s">Reports</h1>
        <table id="reportsTable" class="table table-striped table-bordered animate__animated animate__fadeInUp animate__delay-2s" style="width:100%">
            <thead>
                <tr>
                    <th class="animate__animated animate__fadeInLeft animate__delay-3s">Name</th>
                    <th class="animate__animated animate__fadeInRight animate__delay-3s">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reports as $report)
                    <tr class="animate__animated animate__fadeInUp animate__delay-4s">
                        <td>{{ $report->name }}</td>
                        <td class="animate__animated animate__fadeIn animate__delay-5s">
                            <a href="{{ route('pims.executeReport', $report->id) }}" class="btn btn-primary btn-sm animate__animated animate__rubberBand animate__delay-6s">Execute</a>
                            <a href="{{ route('pims.dashboard', $report->id) }}" class="btn btn-primary btn-sm animate__animated animate__rubberBand animate__delay-6s">View Dashboard</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#reportsTable').DataTable({
                "aoColumnDefs": [
                    { "bSortable": false, "aTargets": [1] }
                ]
            });
        });
    </script>
@endpush