@extends('layouts.app')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #667eea, #764ba2);
        min-height: 100vh;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        padding: 20px 0;
    }
    .form-container {
        background: #fff;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        transition: box-shadow 0.3s ease;
    }
    .form-container:hover {
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.25);
    }
    h2 {
        color: #4b0082;
        margin-bottom: 1.5rem;
        font-weight: 700;
        text-align: center;
    }
    label {
        font-weight: 600;
        color: #4b0082;
    }
    .form-control {
        border-radius: 8px;
        border: 2px solid #ddd;
        padding: 0.5rem 1rem;
        font-size: 1rem;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }
    .form-control:focus {
        border-color: #764ba2;
        box-shadow: 0 0 8px rgba(118, 75, 162, 0.5);
        outline: none;
    }
    .btn-primary {
        background: linear-gradient(90deg, #667eea, #764ba2);
        border: none;
        border-radius: 10px;
        font-weight: 700;
        font-size: 1.1rem;
        padding: 0.75rem;
        box-shadow: 0 6px 12px rgba(102, 126, 234, 0.5);
        transition: background 0.3s ease, box-shadow 0.3s ease;
    }
    .btn-primary:hover {
        background: linear-gradient(90deg, #5a6cd8, #653d9a);
        box-shadow: 0 8px 20px rgba(101, 61, 154, 0.7);
    }
    .btn-add {
        margin-top: 5px;
        font-size: 0.9rem;
    }
    .invalid-feedback {
        font-size: 0.9rem;
        color: #d6336c;
        font-weight: 600;
    }
    .mb-4 {
        margin-bottom: 1.5rem !important;
    }
</style>

<div class="container mt-5 form-container">
    <h2>Create New Civil Case</h2>

    <form method="POST" action="{{ route('civil2.cases.store') }}">
        @csrf

        {{-- Case Origin --}}
        <div class="mb-4">
            <label for="case_origin_type_id">Case Origin</label>
            <select class="form-control @error('case_origin_type_id') is-invalid @enderror" id="case_origin_type_id" name="case_origin_type_id" required>
                <option value="" disabled {{ old('case_origin_type_id') ? '' : 'selected' }}>Select Case Origin</option>
                @foreach($caseOriginTypes as $id => $type)
                    <option value="{{ $id }}" {{ old('case_origin_type_id') == $id ? 'selected' : '' }}>{{ $type }}</option>
                @endforeach
            </select>
            @error('case_origin_type_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Court Type --}}
        <div class="mb-4">
            <label for="court_type_id">Court Type</label>
            <select class="form-control @error('court_type_id') is-invalid @enderror" id="court_type_id" name="court_type_id" required>
                <option value="" disabled {{ old('court_type_id') ? '' : 'selected' }}>Select Court Type</option>
                @foreach($courtCategories as $id => $courtCategory)
                    <option value="{{ $id }}" {{ old('court_type_id') == $id ? 'selected' : '' }}>{{ $courtCategory }}</option>
                @endforeach
            </select>
            @error('court_type_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- File Number --}}
        <div class="mb-4">
            <label for="case_file_no">File Number</label>
            <input type="text" class="form-control @error('case_file_no') is-invalid @enderror" id="case_file_no" name="case_file_no" value="{{ old('case_file_no') }}" required>
            @error('case_file_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-4">
            <label for="case_name">Case Name</label>
            <input type="text" class="form-control @error('case_name') is-invalid @enderror" id="case_name" name="case_name" value="{{ old('case_name') }}" required>
            @error('case_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-4">
            <label for="court_case_no">Court Case Number</label>
            <input type="text" class="form-control @error('court_case_no') is-invalid @enderror" id="court_case_no" name="court_case_no" value="{{ old('court_case_no') }}">
            @error('court_case_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-4">
            <label for="date_received">Date Received</label>
            <input type="date" class="form-control @error('date_received') is-invalid @enderror" id="date_received" name="date_received" value="{{ old('date_received') }}" required>
            @error('date_received') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-4">
            <label for="date_opened">Date Opened</label>
            <input type="date" class="form-control @error('date_opened') is-invalid @enderror" id="date_opened" name="date_opened" value="{{ old('date_opened') }}" required>
            @error('date_opened') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-4">
            <label for="cause_of_action_id">Cause of Action</label>
            <select class="form-control @error('cause_of_action_id') is-invalid @enderror" id="cause_of_action_id" name="cause_of_action_id" required>
                <option value="" disabled {{ old('cause_of_action_id') ? '' : 'selected' }}>Select Cause of Action</option>
                @foreach($causesOfAction as $id => $cause)
                    <option value="{{ $id }}" {{ old('cause_of_action_id') == $id ? 'selected' : '' }}>{{ $cause }}</option>
                @endforeach
            </select>
            @error('cause_of_action_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

      <div class="mb-4">
    <label for="status_date">Status Date</label>
    <input type="date" class="form-control @error('status_date') is-invalid @enderror" id="status_date" name="status_date" value="{{ old('status_date') }}" required>
    @error('status_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-4">
    <label for="current_status">Current Status of the case (as of date of reporting)</label>
    <textarea class="form-control @error('current_status') is-invalid @enderror" id="current_status" name="current_status" rows="3" required>{{ old('current_status') }}</textarea>
    @error('current_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-4">
    <label for="action_required">Action Required</label>
    <textarea class="form-control @error('action_required') is-invalid @enderror" id="action_required" name="action_required" rows="2">{{ old('action_required') }}</textarea>
    @error('action_required') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-4">
    <label for="monitoring_status">Monitoring Status</label>
    <input type="text" class="form-control @error('monitoring_status') is-invalid @enderror" id="monitoring_status" name="monitoring_status" value="{{ old('monitoring_status') }}">
    @error('monitoring_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>


        <div class="mb-4">
            <label for="responsible_counsel_id">Responsible Counsel</label>
            <select class="form-control @error('responsible_counsel_id') is-invalid @enderror" id="responsible_counsel_id" name="responsible_counsel_id">
                <option value="{{ Auth::id() }}" {{ old('responsible_counsel_id', Auth::id()) == Auth::id() ? 'selected' : '' }}>Me (Default)</option>
                @foreach($internalCounsels as $id => $counsel)
                    <option value="{{ $id }}" {{ old('responsible_counsel_id') == $id ? 'selected' : '' }}>{{ $counsel }}</option>
                @endforeach
            </select>
            @error('responsible_counsel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

       {{-- Counsel Assignment --}}
        <div class="mb-4">
            <label class="form-label fw-bold">Assign Counsels (Internal or External)</label>
            <div id="counsel-rows">
                <div class="row g-2 mb-2 counsel-row">
                    <div class="col-md-6">
                        <select class="form-control counsel-id" name="counsels[0][id]" onchange="updateCounselType(this)">
                            <option value="">Select Counsel</option>
                            <optgroup label="Internal (Government Counsel)">
                                @foreach($internalCounsels as $id => $name)
                                    <option value="{{ $id }}" data-type="user">{{ $name }}</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="External (Opposing Party)">
                                @foreach($externalCounsels as $id => $name)
                                    <option value="{{ $id }}" data-type="external">{{ $name }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <select class="form-control" name="counsels[0][role]">
                            <option value="plaintiff">Plaintiff</option>
                            <option value="defendant">Defendant</option>
                        </select>
                        <input type="hidden" class="counsel-type" name="counsels[0][type]" value="">
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-outline-secondary btn-sm btn-add" onclick="addCounselRow()">+ Add More Counsel</button>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primary">Create Case</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function updateCounselType(select) {
    const selected = select.options[select.selectedIndex];
    const type = selected.dataset.type;
    const row = select.closest('.row');
    const typeInput = row.querySelector('.counsel-type');
    if (type && typeInput) {
        typeInput.value = type;
    }
}

function addCounselRow() {
    const index = document.querySelectorAll('.counsel-row').length;
    const html = `
        <div class="row g-2 mb-2 counsel-row">
            <div class="col-md-6">
                <select class="form-control counsel-id" name="counsels[${index}][id]" onchange="updateCounselType(this)">
                    <option value="">Select Counsel</option>
                    <optgroup label="Internal (Government Counsel)">
                        @foreach($internalCounsels as $id => $name)
                            <option value="{{ $id }}" data-type="user">{{ $name }}</option>
                        @endforeach
                    </optgroup>
                    <optgroup label="External (Opposing Party)">
                        @foreach($externalCounsels as $id => $name)
                            <option value="{{ $id }}" data-type="external">{{ $name }}</option>
                        @endforeach
                    </optgroup>
                </select>
            </div>
            <div class="col-md-6">
                <select class="form-control" name="counsels[${index}][role]">
                    <option value="plaintiff">Plaintiff</option>
                    <option value="defendant">Defendant</option>
                </select>
                <input type="hidden" class="counsel-type" name="counsels[${index}][type]" value="">
            </div>
        </div>
    `;
    document.getElementById('counsel-rows').insertAdjacentHTML('beforeend', html);
}

document.addEventListener('DOMContentLoaded', function () {
    const courtTypeSelect = document.getElementById('court_type_id');
    const fileNumberInput = document.getElementById('case_file_no');

    const courtPrefixes = {
        'Magistrate Court': 'MM',
        'High Court': 'Lit',
        'Court of Appeal': 'Coa'
    };

    function updateFileNumber() {
        const selectedOption = courtTypeSelect.options[courtTypeSelect.selectedIndex];
        const prefix = courtPrefixes[selectedOption?.text] || '';
        if (prefix) {
            const cleaned = fileNumberInput.value.replace(/^(MM|Lit|Coa)-?/, '').trim();
            fileNumberInput.value = `${prefix}-${cleaned}`;
        }
    }

    courtTypeSelect.addEventListener('change', updateFileNumber);
    if (courtTypeSelect.value && fileNumberInput.value.trim() !== '') {
        updateFileNumber();
    }
});
</script>
@endpush

@endsection