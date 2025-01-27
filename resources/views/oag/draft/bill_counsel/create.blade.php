@extends('layouts.app')

@section('content')
<div class="container mt-5">

    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('bills.index') }}">Bills</a></li>
            <li class="breadcrumb-item"><a href="{{ route('bills.show', $bill->id) }}">Bill Details</a></li>
            <li class="breadcrumb-item active" aria-current="page">Assign Counsel</li>
        </ol>
    </nav>

    <h1 class="text-center mb-4">Assign Counsel to Bill</h1>

    <form action="{{ route('bill_counsel.store', $bill->id) }}" method="POST" class="p-4 shadow-lg rounded" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); border-radius: 20px;">
        @csrf
        
        <!-- Counsel -->
        <div class="form-group">
            <label for="counsel_id" class="text-white">Counsel</label>
            <select class="form-control @error('counsel_id') is-invalid @enderror" id="counsel_id" name="counsel_id" required>
                @foreach($counsels as $counsel)
                    <option value="{{ $counsel->id }}" {{ old('counsel_id') == $counsel->id ? 'selected' : '' }}>
                        {{ $counsel->name }} ({{ $counsel->position }})
                    </option>
                @endforeach
            </select>
            @error('counsel_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Assigned Date -->
        <div class="form-group">
            <label for="assigned_date" class="text-white">Assigned Date</label>
            <input type="date" class="form-control @error('assigned_date') is-invalid @enderror" id="assigned_date" name="assigned_date" value="{{ old('assigned_date') }}" required>
            @error('assigned_date')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Due Date -->
        <div class="form-group">
            <label for="due_date" class="text-white">Due Date</label>
            <input type="date" class="form-control @error('due_date') is-invalid @enderror" id="due_date" name="due_date" value="{{ old('due_date') }}">
            @error('due_date')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Role -->
        <div class="form-group">
            <label for="role" class="text-white">Role</label>
            <select class="form-control @error('role') is-invalid @enderror" id="role" name="role" required>
                <option value="Lead" {{ old('role') == 'Lead' ? 'selected' : '' }}>Lead</option>
                <option value="Support" {{ old('role') == 'Support' ? 'selected' : '' }}>Support</option>
                <option value="Review" {{ old('role') == 'Review' ? 'selected' : '' }}>Review</option>
            </select>
            @error('role')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary btn-block mt-4">Assign Counsel</button>
    </form>
</div>
@endsection
