@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
    {{ Breadcrumbs::render() }}
    </nav>

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
            @if(count($cases) === 0)
                <div class="alert alert-warning mb-0">
                    No eligible cases found. Every case either already has an unresolved appeal in progress,
                    or is itself an appeal case. Resolve an existing appeal (mark it dismissed or withdrawn) to
                    make its case eligible again.
                </div>
            @else
                <select id="case_id" name="case_id" class="form-control @error('case_id') is-invalid @enderror" required>
                    <option value="">-- Select Case --</option>
                    @foreach($cases as $id => $name)
                        <option value="{{ $id }}" {{ old('case_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            @endif
            @error('case_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- High Court Case Number -->
        <div class="form-group">
            <label for="high_court_case_number" class="text-white">High Court Case Number</label>
            <input type="text" name="high_court_case_number" id="high_court_case_number" class="form-control @error('high_court_case_number') is-invalid @enderror" value="{{ old('high_court_case_number') }}">
            @error('high_court_case_number')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Magistrate Court Case Number -->
        <div class="form-group">
            <label for="magistrate_court_case_number" class="text-white">Magistrate Court Case Number</label>
            <input type="text" name="magistrate_court_case_number" id="magistrate_court_case_number" class="form-control @error('magistrate_court_case_number') is-invalid @enderror" value="{{ old('magistrate_court_case_number') }}">
            @error('magistrate_court_case_number')
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

       <!-- Date Source Dropdown -->
<div class="form-group">
    <label for="filing_date_type" class="text-white">Party Positions</label>
    <select id="filing_date_type" name="filing_date_type" class="form-control" onchange="toggleFilingDateInput()" required>
        <option value="">-- Select Source --</option>
        <option value="court" {{ old('filing_date_type') == 'court' ? 'selected' : '' }}>Appellant</option>
        <option value="defendant" {{ old('filing_date_type') == 'defendant' ? 'selected' : '' }}>Defendant</option>
    </select>
</div>

<!-- Dynamic Date Input -->
<div class="form-group" id="date_input_group" style="display: none;">
    <label for="filing_date_value" class="text-white" id="filing_date_label">Filing Date</label>
    <input type="date" name="filing_date_value" id="filing_date_value"
           class="form-control @error('filing_date_value') is-invalid @enderror"
           value="{{ old('filing_date_value') }}">
    @error('filing_date_value')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>


        <!-- Appeal Status (Matches Schema Enum) -->
        <div class="form-group">
            <label for="appeal_status" class="text-white">Appeal Status</label>
            <select name="appeal_status" id="appeal_status" class="form-control @error('appeal_status') is-invalid @enderror">
                <option value="">-- Select Status --</option>
                @foreach(['pending', 'appealed', 'dismissed', 'withdrawn'] as $status)
                    <option value="{{ $status }}" {{ old('appeal_status') == $status ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                    </option>
                @endforeach
            </select>
            @error('appeal_status')
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
@push('scripts')
<script>
    function toggleFilingDateInput() {
        const typeSelect = document.getElementById('filing_date_type');
        const selectedType = typeSelect.value;
        const dateGroup = document.getElementById('date_input_group');
        const dateLabel = document.getElementById('filing_date_label');

        if (selectedType === 'court') {
            dateLabel.innerText = 'Appellant';
            dateGroup.style.display = 'block';
        } else if (selectedType === 'defendant') {
            dateLabel.innerText = 'Defendant';
            dateGroup.style.display = 'block';
        } else {
            dateGroup.style.display = 'none';
        }
    }

    // On load, restore visibility if old input exists
    document.addEventListener('DOMContentLoaded', toggleFilingDateInput);
</script>
@endpush
