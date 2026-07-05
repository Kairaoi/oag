@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('crime.courtOfAppeal.index') }}">Court of Appeal</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>

    <h1 class="text-center mb-4 text-dark" style="font-family: 'Courier New', monospace; text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">
        Edit Court of Appeal Record
    </h1>

    <form action="{{ route('crime.courtOfAppeal.update', $appeal->id) }}" method="POST" class="p-4 shadow rounded" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); border-radius: 20px;">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="appeal_case_number" class="text-white">Appeal Case Number</label>
            <input type="text" name="appeal_case_number" id="appeal_case_number" class="form-control @error('appeal_case_number') is-invalid @enderror" value="{{ old('appeal_case_number', $appeal->appeal_case_number) }}">
            @error('appeal_case_number')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="filing_date_source" class="text-white">Filing Date Source</label>
            <select id="filing_date_source" name="filing_date_source" class="form-control @error('filing_date_source') is-invalid @enderror" required>
                <option value="">-- Select Source --</option>
                <option value="court" {{ old('filing_date_source', $appeal->filing_date_source) == 'court' ? 'selected' : '' }}>Date from Court</option>
                <option value="defendant" {{ old('filing_date_source', $appeal->filing_date_source) == 'defendant' ? 'selected' : '' }}>Date from Defendant</option>
            </select>
            @error('filing_date_source')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="appeal_filing_date" class="text-white">Filing Date</label>
            <input type="date" name="appeal_filing_date" id="appeal_filing_date" class="form-control @error('appeal_filing_date') is-invalid @enderror" value="{{ old('appeal_filing_date', $appeal->appeal_filing_date ? \Carbon\Carbon::parse($appeal->appeal_filing_date)->format('Y-m-d') : '') }}" required>
            @error('appeal_filing_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="judgment_delivered_date" class="text-white">Judgment Delivered Date</label>
            <input type="date" name="judgment_delivered_date" id="judgment_delivered_date" class="form-control @error('judgment_delivered_date') is-invalid @enderror" value="{{ old('judgment_delivered_date', $appeal->judgment_delivered_date ? \Carbon\Carbon::parse($appeal->judgment_delivered_date)->format('Y-m-d') : '') }}">
            @error('judgment_delivered_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="court_outcome" class="text-white">Court Outcome</label>
            <select name="court_outcome" id="court_outcome" class="form-control @error('court_outcome') is-invalid @enderror">
                <option value="">-- Select Outcome --</option>
                @foreach(['win', 'lose', 'remand'] as $outcome)
                    <option value="{{ $outcome }}" {{ old('court_outcome', $appeal->court_outcome) == $outcome ? 'selected' : '' }}>
                        {{ ucfirst($outcome) }}
                    </option>
                @endforeach
            </select>
            @error('court_outcome')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="decision_principle_established" class="text-white">Decision Principle Established</label>
            <textarea name="decision_principle_established" id="decision_principle_established" class="form-control @error('decision_principle_established') is-invalid @enderror" rows="3">{{ old('decision_principle_established', $appeal->decision_principle_established) }}</textarea>
            @error('decision_principle_established')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row mt-4">
            <div class="col-md-6 mb-2">
                <a href="{{ route('crime.courtOfAppeal.index') }}" class="btn btn-light btn-lg btn-block rounded-pill font-weight-bold">Cancel</a>
            </div>
            <div class="col-md-6">
                <button type="submit" class="btn btn-light btn-lg btn-block rounded-pill font-weight-bold">Update Record</button>
            </div>
        </div>
    </form>
</div>
@endsection
