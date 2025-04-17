@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb" style="background: none; box-shadow: none;">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color: #ff4b2b; font-weight: bold;">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('crime.criminalCase.index') }}" style="color: #ff4b2b; font-weight: bold;">Criminal Cases</a></li>
            <li class="breadcrumb-item active" aria-current="page" style="color: #333; font-weight: bold;">Create Appeal Case</li>
        </ol>
    </nav>

    <!-- Main Title -->
    <h1 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #333; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);">
        Create Appeal Case
    </h1>

    <!-- Explanation Card -->
    <div class="card mb-4 bg-light">
        <div class="card-body">
            <h5 class="card-title">Creating an Appeal Case</h5>
            <p class="card-text">
                This form creates a new appeal case and marks the original case as being on appeal.
                The original case will remain in the system but will be linked to this new appeal.
                All relevant information about the appeal will be tracked separately.
            </p>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('crime.criminalCase.storeAppeal') }}" method="POST" class="p-4 shadow-lg rounded" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); border-radius: 20px;">
        @csrf
        
        <!-- Original Case -->
        <div class="form-group">
            <label for="original_case_id" class="text-white">Original Case</label>
            <select name="original_case_id" id="original_case_id" class="form-control @error('original_case_id') is-invalid @enderror" required>
                <option value="">-- Select Case --</option>
                @foreach($originalCases as $caseId => $caseName)
                    <option value="{{ $caseId }}" {{ (isset($selectedCaseId) && $selectedCaseId == $caseId) || old('original_case_id') == $caseId ? 'selected' : '' }}>
                        {{ $caseName }}
                    </option>
                @endforeach
            </select>
            @error('original_case_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Case File Number -->
        <div class="form-group">
            <label for="case_file_number" class="text-white">Appeal File Number</label>
            <input type="text" class="form-control @error('case_file_number') is-invalid @enderror" id="case_file_number" name="case_file_number" value="{{ old('case_file_number') }}" required>
            <small class="form-text text-white">A unique identifier for this appeal case</small>
            @error('case_file_number')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Basic Fields -->
        <div class="form-group">
            <label for="case_name" class="text-white">Appeal Case Name</label>
            <input type="text" class="form-control @error('case_name') is-invalid @enderror" id="case_name" name="case_name" value="{{ old('case_name') ?? ($suggestedValues['case_name'] ?? '') }}" required>
            @error('case_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group">
            <label for="date_file_received" class="text-white">Date Appeal Received</label>
            <input type="date" class="form-control @error('date_file_received') is-invalid @enderror" id="date_file_received" name="date_file_received" value="{{ old('date_file_received') ?? date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required>
            <small class="form-text text-white">Cannot be a future date</small>
            @error('date_file_received')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Court of Appeal -->
        <div class="form-group">
            <label for="court_of_appeal_id" class="text-white">Court of Appeal</label>
            <select class="form-control @error('court_of_appeal_id') is-invalid @enderror" id="court_of_appeal_id" name="court_of_appeal_id" required>
                <option value="">Select Court of Appeal</option>
                @foreach($courtsOfAppeal as $id => $court)
                    <option value="{{ $id }}" {{ old('court_of_appeal_id') == $id ? 'selected' : '' }}>
                        {{ $court }}
                    </option>
                @endforeach
            </select>
            @error('court_of_appeal_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Appeal Grounds -->
        <div class="form-group">
            <label for="appeal_grounds" class="text-white">Appeal Grounds</label>
            <textarea class="form-control @error('appeal_grounds') is-invalid @enderror" id="appeal_grounds" name="appeal_grounds" rows="4" required>{{ old('appeal_grounds') }}</textarea>
            <small class="form-text text-white">Specify the legal grounds for this appeal</small>
            @error('appeal_grounds')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Lawyer Field -->
        <div class="form-group">
            <label for="lawyer_id" class="text-white">Appeal Lawyer</label>
            <select class="form-control @error('lawyer_id') is-invalid @enderror" id="lawyer_id" name="lawyer_id" required>
                <option value="">Select a lawyer</option>
                @foreach($lawyers as $id => $name)
                    <option value="{{ $id }}" {{ old('lawyer_id') == $id || (isset($suggestedValues['lawyer_id']) && $suggestedValues['lawyer_id'] == $id) ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
            @error('lawyer_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Island Field -->
        <div class="form-group">
            <label for="island_id" class="text-white">Island</label>
            <select class="form-control @error('island_id') is-invalid @enderror" id="island_id" name="island_id" required>
                <option value="">Select an island</option>
                @foreach($islands as $id => $name)
                    <option value="{{ $id }}" {{ old('island_id') == $id || (isset($suggestedValues['island_id']) && $suggestedValues['island_id'] == $id) ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
            @error('island_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Submit and Cancel Buttons -->
        <div class="row mt-4">
            <div class="col-md-6 mb-2">
                <a href="{{ route('crime.criminalCase.index') }}" class="btn btn-light btn-lg btn-block" style="border-radius: 30px; font-weight: bold;">Cancel</a>
            </div>
            <div class="col-md-6">
                <button type="submit" class="btn btn-light btn-lg btn-block" style="border-radius: 30px; font-weight: bold;">Create Appeal Case</button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const originalCaseSelect = document.getElementById('original_case_id');
    const caseNameInput = document.getElementById('case_name');
    const fileNumberInput = document.getElementById('case_file_number');
    
    // Function to update appeal case info when original case changes
    originalCaseSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            // Set case name to "Appeal - [Original Case Name]"
            if (!caseNameInput.value) {
                caseNameInput.value = 'Appeal - ' + selectedOption.text.trim();
            }
            
            // Generate appeal number
            if (!fileNumberInput.value) {
                generateAppealNumber(selectedOption.value);
            }
            
            // Optionally, you could also fetch additional case details via AJAX
            // to populate other fields based on the original case
        }
    });
    
    // Auto-populate on page load if original case is already selected
    if (originalCaseSelect.value && !caseNameInput.value) {
        const selectedOption = originalCaseSelect.options[originalCaseSelect.selectedIndex];
        caseNameInput.value = 'Appeal - ' + selectedOption.text.trim();
    }
    
    if (originalCaseSelect.value && !fileNumberInput.value) {
        generateAppealNumber(originalCaseSelect.value);
    }
});

// Generate a formatted appeal file number
function generateAppealNumber(caseId) {
    const currentYear = new Date().getFullYear();
    const appealNumber = `APP/${caseId}/${currentYear}`;
    document.getElementById('case_file_number').value = appealNumber;
}
</script>
@endsection