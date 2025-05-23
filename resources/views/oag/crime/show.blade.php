@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
        {{ Breadcrumbs::render() }}
        </ol>
    </nav>

    <!-- Heading -->
    <h2 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #f8f9fa; text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.4); border-bottom: 4px double #ff6f61; padding-bottom: 20px;">
        Criminal Case Details
    </h2>

    <!-- Criminal Case Details -->
    <div class="card shadow-lg rounded-3 overflow-hidden" style="background: linear-gradient(145deg, #f1f1f1, #ffffff); border: 2px solid #007bff; border-radius: 20px;">
        <div class="card-body">
            <div class="mb-3">
                <strong>Case File Number:</strong>
                <p>{{ $criminalCase->case_file_number }}</p>
            </div>
            <div class="mb-3">
                <strong>Case Name:</strong>
                <p>{{ $criminalCase->case_name }}</p>
            </div>
            <div class="mb-3">
                <strong>Date File Received:</strong>
                <p>{{ \Carbon\Carbon::parse($criminalCase->date_file_received)->format('d/m/Y') }}</p>
            </div>
            <div class="mb-3">
                <strong>Date of Allocation:</strong>
                <p>{{ \Carbon\Carbon::parse($criminalCase->date_of_allocation)->format('d/m/Y') }}</p>
            </div>
            <div class="mb-3">
                <strong>Reason for Closure:</strong>
                <p>{{ $criminalCase->reason_description ?? 'N/A' }}</p>
            </div>
            <div class="mb-3">
                <strong>Island Name:</strong>
                <p>{{ $criminalCase->island_name ?? 'N/A' }}</p>
            </div>
            <div class="mb-3">
                <strong>Council Name:</strong>
                <p>{{ $criminalCase->lawyer_name ?? 'N/A' }}</p>
            </div>
            
            <!-- Actions -->
            <div class="text-center">
                <a href="{{ route('crime.criminalCase.edit', $criminalCase->id) }}" class="btn btn-primary btn-lg" style="background: radial-gradient(circle at top left, #ff6f61, #de1c1c); color: #fff; border-radius: 30px; font-weight: bold; box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3); transition: background 0.4s, transform 0.3s; transform: perspective(1px) translateZ(0);">
                    Edit
                </a>
                <a href="{{ route('crime.criminalCase.index') }}" class="btn btn-secondary btn-lg ms-3" style="border-radius: 30px; font-weight: bold; box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3); transition: background 0.4s, transform 0.3s; transform: perspective(1px) translateZ(0);">
                    Back to List
                </a>
                <form action="{{ route('crime.criminalCase.destroy', $criminalCase->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this case?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-lg ms-3" style="border-radius: 30px; font-weight: bold; box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3); transition: background 0.4s, transform 0.3s; transform: perspective(1px) translateZ(0);">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
