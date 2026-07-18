@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        {{ Breadcrumbs::render() }}
    </nav>

    <!-- Title -->
    <h1 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #333; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);">
        Submit Case to the AG
    </h1>

    <!-- Info Card -->
    <div class="card bg-light mb-4">
        <div class="card-body">
            <h5 class="card-title">AG Review</h5>
            <p class="card-text mb-0">Before any case is filed in court, the completed legal work is submitted to the Attorney General for final review and approval.</p>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('crime.ag-reviews.store') }}" method="POST" class="p-4 shadow-lg rounded" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); border-radius: 20px;">
        @csrf

        <!-- Case (read-only) -->
        <div class="form-group">
            <label class="text-white">Case</label>
            <input type="text" class="form-control" value="{{ $case->case_name }}" disabled>
            <input type="hidden" name="case_id" value="{{ $case->id }}">
        </div>

        <!-- Submitted At -->
        <div class="form-group">
            <label for="submitted_at" class="text-white">Submitted to AG</label>
            <input type="date" name="submitted_at" id="submitted_at"
                   class="form-control @error('submitted_at') is-invalid @enderror"
                   value="{{ old('submitted_at', now()->format('Y-m-d')) }}" required>
            @error('submitted_at')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- Submission Notes -->
        <div class="form-group">
            <label for="submission_notes" class="text-white">Notes for the AG</label>
            <textarea name="submission_notes" id="submission_notes"
                      class="form-control @error('submission_notes') is-invalid @enderror"
                      rows="3">{{ old('submission_notes') }}</textarea>
            @error('submission_notes')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- Submit and Cancel -->
        <div class="row mt-4">
            <div class="col-md-6 mb-2">
                <a href="{{ route('crime.criminalCase.index') }}" class="btn btn-light btn-lg btn-block rounded-pill font-weight-bold">Cancel</a>
            </div>
            <div class="col-md-6">
                <button type="submit" class="btn btn-light btn-lg btn-block rounded-pill font-weight-bold">Submit to AG</button>
            </div>
        </div>
    </form>
</div>
@endsection
