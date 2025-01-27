@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('civil.civilcase.index') }}">Civil Cases</a></li>
            <li class="breadcrumb-item active" aria-current="page">Civil Case Details</li>
        </ol>
    </nav>

    <!-- Heading -->
    <h1 class="text-center mb-4">Civil Case Details</h1>

    <!-- Civil Case Details Table -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Case Name</th>
                <th>Case Type</th>
                <th>Court Category</th>
                <th>Status</th>
                <th>Status Date</th>
                <th>Action Required</th>
                <th>Monitoring Status</th>
                <th>Entered by SG/DSG</th>
               
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @if ($civilCases)
    <tr>
        <td>{{ $civilCases->case_name }}</td>
        <td>{{ $civilCases->case_type_name }}</td>
        <td>{{ $civilCases->court_category_name }}</td>
        <td>{{ $civilCases->current_status }}</td>
        <td>{{ \Carbon\Carbon::parse($civilCases->status_date)->format('d M, Y') }}</td>
        <td>{{ $civilCases->action_required }}</td>
        <td>{{ $civilCases->monitoring_status }}</td>
        <td>{{ $civilCases->entered_by_sg_dsg ? 'Yes' : 'No' }}</td>
        
        <td>
            <a href="{{ route('civil.civilcase.edit', $civilCases->id) }}" class="btn btn-primary btn-sm">Edit</a>
        </td>
    </tr>
@else
    <tr>
        <td colspan="11" class="text-center">No case found</td>
    </tr>
@endif

        </tbody>
    </table>
</div>
@endsection
