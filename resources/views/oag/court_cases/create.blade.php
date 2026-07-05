@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        {{ Breadcrumbs::render() }}
    </nav>

    <!-- Title -->
    <h1 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #333; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);">
        Create Court Case
    </h1>

    <!-- Info Card -->
    <div class="card bg-light mb-4">
        <div class="card-body">
            <h5 class="card-title">High Court Proceedings</h5>
            <p class="card-text mb-0">This form records the High Court stage of an existing criminal case.</p>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('crime.court-cases.store') }}" method="POST" class="p-4 shadow-lg rounded" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); border-radius: 20px;">
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
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- Charge File Date -->
        <div class="form-group">
            <label for="charge_file_dated" class="text-white">Charge File Dated</label>
            <input type="date" name="charge_file_dated" id="charge_file_dated"
                   class="form-control @error('charge_file_dated') is-invalid @enderror"
                   value="{{ old('charge_file_dated') }}" required>
            @error('charge_file_dated')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- High Court Case Number -->
        <div class="form-group">
            <label for="high_court_case_number" class="text-white">High Court Case Number</label>
            <input type="text" name="high_court_case_number" id="high_court_case_number"
                   class="form-control @error('high_court_case_number') is-invalid @enderror"
                   value="{{ old('high_court_case_number') }}">
            @error('high_court_case_number')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- Verdict -->
        <div class="form-group">
            <label for="verdict" class="text-white">Verdict</label>
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
        <div class="form-group">
            <label for="judgment_delivered_date" class="text-white">Judgment Delivered Date</label>
            <input type="date" name="judgment_delivered_date" id="judgment_delivered_date"
                   class="form-control @error('judgment_delivered_date') is-invalid @enderror"
                   value="{{ old('judgment_delivered_date') }}">
            @error('judgment_delivered_date')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- Court Outcome -->
        <div class="form-group">
            <label for="court_outcome" class="text-white">Court Outcome</label>
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
        <div class="form-group">
            <label for="decision_principle_established" class="text-white">Decision Principle Established</label>
            <textarea name="decision_principle_established" id="decision_principle_established"
                      class="form-control @error('decision_principle_established') is-invalid @enderror"
                      rows="3">{{ old('decision_principle_established') }}</textarea>
            @error('decision_principle_established')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- Hidden Created By -->
        <input type="hidden" name="created_by" value="{{ auth()->id() }}">

        <!-- Submit and Cancel -->
        <div class="row mt-4">
            <div class="col-md-6 mb-2">
                <a href="{{ route('crime.criminalCase.index') }}" class="btn btn-light btn-lg btn-block rounded-pill font-weight-bold">Cancel</a>
            </div>
            <div class="col-md-6">
                <button type="submit" class="btn btn-light btn-lg btn-block rounded-pill font-weight-bold">Submit</button>
            </div>
        </div>
    </form>
</div>
@endsection
