@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Create Court Case</h2>

    <form action="{{ route('crime.court-cases.store') }}" method="POST">
        @csrf

        <!-- Case Selection -->
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


        <!-- Charge File Date -->
        <div class="mb-3">
            <label for="charge_file_dated" class="form-label">Charge File Dated</label>
            <input type="date" name="charge_file_dated" id="charge_file_dated" class="form-control" required>
        </div>

        <!-- High Court Case Number -->
        <div class="mb-3">
            <label for="high_court_case_number" class="form-label">High Court Case Number</label>
            <input type="text" name="high_court_case_number" id="high_court_case_number" class="form-control">
        </div>

        <!-- Court Outcome -->
        <div class="mb-3">
            <label for="court_outcome" class="form-label">Court Outcome</label>
            <select name="court_outcome" id="court_outcome" class="form-control">
                <option value="">Select Outcome</option>
                <option value="guilty">Guilty</option>
                <option value="not_guilty">Not Guilty</option>
                <option value="dismissed">Dismissed</option>
                <option value="withdrawn">Withdrawn</option>
                <option value="other">Other</option>
            </select>
        </div>

        <!-- Court Outcome Details -->
        <div class="mb-3">
            <label for="court_outcome_details" class="form-label">Court Outcome Details</label>
            <textarea name="court_outcome_details" id="court_outcome_details" class="form-control"></textarea>
        </div>

        <!-- Court Outcome Date -->
        <div class="mb-3">
            <label for="court_outcome_date" class="form-label">Court Outcome Date</label>
            <input type="date" name="court_outcome_date" id="court_outcome_date" class="form-control">
        </div>

        <!-- Judgment Delivered Date -->
        <div class="mb-3">
            <label for="judgment_delivered_date" class="form-label">Judgment Delivered Date</label>
            <input type="date" name="judgment_delivered_date" id="judgment_delivered_date" class="form-control">
        </div>

        <!-- Verdict -->
        <div class="mb-3">
            <label for="verdict" class="form-label">Verdict</label>
            <select name="verdict" id="verdict" class="form-control">
                <option value="">Select Verdict</option>
                <option value="win">Win</option>
                <option value="lose">Lose</option>
            </select>
        </div>

        <!-- Decision Principle Established -->
        <div class="mb-3">
            <label for="decision_principle_established" class="form-label">Decision Principle Established</label>
            <textarea name="decision_principle_established" id="decision_principle_established" class="form-control"></textarea>
        </div>

        <!-- Hidden Created By -->
        <input type="hidden" name="created_by" value="{{ auth()->id() }}">

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
@endsection
