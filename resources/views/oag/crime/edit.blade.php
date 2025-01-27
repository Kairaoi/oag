@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('crime.criminalCase.index') }}">Cases</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Case</li>
        </ol>
    </nav>

    <h1 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #333; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);">Edit Case</h1>

    <form action="{{ route('crime.criminalCase.update', $criminalCase->id) }}" method="POST" class="p-4 shadow-lg rounded" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); border-radius: 20px;">
        @csrf
        @method('PUT')
        
        <!-- Case File Number -->
        <div class="form-group">
            <label for="case_file_number" class="text-white">Case File Number</label>
            <input type="text" class="form-control @error('case_file_number') is-invalid @enderror" id="case_file_number" name="case_file_number" value="{{ old('case_file_number', $criminalCase->case_file_number) }}" required>
            @error('case_file_number')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Date File Received -->
        <div class="form-group">
            <label for="date_file_received" class="text-white">Date File Received</label>
            <input type="date" class="form-control @error('date_file_received') is-invalid @enderror" id="date_file_received" name="date_file_received" value="{{ old('date_file_received', $criminalCase->date_file_received) }}" required>
            @error('date_file_received')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Case Name -->
        <div class="form-group">
            <label for="case_name" class="text-white">Case Name</label>
            <input type="text" class="form-control @error('case_name') is-invalid @enderror" id="case_name" name="case_name" value="{{ old('case_name', $criminalCase->case_name) }}" required>
            @error('case_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Date of Allocation -->
        <div class="form-group">
            <label for="date_of_allocation" class="text-white">Date of Allocation</label>
            <input type="date" class="form-control @error('date_of_allocation') is-invalid @enderror" id="date_of_allocation" name="date_of_allocation" value="{{ old('date_of_allocation', $criminalCase->date_of_allocation) }}">
            @error('date_of_allocation')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Date File Closed -->
        <div class="form-group">
            <label for="date_file_closed" class="text-white">Date File Closed</label>
            <input type="date" class="form-control @error('date_file_closed') is-invalid @enderror" id="date_file_closed" name="date_file_closed" value="{{ old('date_file_closed', $criminalCase->date_file_closed) }}">
            @error('date_file_closed')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Reason for Closure -->
        <div class="form-group">
            <label for="reason_for_closure_id" class="text-white">Reason for Closure</label>
            <select class="form-control @error('reason_for_closure_id') is-invalid @enderror" id="reason_for_closure_id" name="reason_for_closure_id">
                <option value="">Select a reason</option>
                @foreach($reasons as $id => $reason)
                    <option value="{{ $id }}" {{ old('reason_for_closure_id', $criminalCase->reason_for_closure_id) == $id ? 'selected' : '' }}>
                        {{ $reason }}
                    </option>
                @endforeach
            </select>
            @error('reason_for_closure_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Lawyer Field -->
        <div class="form-group">
            <label for="lawyer_id" class="text-white">Lawyer</label>
            <select class="form-control @error('lawyer_id') is-invalid @enderror" id="lawyer_id" name="lawyer_id" required>
                <option value="">Select a lawyer</option>
                @foreach($lawyers as $id => $name)
                    <option value="{{ $id }}" {{ old('lawyer_id', $criminalCase->lawyer_id) == $id ? 'selected' : '' }}>
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
                    <option value="{{ $id }}" {{ old('island_id', $criminalCase->island_id) == $id ? 'selected' : '' }}>
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
        <button type="submit" class="btn btn-light btn-lg btn-block" style="border-radius: 30px; font-weight: bold;">Update Case</button>
    </form>
</div>
@endsection
