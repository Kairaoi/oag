@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        
        {{ Breadcrumbs::render() }}
        
    </nav>

    <!-- Heading -->
    <h1 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #333; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);">Edit Criminal Case</h1>

    <!-- Form -->
    <form action="{{ route('crime.criminalCase.update', $criminalCase->case_id) }}" method="POST" class="p-4 shadow-lg rounded" style="background: linear-gradient(90deg, #007bff, #00c6ff); border-radius: 20px;">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="case_file_number" class="text-white">Case File Number</label>
            <input type="text" class="form-control @error('case_file_number') is-invalid @enderror" id="case_file_number" name="case_file_number" value="{{ old('case_file_number', $criminalCase->case_file_number) }}" required>
            @error('case_file_number')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group">
            <label for="case_name" class="text-white">Case Name</label>
            <input type="text" class="form-control @error('case_name') is-invalid @enderror" id="case_name" name="case_name" value="{{ old('case_name', $criminalCase->case_name) }}" required>
            @error('case_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group">
            <label for="date_file_received" class="text-white">Date File Received</label>
            <input type="date" class="form-control @error('date_file_received') is-invalid @enderror" id="date_file_received" name="date_file_received" value="{{ old('date_file_received', $criminalCase->date_file_received->format('Y-m-d')) }}" required>
            @error('date_file_received')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group">
            <label for="date_file_closed" class="text-white">Date File Closed</label>
            <input type="date" class="form-control @error('date_file_closed') is-invalid @enderror" id="date_file_closed" name="date_file_closed" value="{{ old('date_file_closed', $criminalCase->date_file_closed ? $criminalCase->date_file_closed->format('Y-m-d') : '') }}">
            @error('date_file_closed')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group">
            <label for="reason_for_closure" class="text-white">Reason for Closure</label>
            <textarea class="form-control @error('reason_for_closure') is-invalid @enderror" id="reason_for_closure" name="reason_for_closure">{{ old('reason_for_closure', $criminalCase->reason_for_closure) }}</textarea>
            @error('reason_for_closure')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <button type="submit" class="btn btn-light btn-lg btn-block" style="border-radius: 30px; font-weight: bold;">Update</button>
    </form>
</div>
@endsection
