@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Court Attendance Details</h1>

        <div class="card">
            <div class="card-header">Court Attendance #{{ $courtAttendance->id }}</div>
            <div class="card-body">
                <p><strong>Civil Case:</strong> {{ $courtAttendance->civilCase->case_number }}</p>
                <p><strong>Opposing Counsel Name:</strong> {{ $courtAttendance->opposing_counsel_name }}</p>
                <p><strong>Hearing Date:</strong> {{ $courtAttendance->hearing_date->format('d/m/Y') }}</p>
                <p><strong>Hearing Type:</strong> {{ $courtAttendance->hearing_type }}</p>
                <p><strong>Hearing Time:</strong> {{ $courtAttendance->hearing_time ? $courtAttendance->hearing_time->format('H:i') : 'N/A' }}</p>
                <p><strong>Case Status:</strong> {{ $courtAttendance->case_status }}</p>
                <p><strong>Status Notes:</strong> {{ $courtAttendance->status_notes ?? 'No additional notes.' }}</p>
            </div>
            <div class="card-footer">
                <a href="{{ route('court_attendances.edit', $courtAttendance->id) }}" class="btn btn-warning">Edit</a>
                <a href="{{ route('court_attendances.index') }}" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
    </div>
@endsection
