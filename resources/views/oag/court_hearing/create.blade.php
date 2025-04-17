@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Create Court Hearing</h2>
    
    <form action="{{ route('crime.court-hearings.store') }}" method="POST">
        @csrf

        <!-- Case Selection -->
        <div class="mb-3">
            <label for="case_id" class="form-label">Case</label>
            <select name="case_id" id="case_id" class="form-control" required>
                <option value="">Select Case</option>
                @foreach($cases as $id => $case)
                    <option value="{{ $id }}">{{ $case }}</option>
                @endforeach
            </select>
        </div>

        <!-- Hearing Date -->
        <div class="mb-3">
            <label for="hearing_date" class="form-label">Hearing Date</label>
            <input type="date" name="hearing_date" id="hearing_date" class="form-control" required>
        </div>

        <!-- Hearing Type -->
        <div class="mb-3">
            <label for="hearing_type" class="form-label">Hearing Type</label>
            <input type="text" name="hearing_type" id="hearing_type" class="form-control" required>
        </div>

        <!-- Hearing Notes -->
        <div class="mb-3">
            <label for="hearing_notes" class="form-label">Hearing Notes</label>
            <textarea name="hearing_notes" id="hearing_notes" class="form-control"></textarea>
        </div>

        <!-- Is Completed -->
        <div class="mb-3">
            <label for="is_completed" class="form-label">Is Completed?</label>
            <select name="is_completed" id="is_completed" class="form-control">
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
        </div>

        <!-- Has Verdict -->
        <div class="mb-3">
            <label for="has_verdict" class="form-label">Has Verdict?</label>
            <select name="has_verdict" id="has_verdict" class="form-control">
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
        </div>

        <!-- Verdict -->
        <div class="mb-3">
            <label for="verdict" class="form-label">Verdict</label>
            <select name="verdict" id="verdict" class="form-control">
                <option value="">Select Verdict</option>
                <option value="guilty">Guilty</option>
                <option value="not_guilty">Not Guilty</option>
                <option value="dismissed">Dismissed</option>
                <option value="withdrawn">Withdrawn</option>
                <option value="other">Other</option>
            </select>
        </div>

        <!-- Verdict Details -->
        <div class="mb-3">
            <label for="verdict_details" class="form-label">Verdict Details</label>
            <textarea name="verdict_details" id="verdict_details" class="form-control"></textarea>
        </div>

        <!-- Verdict Date -->
        <div class="mb-3">
            <label for="verdict_date" class="form-label">Verdict Date</label>
            <input type="date" name="verdict_date" id="verdict_date" class="form-control">
        </div>

        <!-- Sentencing Details -->
        <div class="mb-3">
            <label for="sentencing_details" class="form-label">Sentencing Details</label>
            <textarea name="sentencing_details" id="sentencing_details" class="form-control"></textarea>
        </div>

        <!-- Created By (Hidden) -->
        <input type="hidden" name="created_by" value="{{ auth()->id() }}">

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
@endsection
