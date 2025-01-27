@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('bills.index') }}">Bills</a></li>
            <li class="breadcrumb-item active" aria-current="page">Show Bill</li>
        </ol>
    </nav>
    
    <h1 class="text-center mb-4">Bill Details</h1>

    <div class="card shadow-lg">
        <div class="card-body">
            <h5><strong>Bill Name:</strong> {{ $bill->name }}</h5>
            <p><strong>Receipt Date:</strong> {{ $bill->receipt_date }}</p>
            <p><strong>Ministry:</strong> {{ $bill->ministry->name }}</p>
            <p><strong>Status:</strong> {{ $bill->status }}</p>
            <p><strong>Priority:</strong> {{ $bill->priority }}</p>
            <p><strong>Task:</strong> {{ $bill->task }}</p>
            <p><strong>Progress Status:</strong> {{ $bill->progress_status }}</p>
            <p><strong>Comments:</strong> {{ $bill->comments }}</p>
            <p><strong>Target Completion Date:</strong> {{ $bill->target_completion_date }}</p>
            <p><strong>Actual Completion Date:</strong> {{ $bill->actual_completion_date }}</p>
        </div>
    </div>

    <a href="{{ route('bills.index') }}" class="btn btn-secondary mt-3">Back to Bills</a>
</div>
@endsection
