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

        <!-- Reason for Closure / Return -->
        <div class="form-group" id="reason_for_closure_container" style="display: none;">
            <label for="reason_for_closure_id" class="text-white" id="reason_for_closure_label">Reason for Closure</label>
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

        <!-- Closure Decision (insufficient evidence only) -->
        <div class="form-group" id="closure_decision_container" style="display: none;">
            <label for="closure_decision" class="text-white">Closure Decision</label>
            <select class="form-control @error('closure_decision') is-invalid @enderror" id="closure_decision" name="closure_decision">
                <option value="">Select a decision</option>
                <option value="nfa" {{ old('closure_decision') == 'nfa' ? 'selected' : '' }}>No Further Action (NFA)</option>
                <option value="nolle_prosequi" {{ old('closure_decision') == 'nolle_prosequi' ? 'selected' : '' }}>Nolle Prosequi</option>
            </select>
            @error('closure_decision')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Dynamic Offences Group (Moved above Review Notes) -->
        <div id="offenceGroupsSection" style="display: none;">
            <label class="text-white">Offences Charged</label>
            <div id="offenceGroupsContainer">
                <div class="offence-group row mb-3">
                    <div class="form-group col-md-4">
                        <input type="text" class="form-control" name="offences[0][offence_name]" placeholder="Offence">
                    </div>

                    <div class="form-group col-md-4">
                        <select class="form-control" name="offences[0][category_id]">
                            <option value="">Select a category</option>
                            @foreach($categories as $id => $category)
                                <option value="{{ $id }}">{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-3 d-flex align-items-center">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="dv_0" name="offences[0][domestic_violence]" value="1">
                            <label class="form-check-label text-white" for="dv_0">Domestic Violence</label>
                        </div>
                    </div>

                    <div class="form-group col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm remove-offence-group">&times;</button>
                    </div>
                </div>
            </div>

            @error('offences')
                <div class="alert alert-danger py-2">{{ $message }}</div>
            @enderror

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
        const reasonLabel = document.getElementById('reason_for_closure_label');
        const closureDecisionContainer = document.getElementById('closure_decision_container');
        const dateFileClosed = document.getElementById('date_file_closed');
        const offenceSection = document.getElementById('offenceGroupsSection');

        function updateFields() {
            const status = evidenceStatus.value;

            reasonContainer.style.display = 'none';
            closureDecisionContainer.style.display = 'none';
            offenceSection.style.display = 'none';
            dateFileClosed.value = '';

            if (status === 'insufficient_evidence' || status === 'returned_to_police') {
                reasonContainer.style.display = 'block';
                reasonLabel.innerText = status === 'insufficient_evidence' ? 'Reason for Closure' : 'Reason for Return';
            }

            // Only an actual closure (insufficient evidence) stamps a closure
            // date or asks for a Closure Decision — "returned_to_police" sends
            // the file back to the lawyer instead of closing the case.
            if (status === 'insufficient_evidence') {
                closureDecisionContainer.style.display = 'block';
                dateFileClosed.value = new Date().toISOString().slice(0, 16);
            }

            if (status === 'sufficient_evidence') {
                offenceSection.style.display = 'block';
            }
        }

        updateFields();
        evidenceStatus.addEventListener('change', updateFields);

        // Dynamic offence groups logic. Each row is keyed by an
        // ever-incrementing index (never reused, even after a row is
        // removed) so offence_name/category_id/domestic_violence stay
        // correlated per row once submitted — an unchecked checkbox simply
        // omits itself from the request instead of shifting later rows out
        // of alignment the way a flat offence_id[]/category_id[] pair would.
        const container = document.getElementById('offenceGroupsContainer');
        let nextOffenceRowIndex = 1;

        document.getElementById('addOffenceGroup').addEventListener('click', function () {
            const original = container.querySelector('.offence-group');
            const clone = original.cloneNode(true);
            const index = nextOffenceRowIndex++;

            clone.querySelectorAll('select, input[type="text"]').forEach(el => el.value = '');
            clone.querySelectorAll('input[type="checkbox"]').forEach(el => el.checked = false);

            clone.querySelectorAll('[name]').forEach(el => {
                el.name = el.name.replace(/offences\[\d+\]/, `offences[${index}]`);
            });
            clone.querySelectorAll('[id]').forEach(el => {
                el.id = el.id.replace(/_\d+$/, `_${index}`);
            });
            clone.querySelectorAll('label[for]').forEach(el => {
                el.htmlFor = el.htmlFor.replace(/_\d+$/, `_${index}`);
            });

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
