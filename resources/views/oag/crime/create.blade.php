@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb" style="background: none; box-shadow: none;">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color: #ff4b2b; font-weight: bold;">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('crime.criminalCase.index') }}" style="color: #ff4b2b; font-weight: bold;">Criminal Cases</a></li>
            <li class="breadcrumb-item active" aria-current="page" style="color: #333; font-weight: bold;">Create New Case</li>
        </ol>
    </nav>

    <!-- Main Title -->
    <h1 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #333; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);">
        Create New Case
    </h1>

    <!-- Form -->
    <form action="{{ route('crime.criminalCase.store') }}" method="POST" class="p-4 shadow-lg rounded" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); border-radius: 20px;">
        @csrf
        
        <!-- Case File Number -->
        <div class="form-group">
            <label for="case_file_number" class="text-white">Case File Number</label>
            <input type="text" class="form-control @error('case_file_number') is-invalid @enderror" id="case_file_number" name="case_file_number" value="{{ old('case_file_number') }}" required>
            @error('case_file_number')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Date File Received -->
        <div class="form-group">
            <label for="date_file_received" class="text-white">Date File Received</label>
            <input type="date" class="form-control @error('date_file_received') is-invalid @enderror" id="date_file_received" name="date_file_received" value="{{ old('date_file_received') }}" required>
            @error('date_file_received')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Case Name -->
        <div class="form-group">
            <label for="case_name" class="text-white">Case Name</label>
            <input type="text" class="form-control @error('case_name') is-invalid @enderror" id="case_name" name="case_name" value="{{ old('case_name') }}" required>
            @error('case_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Date of Allocation -->
        <div class="form-group">
            <label for="date_of_incident" class="text-white">Date of Incident</label>
            <input type="date" class="form-control @error('date_of_incident') is-invalid @enderror" id="date_of_incident" name="date_of_incident" value="{{ old('date_of_incident') }}">
            @error('date_of_incident')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        
        <!-- Island Field -->
        <div class="form-group">
            <label for="island_id" class="text-white">Place of Incident</label>
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

        <!-- Submit Button -->
        <button type="submit" class="btn btn-light btn-lg btn-block" style="border-radius: 30px; font-weight: bold;">Create Case</button>
        @if(isset($criminalCase))
    <a href="{{ route('crime.criminalCase.createAccused', $criminalCase->id) }}" class="btn btn-success">
        Add Accused to this Case
    </a>
@endif

    </form>
</div>
@endsection