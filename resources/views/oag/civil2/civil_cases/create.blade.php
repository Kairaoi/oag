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
        padding: 2rem 2rem;
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

    .invalid-feedback {
        font-size: 0.9rem;
        color: #d6336c;
        font-weight: 600;
    }

    .mb-4 {
        margin-bottom: 1.5rem !important;
    }
</style>

<div class="container mt-5">
    <h2>Create New Civil Case</h2>

    <form method="POST" action="{{ route('civil2.cases.store') }}">
        @csrf

        {{-- Case Name --}}
        <div class="mb-4">
            <label for="case_name">Case Name</label>
            <input type="text" class="form-control @error('case_name') is-invalid @enderror" id="case_name" name="case_name" value="{{ old('case_name') }}" required>
            @error('case_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- File Number --}}
        <div class="mb-4">
            <label for="case_file_no">File Number</label>
            <input type="text" class="form-control @error('case_file_no') is-invalid @enderror" id="case_file_no" name="case_file_no" value="{{ old('case_file_no') }}" required>
            @error('case_file_no')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Court Case Number --}}
        <div class="mb-4">
            <label for="court_case_no">Court Case Number</label>
            <input type="text" class="form-control @error('court_case_no') is-invalid @enderror" id="court_case_no" name="court_case_no" value="{{ old('court_case_no') }}">
            @error('court_case_no')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Date Received --}}
        <div class="mb-4">
            <label for="date_received">Date Received</label>
            <input type="date" class="form-control @error('date_received') is-invalid @enderror" id="date_received" name="date_received" value="{{ old('date_received') }}" required>
            @error('date_received')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Date Opened --}}
        <div class="mb-4">
            <label for="date_opened">Date Opened</label>
            <input type="date" class="form-control @error('date_opened') is-invalid @enderror" id="date_opened" name="date_opened" value="{{ old('date_opened') }}" required>
            @error('date_opened')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
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
    @error('court_type_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>


        {{-- Cause of Action --}}
        <div class="mb-4">
            <label for="cause_of_action_id">Cause of Action</label>
            <select class="form-control @error('cause_of_action_id') is-invalid @enderror" id="cause_of_action_id" name="cause_of_action_id" required>
                <option value="" disabled {{ old('cause_of_action_id') ? '' : 'selected' }}>Select Cause of Action</option>
                @foreach($causesOfAction as $id => $cause)
                    <option value="{{ $id }}" {{ old('cause_of_action_id') == $id ? 'selected' : '' }}>{{ $cause }}</option>
                @endforeach
            </select>
            @error('cause_of_action_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Case Status --}}
        <div class="mb-4">
            <label for="case_status_id">Case Status</label>
            <select class="form-control @error('case_status_id') is-invalid @enderror" id="case_status_id" name="case_status_id" required>
                <option value="" disabled {{ old('case_status_id') ? '' : 'selected' }}>Select Case Status</option>
                @foreach($caseStatuses as $id => $status)
                    <option value="{{ $id }}" {{ old('case_status_id') == $id ? 'selected' : '' }}>{{ $status }}</option>
                @endforeach
            </select>
            @error('case_status_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Pending Status --}}
        <div class="mb-4">
            <label for="case_pending_status_id">Pending Status</label>
            <select class="form-control @error('case_pending_status_id') is-invalid @enderror" id="case_pending_status_id" name="case_pending_status_id">
                <option value="" {{ old('case_pending_status_id') == '' ? 'selected' : '' }}>None</option>
                @foreach($casePendingStatuses as $id => $status)
                    <option value="{{ $id}}" {{ old('case_pending_status_id') == $id? 'selected' : '' }}>{{ $status }}</option>
                @endforeach
            </select>
            @error('case_pending_status_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Case Origin --}}
        <div class="mb-4">
            <label for="case_origin_type_id">Case Origin</label>
            <select class="form-control @error('case_origin_type_id') is-invalid @enderror" id="case_origin_type_id" name="case_origin_type_id" required>
                <option value="" disabled {{ old('case_origin_type_id') ? '' : 'selected' }}>Select Case Origin</option>
                @foreach($caseOriginTypes as $id => $type)
                    <option value="{{ $id }}" {{ old('case_origin_type_id') == $id ? 'selected' : '' }}>{{ $type }}</option>
                @endforeach
            </select>
            @error('case_origin_type_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Description --}}
        <div class="mb-4">
            <label for="case_description">Description</label>
            <textarea class="form-control @error('case_description') is-invalid @enderror" id="case_description" name="case_description" rows="3" placeholder="Optional description">{{ old('case_description') }}</textarea>
            @error('case_description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Responsible Counsel --}}
        <div class="mb-4">
            <label for="responsible_counsel_id">Responsible Counsel</label>
            <select class="form-control @error('responsible_counsel_id') is-invalid @enderror" id="responsible_counsel_id" name="responsible_counsel_id">
                <option value="{{ Auth::id() }}" {{ old('responsible_counsel_id', Auth::id()) == Auth::id() ? 'selected' : '' }}>Me (Default)</option>
                @foreach($counsels as $id => $counsel)
                    <option value="{{ $id }}" {{ old('responsible_counsel_id') == $id ? 'selected' : '' }}>{{ $counsel }}</option>
                @endforeach
            </select>
            @error('responsible_counsel_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Submit Button --}}
        <button type="submit" class="btn btn-primary">Create Case</button>
    </form>
</div>
@endsection
