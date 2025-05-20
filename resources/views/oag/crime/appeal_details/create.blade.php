@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent p-0 mb-4">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-danger font-weight-bold">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('crime.criminalCase.index') }}" class="text-danger font-weight-bold">Criminal Cases</a></li>
            <li class="breadcrumb-item active font-weight-bold text-dark" aria-current="page">Create Appeal Case</li>
        </ol>
    </nav>

    <!-- Title -->
    <h1 class="text-center mb-4 text-dark" style="font-family: 'Courier New', monospace; text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">
        Create Appeal Case
    </h1>

    <!-- Info Card -->
    <div class="card bg-light mb-4">
        <div class="card-body">
            <h5 class="card-title">Filing an Appeal</h5>
            <p class="card-text mb-0">This form links a new appeal to an existing criminal case.</p>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('crime.appeal.store') }}" method="POST" class="p-4 shadow rounded" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); border-radius: 20px;">
        @csrf

        <!-- Case Selection -->
        <div class="form-group">
            <label for="case_id" class="text-white">Original Case</label>
            <select id="case_id" name="case_id" class="form-control @error('case_id') is-invalid @enderror" required>
                <option value="">-- Select Case --</option>
                @foreach($cases as $id => $name)
                    <option value="{{ $id }}" {{ old('case_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
            @error('case_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Appeal Case Number -->
        <div class="form-group">
            <label for="appeal_case_number" class="text-white">Appeal Case Number</label>
            <input type="text" name="appeal_case_number" id="appeal_case_number" class="form-control @error('appeal_case_number') is-invalid @enderror" value="{{ old('appeal_case_number') }}" required>
            @error('appeal_case_number')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Appeal Filing Date -->
        <div class="form-group">
            <label for="appeal_filing_date" class="text-white">Date Appeal Filed</label>
            <input type="date" name="appeal_filing_date" id="appeal_filing_date" class="form-control @error('appeal_filing_date') is-invalid @enderror" value="{{ old('appeal_filing_date', date('Y-m-d')) }}" required>
            @error('appeal_filing_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Verdict (Matches Schema Enum) -->
        <div class="form-group">
            <label for="verdict" class="text-white">Verdict</label>
            <select name="verdict" id="verdict" class="form-control @error('verdict') is-invalid @enderror">
                <option value="">-- Select Verdict --</option>
                @foreach(['guilty', 'not_guilty', 'dismissed', 'withdrawn', 'other'] as $verdict)
                    <option value="{{ $verdict }}" {{ old('verdict') == $verdict ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $verdict)) }}
                    </option>
                @endforeach
            </select>
            @error('verdict')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Judgment Delivered Date -->
        <div class="form-group">
            <label for="judgment_delivered_date" class="text-white">Judgment Delivered Date</label>
            <input type="date" name="judgment_delivered_date" id="judgment_delivered_date" class="form-control @error('judgment_delivered_date') is-invalid @enderror" value="{{ old('judgment_delivered_date') }}">
            @error('judgment_delivered_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Court Outcome (Matches Schema Enum) -->
        <div class="form-group">
            <label for="court_outcome" class="text-white">Court Outcome</label>
            <select name="court_outcome" id="court_outcome" class="form-control @error('court_outcome') is-invalid @enderror">
                <option value="">-- Select Outcome --</option>
                @foreach(['win', 'lose'] as $outcome)
                    <option value="{{ $outcome }}" {{ old('court_outcome') == $outcome ? 'selected' : '' }}>
                        {{ ucfirst($outcome) }}
                    </option>
                @endforeach
            </select>
            @error('court_outcome')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Decision Principle -->
        <div class="form-group">
            <label for="decision_principle_established" class="text-white">Decision Principle Established</label>
            <textarea name="decision_principle_established" id="decision_principle_established" class="form-control @error('decision_principle_established') is-invalid @enderror" rows="3">{{ old('decision_principle_established') }}</textarea>
            @error('decision_principle_established')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Submit -->
        <div class="row mt-4">
            <div class="col-md-6 mb-2">
                <a href="{{ route('crime.criminalCase.index') }}" class="btn btn-light btn-lg btn-block rounded-pill font-weight-bold">Cancel</a>
            </div>
            <div class="col-md-6">
                <button type="submit" class="btn btn-light btn-lg btn-block rounded-pill font-weight-bold">Submit Appeal</button>
            </div>
        </div>
    </form>
</div>
@endsection
