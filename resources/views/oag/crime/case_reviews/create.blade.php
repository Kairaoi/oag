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

        <!-- Reason for Closure -->
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

        <!-- Dynamic Offences Group (Moved above Review Notes) -->
        <div id="offenceGroupsSection" style="display: none;">
            <label class="text-white">Offences</label>
            <div id="offenceGroupsContainer">
                <div class="offence-group row mb-3">
                    <div class="form-group col-md-4">
                        <select class="form-control" name="offence_id[]">
                            <option value="">Select an offence</option>
                            @foreach($offences as $id => $offence)
                                <option value="{{ $id }}">{{ $offence }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-4">
                        <select class="form-control" name="category_id[]">
                            <option value="">Select a category</option>
                            @foreach($categories as $id => $category)
                                <option value="{{ $id }}">{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm remove-offence-group">&times;</button>
                    </div>
                </div>
            </div>

            <div class="text-right mb-4">
                <button type="button" class="btn btn-light btn-sm" id="addOffenceGroup">+ Add Offence</button>
            </div>
        </div>

         <!-- Offence Particulars -->
         <div class="form-group">
            <label for="offence_particulars" class="text-white">Offence Particulars</label>
            <textarea class="form-control @error('offence_particulars') is-invalid @enderror" id="offence_particulars" name="offence_particulars" rows="3" required>{{ old('offence_particulars') }}</textarea>
            @error('offence_particulars')
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

        <!-- Hidden Fields -->
        <input type="hidden" id="date_file_closed" name="date_file_closed" value="">
        <input type="hidden" name="created_by" value="{{ auth()->id() }}">
        <input type="hidden" name="updated_by" value="{{ auth()->id() }}">

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary btn-block">Create Review</button>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const evidenceStatus = document.getElementById('evidence_status');
        const reasonContainer = document.getElementById('reason_for_closure_container');
        const dateFileClosed = document.getElementById('date_file_closed');
        const offenceSection = document.getElementById('offenceGroupsSection');

        function updateFields() {
            const status = evidenceStatus.value;

            reasonContainer.style.display = 'none';
            offenceSection.style.display = 'none';
            dateFileClosed.value = '';

            if (status === 'insufficient_evidence' || status === 'returned_to_police') {
                reasonContainer.style.display = 'block';
                dateFileClosed.value = new Date().toISOString().slice(0, 16);
            }

            if (status === 'sufficient_evidence') {
                offenceSection.style.display = 'block';
            }
        }

        updateFields();
        evidenceStatus.addEventListener('change', updateFields);

        // Dynamic offence groups logic
        const container = document.getElementById('offenceGroupsContainer');
        document.getElementById('addOffenceGroup').addEventListener('click', function () {
            const original = container.querySelector('.offence-group');
            const clone = original.cloneNode(true);

            clone.querySelectorAll('select, input').forEach(el => el.value = '');
            container.appendChild(clone);
        });

        container.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-offence-group')) {
                const groups = container.querySelectorAll('.offence-group');
                if (groups.length > 1) {
                    e.target.closest('.offence-group').remove();
                }
            }
        });
    });
</script>
@endpush

@endsection
