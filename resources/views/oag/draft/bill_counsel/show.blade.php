@extends('layouts.app')

@section('content')
<div class="container mt-5">

    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('counsels.index') }}">Counsels</a></li>
            <li class="breadcrumb-item active" aria-current="page">View Counsel</li>
        </ol>
    </nav>

    <h1 class="text-center mb-4">Counsel Details</h1>

    <div class="card p-4 shadow-lg rounded" style="background: linear-gradient(90deg, #ff416c, #ff4b2b);">
        <!-- Display counsel details -->
        <p><strong>Name:</strong> {{ $counsel->name }}</p>
        <p><strong>Position:</strong> {{ $counsel->position }}</p>
        <p><strong>Max Assignments:</strong> {{ $counsel->max_assignments }}</p>
        <p><strong>Is Active:</strong> {{ $counsel->is_active ? 'Yes' : 'No' }}</p>
    </div>

    <a href="{{ route('counsels.index') }}" class="btn btn-secondary btn-block mt-4">Back to List</a>
</div>
@endsection
