@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <nav aria-label="breadcrumb">
        {{ Breadcrumbs::render() }}
    </nav>

    <h1 class="text-center mb-4" style="font-family: 'Courier New', monospace;">Edit Appeal Case</h1>

    <form action="{{ route('crime.appeal.update', $appeal->id) }}" method="POST" class="p-4 shadow rounded" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); border-radius: 20px;">
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
            <label for="appeal_filing_date" class="text-white">Appeal Filing Date</label>
            <input type="date" name="appeal_filing_date" id="appeal_filing_date" class="form-control @error('appeal_filing_date') is-invalid @enderror" value="{{ old('appeal_filing_date', $appeal->appeal_filing_date ? \Carbon\Carbon::parse($appeal->appeal_filing_date)->format('Y-m-d') : '') }}">
            @error('appeal_filing_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="filing_date_source" class="text-white">Select Date Source</label>
            <select name="filing_date_source" id="filing_date_source" class="form-control @error('filing_date_source') is-invalid @enderror">
                <option value="court" {{ old('filing_date_source', $appeal->filing_date_source) == 'court' ? 'selected' : '' }}>Date from Court</option>
                <option value="defendant" {{ old('filing_date_source', $appeal->filing_date_source) == 'defendant' ? 'selected' : '' }}>Date from Defendant</option>
            </select>
            @error('filing_date_source')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="appeal_status" class="text-white">Appeal Status</label>
            <select name="appeal_status" id="appeal_status" class="form-control @error('appeal_status') is-invalid @enderror" required>
                <option value="">-- Select Status --</option>
                @foreach(['pending', 'in_progress', 'decided', 'withdrawn'] as $status)
                    <option value="{{ $status }}" {{ old('appeal_status', $appeal->appeal_status) == $status ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                    </option>
                @endforeach
            </select>
            @error('appeal_status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="appeal_grounds" class="text-white">Appeal Grounds</label>
            <textarea name="appeal_grounds" id="appeal_grounds" class="form-control @error('appeal_grounds') is-invalid @enderror" rows="3">{{ old('appeal_grounds', $appeal->appeal_grounds) }}</textarea>
            @error('appeal_grounds')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="appeal_decision" class="text-white">Appeal Decision</label>
            <textarea name="appeal_decision" id="appeal_decision" class="form-control @error('appeal_decision') is-invalid @enderror" rows="3">{{ old('appeal_decision', $appeal->appeal_decision) }}</textarea>
            @error('appeal_decision')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="appeal_decision_date" class="text-white">Appeal Decision Date</label>
            <input type="date" name="appeal_decision_date" id="appeal_decision_date" class="form-control @error('appeal_decision_date') is-invalid @enderror" value="{{ old('appeal_decision_date', $appeal->appeal_decision_date ? \Carbon\Carbon::parse($appeal->appeal_decision_date)->format('Y-m-d') : '') }}">
            @error('appeal_decision_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row mt-4">
            <div class="col-md-6 mb-2">
                <a href="{{ route('crime.appeal.index') }}" class="btn btn-light btn-lg btn-block rounded-pill font-weight-bold">Cancel</a>
            </div>
            <div class="col-md-6">
                <button type="submit" class="btn btn-light btn-lg btn-block rounded-pill font-weight-bold">Update Appeal</button>
            </div>
        </div>
    </form>
</div>
@endsection
