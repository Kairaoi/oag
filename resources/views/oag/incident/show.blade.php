@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb" style="background: #f8f9fa; border-radius: 25px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color: #007bff; text-decoration: none;">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('crime.incident.index') }}" style="color: #007bff; text-decoration: none;">Incidents</a></li>
            <li class="breadcrumb-item active" aria-current="page" style="color: #6c757d;">Incident Details</li>
        </ol>
    </nav>

    <!-- Heading -->
    <h2 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #f8f9fa; text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.4); border-bottom: 4px double #ff6f61; padding-bottom: 20px;">
        Incident Details
    </h2>

    <!-- Incident Details -->
    <div class="card shadow-lg rounded-3 overflow-hidden" style="background: linear-gradient(145deg, #f1f1f1, #ffffff); border: 2px solid #007bff; border-radius: 20px;">
        <div class="card-body">
            <h5 class="card-title">Incident ID: {{ $incident->id }}</h5>
            <p class="card-text"><strong>Case File Number:</strong> {{ $incident->case_file_number }}</p>
            <p class="card-text"><strong>Lawyer Name:</strong> {{ $incident->lawyer_name }}</p>
            <p class="card-text"><strong>Island Name:</strong> {{ $incident->island_name }}</p>
            <p class="card-text"><strong>Date of Incident (Start):</strong> {{ $incident->date_of_incident_start->format('Y-m-d') }}</p>
            <p class="card-text"><strong>Date of Incident (End):</strong> {{ $incident->date_of_incident_end ? $incident->date_of_incident_end->format('Y-m-d') : 'N/A' }}</p>
            <p class="card-text"><strong>Place of Incident:</strong> {{ $incident->place_of_incident }}</p>

            <!-- Action Buttons -->
            <div class="text-center mt-4">
                <a href="{{ route('crime.incident.edit', $incident->id) }}" class="btn btn-primary px-4 py-2" style="border-radius: 30px; font-weight: bold;">Edit</a>
                <form action="{{ route('crime.incident.destroy', $incident->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this incident?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger px-4 py-2" style="border-radius: 30px; font-weight: bold;">Delete</button>
                </form>
                <a href="{{ route('crime.incident.index') }}" class="btn btn-secondary px-4 py-2" style="border-radius: 30px; font-weight: bold;">Back to List</a>
            </div>
        </div>
    </div>
</div>
@endsection
