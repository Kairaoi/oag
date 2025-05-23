<!-- resources/views/oag/crime/allocate_lawyer.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
    {{ Breadcrumbs::render() }}
    </nav>

    <!-- Main Title -->
    <h1 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #333; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);">
        Allocate Lawyer to Case
    </h1>

    <!-- Case Information Card -->
    <div class="card mb-4">
        <div class="card-header" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); color: white;">
            Case Information
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Case File Number:</strong> {{ $criminalCase->case_file_number }}</p>
                    <p><strong>Case Name:</strong> {{ $criminalCase->case_name }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Date File Received:</strong> {{ $criminalCase->date_file_received }}</p>
                    <p><strong>Status:</strong> {{ ucfirst($criminalCase->status ?? 'New') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Allocation Form -->
    <form action="{{ route('crime.criminalCase.allocateLawyer', $criminalCase->id) }}" method="POST" class="p-4 shadow-lg rounded" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); border-radius: 20px;">
        @csrf
        
        <!-- Lawyer Selection -->
        <label for="to_lawyer_id" class="text-white">Select Lawyer</label>
<select class="form-control @error('to_lawyer_id') is-invalid @enderror" id="to_lawyer_id" name="to_lawyer_id" required>
    <option value="">Select a lawyer</option>
    @foreach($lawyers as $id => $name)
        <option value="{{ $id }}" {{ old('to_lawyer_id') == $id ? 'selected' : '' }}>
            {{ $name }}
        </option>
    @endforeach
</select>
@error('to_lawyer_id')
    <span class="invalid-feedback" role="alert">
        <strong>{{ $message }}</strong>
    </span>
@enderror


        <!-- Allocation Date -->
        <div class="form-group">
            <label for="reallocation_date" class="text-white">Date of Allocation</label>
            <input type="date" class="form-control @error('reallocation_date') is-invalid @enderror" id="reallocation_date" name="reallocation_date" value="{{ old('reallocation_date', date('Y-m-d')) }}" required>
            @error('reallocation_date')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Allocation Notes -->
        <div class="form-group">
            <label for="reallocation_reason" class="text-white">Allocation Notes/Reason</label>
            <textarea class="form-control @error('reallocation_reason') is-invalid @enderror" id="reallocation_reason" name="reallocation_reason" rows="3">{{ old('reallocation_reason') }}</textarea>
            @error('reallocation_reason')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="form-group mt-4">
            <button type="submit" class="btn btn-light btn-lg btn-block" style="border-radius: 30px; font-weight: bold;">Allocate Lawyer</button>
        </div>
    </form>

    <!-- Back Button -->
    <div class="mt-3 text-center">
        <a href="{{ route('crime.criminalCase.show', $criminalCase->id) }}" class="btn btn-secondary">
            Back to Case Details
        </a>
    </div>
</div>
@endsection