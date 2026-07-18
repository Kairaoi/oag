@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <nav aria-label="breadcrumb">
        {{ Breadcrumbs::render() }}
    </nav>

    <h2 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #f8f9fa; text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.4); border-bottom: 4px double #ff6f61; padding-bottom: 20px;">
        AG Review Details
    </h2>

    <div class="card shadow-lg rounded-3">
        <div class="card-body">
            <div class="mb-3">
                <strong>Case Name:</strong> {{ $agReview->case->case_name ?? 'N/A' }}
            </div>
            <div class="mb-3">
                <strong>Submitted At:</strong> {{ \Carbon\Carbon::parse($agReview->submitted_at)->format('d M Y') }}
            </div>
            <div class="mb-3">
                <strong>Submitted By:</strong> {{ $agReview->submittedBy->name ?? 'N/A' }}
            </div>
            <div class="mb-3">
                <strong>Notes for the AG:</strong>
                <p>{{ $agReview->submission_notes ?? 'N/A' }}</p>
            </div>
            <div class="mb-3">
                <strong>AG Decision:</strong> {{ ucfirst($agReview->ag_decision) }}
            </div>
            <div class="mb-3">
                <strong>Decision Date:</strong> {{ $agReview->decision_date ? \Carbon\Carbon::parse($agReview->decision_date)->format('d M Y') : 'N/A' }}
            </div>
            <div class="mb-3">
                <strong>AG Comments:</strong>
                <p>{{ $agReview->ag_comments ?? 'N/A' }}</p>
            </div>
        </div>

        <div class="card-footer text-center">
            <a href="{{ route('crime.ag-reviews.index') }}" class="btn btn-primary">Back to AG Reviews</a>
            <a href="{{ route('crime.ag-reviews.edit', $agReview->id) }}" class="btn btn-warning">Edit</a>
        </div>
    </div>
</div>
@endsection
