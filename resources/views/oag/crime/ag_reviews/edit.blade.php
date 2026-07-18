@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        {{ Breadcrumbs::render() }}
    </nav>

    <!-- Title -->
    <h1 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #333; text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">
        Record AG Decision
    </h1>

    <!-- Case Summary -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white">Case Summary</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-2"><strong>Case Name:</strong> {{ $agReview->case->case_name ?? 'N/A' }}</div>
                <div class="col-md-6 mb-2"><strong>Police Case File Number:</strong> {{ $agReview->case->case_file_number ?? 'N/A' }}</div>
                <div class="col-md-6 mb-2"><strong>Island:</strong> {{ $agReview->case->island->island_name ?? 'N/A' }}</div>
                <div class="col-md-6 mb-2"><strong>Assigned Lawyer:</strong> {{ $agReview->case->lawyer->name ?? 'N/A' }}</div>
                <div class="col-md-6 mb-2">
                    <strong>Accused:</strong>
                    {{ $agReview->case->accused->map(fn($a) => "{$a->first_name} {$a->last_name}")->implode(', ') ?: 'None recorded' }}
                </div>
                <div class="col-md-6 mb-2">
                    <strong>Victims:</strong>
                    {{ $agReview->case->victims->map(fn($v) => "{$v->first_name} {$v->last_name}")->implode(', ') ?: 'None recorded' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Case Review (Evidence Sufficiency) Summary -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white">Case Review — Evidence Sufficiency</div>
        <div class="card-body">
            @if($caseReview)
                <div class="row">
                    <div class="col-md-6 mb-2"><strong>Evidence Status:</strong> {{ ucfirst(str_replace('_', ' ', $caseReview->evidence_status)) }}</div>
                    <div class="col-md-6 mb-2"><strong>Review Date:</strong> {{ $caseReview->review_date ? \Carbon\Carbon::parse($caseReview->review_date)->format('d M Y') : 'N/A' }}</div>
                    <div class="col-md-6 mb-2"><strong>Offences Charged:</strong> {{ $caseReview->offence_names ?: 'None recorded' }}</div>
                    <div class="col-md-6 mb-2"><strong>Offence Category:</strong> {{ $caseReview->category_names ?: 'N/A' }}</div>
                    <div class="col-12 mb-2"><strong>Offence Particulars:</strong> {{ $caseReview->offence_particulars ?: 'N/A' }}</div>
                </div>
            @else
                <p class="mb-0 text-muted">No case review found for this case.</p>
            @endif
        </div>
    </div>

    <!-- Lawyer's Submission Notes -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white">Lawyer's Submission</div>
        <div class="card-body">
            <p class="mb-1"><strong>Submitted to AG:</strong> {{ \Carbon\Carbon::parse($agReview->submitted_at)->format('d M Y') }} by {{ $agReview->submittedBy->name ?? 'N/A' }}</p>
            <p class="mb-0"><strong>Notes for the AG:</strong> {{ $agReview->submission_notes ?: 'None provided' }}</p>
        </div>
    </div>

    <!-- Prior AG Submissions (resubmission history) -->
    @if($priorSubmissions->isNotEmpty())
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">Previous AG Review Rounds</div>
            <div class="card-body">
                @foreach($priorSubmissions as $prior)
                    <div class="mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <p class="mb-1">
                            <strong>Submitted:</strong> {{ \Carbon\Carbon::parse($prior->submitted_at)->format('d M Y') }}
                            &mdash;
                            <strong>Decision:</strong>
                            <span class="badge {{ $prior->ag_decision === 'approved' ? 'bg-success' : ($prior->ag_decision === 'rejected' ? 'bg-danger' : 'bg-warning') }}">
                                {{ ucfirst($prior->ag_decision) }}
                            </span>
                            @if($prior->decision_date)
                                on {{ \Carbon\Carbon::parse($prior->decision_date)->format('d M Y') }}
                            @endif
                        </p>
                        @if($prior->submission_notes)
                            <p class="mb-1"><strong>Lawyer's notes:</strong> {{ $prior->submission_notes }}</p>
                        @endif
                        @if($prior->ag_comments)
                            <p class="mb-0"><strong>AG comments:</strong> {{ $prior->ag_comments }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Decision Form -->
    <form action="{{ route('crime.ag-reviews.update', $agReview->id) }}" method="POST" class="p-4 shadow-lg rounded" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); border-radius: 20px;">
        @csrf
        @method('PUT')

        <!-- AG Decision -->
        <div class="form-group">
            <label for="ag_decision" class="text-white">AG Decision</label>
            <select name="ag_decision" id="ag_decision" class="form-control @error('ag_decision') is-invalid @enderror" required>
                <option value="">-- Select Decision --</option>
                <option value="approved" {{ old('ag_decision', $agReview->ag_decision) == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ old('ag_decision', $agReview->ag_decision) == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            @error('ag_decision')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- Decision Date -->
        <div class="form-group">
            <label for="decision_date" class="text-white">Decision Date</label>
            <input type="date" name="decision_date" id="decision_date"
                   class="form-control @error('decision_date') is-invalid @enderror"
                   value="{{ old('decision_date', $agReview->decision_date ? \Carbon\Carbon::parse($agReview->decision_date)->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
            @error('decision_date')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- AG Comments -->
        <div class="form-group">
            <label for="ag_comments" class="text-white">AG Comments <span class="text-white-50">(revision instructions if rejected)</span></label>
            <textarea name="ag_comments" id="ag_comments"
                      class="form-control @error('ag_comments') is-invalid @enderror"
                      rows="4">{{ old('ag_comments', $agReview->ag_comments) }}</textarea>
            @error('ag_comments')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <div class="row mt-4">
            <div class="col-md-6 mb-2">
                <a href="{{ route('crime.ag-reviews.index') }}" class="btn btn-light btn-lg btn-block rounded-pill font-weight-bold">Cancel</a>
            </div>
            <div class="col-md-6">
                <button type="submit" class="btn btn-light btn-lg btn-block rounded-pill font-weight-bold">Save Decision</button>
            </div>
        </div>
    </form>
</div>
@endsection
