@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #333; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);">Criminal Case Details</h1>

    <div class="card shadow-lg rounded" style="border: 2px solid #007bff; border-radius: 20px;">
        <div class="card-header text-white" style="background: linear-gradient(90deg, #007bff, #00c6ff);">
            <h5>Case File Number: <strong>{{ $criminalCase->case_file_number }}</strong></h5>
        </div>
        <div class="card-body">
            <p><strong>Case Name:</strong> {{ $criminalCase->case_name }}</p>
            <p><strong>Date File Received:</strong> {{ $criminalCase->date_file_received->format('Y-m-d') }}</p>
            <p><strong>Date File Closed:</strong> {{ $criminalCase->date_file_closed ? $criminalCase->date_file_closed->format('Y-m-d') : 'N/A' }}</p>
            <p><strong>Reason for Closure:</strong> {{ $criminalCase->reason_for_closure }}</p>
        </div>
        <div class="card-footer text-center">
            <a href="{{ route('crime.criminalCase.index') }}" class="btn btn-secondary btn-lg" style="border-radius: 30px;">Back to List</a>
            <a href="{{ route('crime.criminalCase.edit', $criminalCase->case_id) }}" class="btn btn-warning btn-lg" style="border-radius: 30px;">Edit</a>
            <form action="{{ route('crime.criminalCase.destroy', $criminalCase->case_id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-lg" style="border-radius: 30px;" onclick="return confirm('Are you sure?')">Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection