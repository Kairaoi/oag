@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Create Court Case</h2>

    <form action="{{ route('crime.court-cases.store') }}" method="POST">
        @csrf

        <!-- Case Selection -->
        <div class="form-group">
            <label for="case_id" class="form-label">Case</label>
            <select class="form-control @error('case_id') is-invalid @enderror" id="case_id" name="case_id" required>
                <option value="">Select a case</option>
                <option value="{{ $case->id }}" {{ old('case_id', $case->id) == $case->id ? 'selected' : '' }}>
                    {{ $case->case_name }}
                </option>
            </select>
            @error('case_id')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- Charge File Date -->
        <div class="mb-3">
            <label for="charge_file_dated" class="form-label">Charge File Dated</label>
            <input type="date" name="charge_file_dated" id="charge_file_dated"
                   class="form-control @error('charge_file_dated') is-invalid @enderror"
                   value="{{ old('charge_file_dated') }}" required>
            @error('charge_file_dated')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- High Court Case Number -->
        <div class="mb-3">
            <label for="high_court_case_number" class="form-label">High Court Case Number</label>
            <input type="text" name="high_court_case_number" id="high_court_case_number"
                   class="form-control @error('high_court_case_number') is-invalid @enderror"
                   value="{{ old('high_court_case_number') }}">
            @error('high_court_case_number')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- Verdict -->
        <div class="mb-3">
            <label for="verdict" class="form-label">Verdict</label>
            <select name="verdict" id="verdict" class="form-control @error('verdict') is-invalid @enderror">
                <option value="">Select Verdict</option>
                @foreach(['guilty', 'not_guilty', 'dismissed', 'withdrawn', 'other'] as $verdict)
                    <option value="{{ $verdict }}" {{ old('verdict') == $verdict ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $verdict)) }}
                    </option>
                @endforeach
            </select>
            @error('verdict')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- Judgment Delivered Date -->
        <div class="mb-3">
            <label for="judgment_delivered_date" class="form-label">Judgment Delivered Date</label>
            <input type="date" name="judgment_delivered_date" id="judgment_delivered_date"
                   class="form-control @error('judgment_delivered_date') is-invalid @enderror"
                   value="{{ old('judgment_delivered_date') }}">
            @error('judgment_delivered_date')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- Court Outcome -->
        <div class="mb-3">
            <label for="court_outcome" class="form-label">Court Outcome</label>
            <select name="court_outcome" id="court_outcome" class="form-control @error('court_outcome') is-invalid @enderror">
                <option value="">Select Outcome</option>
                @foreach(['win', 'lose'] as $outcome)
                    <option value="{{ $outcome }}" {{ old('court_outcome') == $outcome ? 'selected' : '' }}>
                        {{ ucfirst($outcome) }}
                    </option>
                @endforeach
            </select>
            @error('court_outcome')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- Decision Principle Established -->
        <div class="mb-3">
            <label for="decision_principle_established" class="form-label">Decision Principle Established</label>
            <textarea name="decision_principle_established" id="decision_principle_established"
                      class="form-control @error('decision_principle_established') is-invalid @enderror"
                      rows="3">{{ old('decision_principle_established') }}</textarea>
            @error('decision_principle_established')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- Hidden Created By -->
        <input type="hidden" name="created_by" value="{{ auth()->id() }}">

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
@endsection
