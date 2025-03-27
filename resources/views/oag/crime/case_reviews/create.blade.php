@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create Case Review</li>
        </ol>
    </nav>
    
    <h1 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #333; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);">Create Case Review</h1>

    <form action="{{ route('crime.CaseReview.store') }}" method="POST" class="p-4 shadow-lg rounded" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); border-radius: 20px;">
        @csrf

        <!-- Case Selection -->
        <div class="form-group">
            <label for="case_id" class="text-white">Case</label>
            <select class="form-control @error('case_id') is-invalid @enderror" id="case_id" name="case_id" required>
                <option value="">Select a case</option>
                <option value="{{ $case->id }}" {{ old('case_id', $case->id) == $case->id ? 'selected' : '' }}>
                    {{ $case->case_name }}
                </option>
            </select>
            @error('case_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Action Type Selection -->
        <div class="form-group">
            <label for="action_type" class="text-white">Action Type</label>
            <select class="form-control" id="action_type" name="action_type" required>
                <option value="review">Review Case</option>
                <option value="reallocate">Reallocate Case</option>
                <option value="update_court_info">Update Court Information</option>
            </select>
        </div>

        <!-- Evidence Status -->
        <div class="form-group">
            <label for="evidence_status" class="text-white">Evidence Status</label>
            <select class="form-control @error('evidence_status') is-invalid @enderror" id="evidence_status" name="evidence_status" required>
                <option value="pending_review" {{ old('evidence_status') == 'pending_review' ? 'selected' : '' }}>Pending Review</option>
                <option value="sufficient_evidence" {{ old('evidence_status') == 'sufficient_evidence' ? 'selected' : '' }}>Sufficient Evidence</option>
                <option value="insufficient_evidence" {{ old('evidence_status') == 'insufficient_evidence' ? 'selected' : '' }}>Insufficient Evidence</option>
                <option value="returned_to_police" {{ old('evidence_status') == 'returned_to_police' ? 'selected' : '' }}>Returned to Police</option>
            </select>
            @error('evidence_status')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Conditionally displayed fields based on action type -->
        <div id="reallocation_container" style="display: none;">
            <div class="form-group">
                <label for="new_lawyer_id" class="text-white">Reallocate To</label>
                <select class="form-control" id="new_lawyer_id" name="new_lawyer_id">
                    <option value="">Select new lawyer</option>
                    @foreach($councils as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label for="reallocation_reason" class="text-white">Reason for Reallocation</label>
                <textarea class="form-control" id="reallocation_reason" name="reallocation_reason" rows="2"></textarea>
            </div>
        </div>

        <!-- Court Info -->
        <div class="form-group" id="court_info_container" style="display: none;">
            <label for="court_case_number" class="text-white">Court Case Number</label>
            <input type="text" class="form-control" id="court_case_number" name="court_case_number">
        </div>

        <!-- Reason for Closure (moved below Evidence Status) -->
        <div class="form-group" id="reason_for_closure_container" style="display: none;">
            <label for="reason_for_closure_id" class="text-white">Reason for Closure</label>
            <select class="form-control @error('reason_for_closure_id') is-invalid @enderror" id="reason_for_closure_id" name="reason_for_closure_id">
                <option value="">Select a reason</option>
                @foreach($reasonsForClosure as $id => $reason)
                    <option value="{{ $id }}" {{ old('reason_for_closure_id') == $id ? 'selected' : '' }}>{{ $reason }}</option>
                @endforeach
            </select>
            @error('reason_for_closure_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Review Notes -->
        <div class="form-group">
            <label for="review_notes" class="text-white">Review Notes</label>
            <textarea class="form-control @error('review_notes') is-invalid @enderror" id="review_notes" name="review_notes" rows="3" required>{{ old('review_notes') }}</textarea>
            @error('review_notes')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Review Date -->
        <div class="form-group">
            <label for="review_date" class="text-white">Review Date</label>
            <input type="datetime-local" class="form-control @error('review_date') is-invalid @enderror" id="review_date" name="review_date" value="{{ old('review_date') }}" required>
            @error('review_date')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Hidden Fields for Created By and Updated By -->
        <input type="hidden" name="created_by" value="{{ auth()->id() }}">
        <input type="hidden" name="updated_by" value="{{ auth()->id() }}">

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary btn-block">Create Review</button>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const actionTypeSelect = document.getElementById('action_type');
        const reallocationContainer = document.getElementById('reallocation_container');
        const courtInfoContainer = document.getElementById('court_info_container');
        const reasonForClosureContainer = document.getElementById('reason_for_closure_container');
        const evidenceStatusSelect = document.getElementById('evidence_status');
        
        // Function to toggle display of fields based on selected action and evidence status
        function toggleActionFields() {
            const action = actionTypeSelect.value;
            const evidenceStatus = evidenceStatusSelect.value;

            // Toggle Reallocation Fields
            if (action === 'reallocate') {
                reallocationContainer.style.display = 'block';
            } else {
                reallocationContainer.style.display = 'none';
            }

            // Toggle Court Info Fields
            if (action === 'update_court_info') {
                courtInfoContainer.style.display = 'block';
            } else {
                courtInfoContainer.style.display = 'none';
            }

            // Toggle Reason for Closure Fields based on Evidence Status
            if (evidenceStatus === 'insufficient_evidence' || evidenceStatus === 'returned_to_police') {
                reasonForClosureContainer.style.display = 'block';
            } else {
                reasonForClosureContainer.style.display = 'none';
            }
        }

        // Initial call to function to set field visibility on page load
        toggleActionFields();

        // Event listeners for dynamic field display
        actionTypeSelect.addEventListener('change', toggleActionFields);
        evidenceStatusSelect.addEventListener('change', toggleActionFields);
    });
</script>
@endpush

@endsection