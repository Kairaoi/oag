@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('crime.incident.index') }}">crime.s</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create New Incident</li>
        </ol>
    </nav>

    <!-- Heading -->
    <h1 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #333; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);">Create New Incident</h1>

    <!-- Form -->
    <form action="{{ route('crime.incident.store') }}" method="POST" class="p-4 shadow-lg rounded" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); border-radius: 20px;">
        @csrf

        <!-- Hidden field for tracking if from case -->
        @if(isset($selected_case_id))
        <input type="hidden" name="from_case" value="1">
        @endif

        <!-- Case Selection -->
        <div class="form-group">
            <label for="case_id" class="text-white">Case</label>
            <select class="form-control @error('case_id') is-invalid @enderror" id="case_id" name="case_id" required>
                <option value="">Select a case</option>
                @foreach($cases as $id => $name)
                    <option value="{{ $id }}" {{ old('case_id', $selected_case_id ?? '') == $id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
            @error('case_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Lawyer Selection -->
        <div class="form-group">
            <label for="lawyer_id" class="text-white">Lawyer</label>
            <select class="form-control @error('lawyer_id') is-invalid @enderror" id="lawyer_id" name="lawyer_id" required>
                <option value="">Select a lawyer</option>
                @foreach($lawyers as $id => $name)
                    <option value="{{ $id }}" {{ old('lawyer_id', $selected_lawyer_id ?? '') == $id ? 'selected' : '' }}>
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

        <div class="form-group">
            <label for="island_id" class="text-white">Island</label>
            <select class="form-control @error('island_id') is-invalid @enderror" id="island_id" name="island_id" required>
                <option value="">Select an island</option>
                @foreach($islands as $id => $name)
                    <option value="{{ $id }}" {{ old('island_id') == $id ? 'selected' : '' }}>
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

        <div class="form-group">
            <label for="date_of_incident_start" class="text-white">Date of Incident Start</label>
            <input type="date" class="form-control @error('date_of_incident_start') is-invalid @enderror" id="date_of_incident_start" name="date_of_incident_start" value="{{ old('date_of_incident_start') }}" required>
            @error('date_of_incident_start')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group">
            <label for="date_of_incident_end" class="text-white">Date of Incident End</label>
            <input type="date" class="form-control @error('date_of_incident_end') is-invalid @enderror" id="date_of_incident_end" name="date_of_incident_end" value="{{ old('date_of_incident_end') }}">
            @error('date_of_incident_end')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group">
            <label for="place_of_incident" class="text-white">Place of Incident</label>
            <input type="text" class="form-control @error('place_of_incident') is-invalid @enderror" id="place_of_incident" name="place_of_incident" value="{{ old('place_of_incident') }}" required>
            @error('place_of_incident')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group mt-4">
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-light" style="border-radius: 30px; font-weight: bold;">
                    Create Incident
                </button>
                
                <button type="submit" name="add_another_incident" value="1" class="btn btn-info" style="border-radius: 30px; font-weight: bold;">
                    Create & Add Another Incident
                </button>
                
                <button type="submit" name="return_to_case" value="1" class="btn btn-success" style="border-radius: 30px; font-weight: bold;">
                    Create & Return to Case
                </button>
            </div>
        </div>
    </form>
</div>
@endsection