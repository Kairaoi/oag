@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Court Case</h2>

    <form action="{{ route('crime.court-cases.update', $courtCase->id) }}" method="POST">
        @csrf
        @method('PUT')

        @php
            $isAppealed = old('is_appealed', $courtCase->is_appealed === null ? '' : ($courtCase->is_appealed ? '1' : '0'));
        @endphp

        <!-- Case Selection -->
        <div class="mb-3">
            <label for="case_id" class="form-label">Case</label>
            <select name="case_id" id="case_id" class="form-control" required>
                <option value="">Select Case</option>
                @foreach($cases as $id => $case)
                    <option value="{{ $id }}" {{ $courtCase->case_id == $id ? 'selected' : '' }}>{{ $case }}</option>
                @endforeach
            </select>
        </div>

        <!-- Charge File Date -->
        <div class="mb-3">
            <label for="charge_file_dated" class="form-label">Charge File Dated</label>
            <input type="date" name="charge_file_dated" id="charge_file_dated" class="form-control" value="{{ $courtCase->charge_file_dated }}" required>
        </div>

        <!-- High Court Case Number -->
        <div class="mb-3">
            <label for="high_court_case_number" class="form-label">High Court Case Number</label>
            <input type="text" name="high_court_case_number" id="high_court_case_number" class="form-control" value="{{ $courtCase->high_court_case_number }}">
        </div>

        <!-- Magistrate Court Case Number -->
        <div class="mb-3">
            <label for="magistrate_court_case_number" class="form-label">Magistrate Court Case Number</label>
            <input type="text" name="magistrate_court_case_number" id="magistrate_court_case_number" class="form-control" value="{{ $courtCase->magistrate_court_case_number }}">
        </div>

        <!-- Court Outcome -->
        <div class="mb-3">
            <label for="court_outcome" class="form-label">Court Outcome</label>
            <select name="court_outcome" id="court_outcome" class="form-control">
                <option value="">Select Outcome</option>
                @foreach(['guilty', 'not_guilty', 'dismissed', 'withdrawn', 'other'] as $outcome)
                    <option value="{{ $outcome }}" {{ $courtCase->court_outcome == $outcome ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $outcome)) }}</option>
                @endforeach
            </select>
        </div>

        <!-- Court Outcome Details -->
        <div class="mb-3">
            <label for="court_outcome_details" class="form-label">Court Outcome Details</label>
            <textarea name="court_outcome_details" id="court_outcome_details" class="form-control">{{ $courtCase->court_outcome_details }}</textarea>
        </div>

        <!-- Court Outcome Date -->
        <div class="mb-3">
            <label for="court_outcome_date" class="form-label">Court Outcome Date</label>
            <input type="date" name="court_outcome_date" id="court_outcome_date" class="form-control" value="{{ $courtCase->court_outcome_date }}">
        </div>

        <!-- Judgment Delivered Date -->
        <div class="mb-3">
            <label for="judgment_delivered_date" class="form-label">Judgment Delivered Date</label>
            <input type="date" name="judgment_delivered_date" id="judgment_delivered_date" class="form-control" value="{{ $courtCase->judgment_delivered_date }}">
        </div>

        <!-- Verdict -->
        <div class="mb-3">
            <label for="verdict" class="form-label">Verdict</label>
            <select name="verdict" id="verdict" class="form-control">
                <option value="">Select Verdict</option>
                <option value="win" {{ $courtCase->verdict == 'win' ? 'selected' : '' }}>Win</option>
                <option value="lose" {{ $courtCase->verdict == 'lose' ? 'selected' : '' }}>Lose</option>
            </select>
        </div>

        <!-- Decision Principle Established -->
        <div class="mb-3">
            <label for="decision_principle_established" class="form-label">Decision Principle Established</label>
            <textarea name="decision_principle_established" id="decision_principle_established" class="form-control">{{ $courtCase->decision_principle_established }}</textarea>
        </div>

        <!-- Appeal Status -->
        <div class="mb-3">
            <label class="form-label d-block">Appeal Status</label>
            <div class="form-check form-check-inline">
                <input type="checkbox" class="form-check-input" id="appealed_checkbox" {{ $isAppealed === '1' ? 'checked' : '' }}>
                <label class="form-check-label" for="appealed_checkbox">Appealed</label>
            </div>
            <div class="form-check form-check-inline">
                <input type="checkbox" class="form-check-input" id="not_appealed_checkbox" {{ $isAppealed === '0' ? 'checked' : '' }}>
                <label class="form-check-label" for="not_appealed_checkbox">Not Appealed</label>
            </div>
            <input type="hidden" name="is_appealed" id="is_appealed" value="{{ $isAppealed }}">
        </div>

        <!-- Updated By (hidden) -->
        <input type="hidden" name="updated_by" value="{{ auth()->id() }}">

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>

<script>
    (function () {
        const appealedCheckbox = document.getElementById('appealed_checkbox');
        const notAppealedCheckbox = document.getElementById('not_appealed_checkbox');
        const isAppealedInput = document.getElementById('is_appealed');

        appealedCheckbox.addEventListener('change', function () {
            if (this.checked) {
                notAppealedCheckbox.checked = false;
                isAppealedInput.value = '1';
            } else {
                isAppealedInput.value = '';
            }
        });

        notAppealedCheckbox.addEventListener('change', function () {
            if (this.checked) {
                appealedCheckbox.checked = false;
                isAppealedInput.value = '0';
            } else {
                isAppealedInput.value = '';
            }
        });
    })();
</script>
@endsection
