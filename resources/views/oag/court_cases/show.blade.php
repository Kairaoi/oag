@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('crime.court-hearings.index') }}">Court Hearings</a></li>
            <li class="breadcrumb-item active" aria-current="page">Show Court Hearing</li>
        </ol>
    </nav>

    <!-- Heading -->
    <h2 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #f8f9fa; text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.4); border-bottom: 4px double #ff6f61; padding-bottom: 20px;">
        Court Hearing Details
    </h2>

    <div class="card shadow-lg rounded-3">
        <div class="card-body">
            <div class="mb-4">
                <strong>Case Name:</strong> {{ $courtCase->case->case_name }}
            </div>
            <div class="mb-4">
                <strong>Charge File Dated:</strong> {{ \Carbon\Carbon::parse($courtCase->charge_file_dated)->format('M d, Y') }}
            </div>
            <div class="mb-4">
                <strong>High Court Case Number:</strong> {{ $courtCase->high_court_case_number ?? 'N/A' }}
            </div>
            <div class="mb-4">
                <strong>Court Outcome:</strong> {{ ucfirst($courtCase->court_outcome) ?? 'N/A' }}
            </div>
            <div class="mb-4">
                <strong>Court Outcome Details:</strong>
                <p>{{ $courtCase->court_outcome_details ?? 'N/A' }}</p>
            </div>
            <div class="mb-4">
                <strong>Court Outcome Date:</strong> 
                {{ $courtCase->court_outcome_date ? \Carbon\Carbon::parse($courtCase->court_outcome_date)->format('M d, Y') : 'N/A' }}
            </div>
            <div class="mb-4">
                <strong>Judgment Delivered Date:</strong> 
                {{ $courtCase->judgment_delivered_date ? \Carbon\Carbon::parse($courtCase->judgment_delivered_date)->format('M d, Y') : 'N/A' }}
            </div>
            <div class="mb-4">
                <strong>Verdict:</strong> {{ ucfirst($courtCase->verdict) ?? 'N/A' }}
            </div>
            <div class="mb-4">
                <strong>Decision Principle Established:</strong>
                <p>{{ $courtCase->decision_principle_established ?? 'N/A' }}</p>
            </div>
            <div class="mb-4">
                <strong>Created By:</strong> {{ $courtCase->createdBy->name ?? 'N/A' }}
            </div>
            <div class="mb-4">
                <strong>Updated By:</strong> {{ $courtCase->updatedBy->name ?? 'N/A' }}
            </div>
            <div class="mb-4">
                <strong>Created At:</strong> {{ \Carbon\Carbon::parse($courtCase->created_at)->format('M d, Y h:i A') }}
            </div>
            <div class="mb-4">
                <strong>Updated At:</strong> {{ $courtCase->updated_at ? \Carbon\Carbon::parse($courtCase->updated_at)->format('M d, Y h:i A') : 'N/A' }}
            </div>
            <div class="mb-4">
                <strong>Deleted At:</strong> {{ $courtCase->deleted_at ? \Carbon\Carbon::parse($courtCase->deleted_at)->format('M d, Y') : 'N/A' }}
            </div>
        </div>

        <!-- Actions -->
        <div class="card-footer text-center">
            <a href="{{ route('crime.court-hearings.index') }}" class="btn btn-primary">Back to Court Hearings</a>
            <a href="{{ route('crime.court-hearings.edit', $courtCase->id) }}" class="btn btn-warning">Edit</a>
        </div>
    </div>
</div>
@endsection
