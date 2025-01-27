@extends('layouts.app')

@section('content')
<div class="container mt-5">

    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('regulations.index') }}">Regulations</a></li>
            <li class="breadcrumb-item active" aria-current="page">View Regulation</li>
        </ol>
    </nav>

    <h1 class="text-center mb-4">Regulation Details</h1>

    <div class="card p-4 shadow-lg rounded" style="background: linear-gradient(90deg, #ff416c, #ff4b2b);">
        <!-- Display regulation details -->
        <!-- Example: -->
        <p><strong>Name:</strong> {{ $regulation->name }}</p>
        <p><strong>Receipt Date:</strong> {{ $regulation->receipt_date }}</p>
        <p><strong>Status:</strong> {{ $regulation->status }}</p>
        <p><strong>Priority:</strong> {{ $regulation->priority }}</p>
        <!-- Add other fields like comments, ministry, etc. -->
    </div>

    <a href="{{ route('regulations.index') }}" class="btn btn-secondary btn-block mt-4">Back to List</a>
</div>
@endsection
