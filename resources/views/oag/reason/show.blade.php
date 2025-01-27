@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('crime.reason.index') }}">Reasons for Closure</a></li>
            <li class="breadcrumb-item active" aria-current="page">Show Reason</li>
        </ol>
    </nav>

    <!-- Heading -->
    <h2 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #f8f9fa; text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.4); border-bottom: 4px double #ff6f61; padding-bottom: 20px;">
        Reason for Closure Details
    </h2>

    <!-- Reason Details -->
    <div class="p-4 shadow-lg rounded" style="background: linear-gradient(145deg, #f1f1f1, #ffffff); border: 2px solid #007bff; border-radius: 20px;">
        <div class="form-group mb-3">
            <label for="reason_description" class="font-weight-bold">Reason Description</label>
            <textarea class="form-control-plaintext" id="reason_description" rows="4" readonly>{{ $reason->reason_description }}</textarea>
        </div>

        <!-- Action Buttons -->
        <div class="text-center">
            <a href="{{ route('crime.reason.edit', $reason->id) }}" class="btn btn-warning btn-lg px-4" style="border-radius: 30px; font-weight: bold; background: radial-gradient(circle at top left, #ff6f61, #de1c1c); color: #fff; transition: background 0.4s, transform 0.3s;">
                Edit
            </a>
            <a href="{{ route('crime.reason.index') }}" class="btn btn-secondary btn-lg px-4" style="border-radius: 30px; font-weight: bold; background: #6c757d; color: #fff; transition: background 0.4s, transform 0.3s;">
                Back to List
            </a>
        </div>
    </div>
</div>
@endsection
