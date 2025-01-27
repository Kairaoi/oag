@extends('layouts.app')

@section('content')
<div class="container mt-5">

    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('regulations.index') }}">Regulations</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create Regulation</li>
        </ol>
    </nav>

    <h1 class="text-center mb-4">Create New Regulation</h1>

    <form action="{{ route('regulations.store') }}" method="POST" class="p-4 shadow-lg rounded" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); border-radius: 20px;">
        @csrf
        
        <!-- Name -->
        <div class="form-group">
            <label for="name" class="text-white">Regulation Name</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
            @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Receipt Date -->
        <div class="form-group">
            <label for="receipt_date" class="text-white">Receipt Date</label>
            <input type="date" class="form-control @error('receipt_date') is-invalid @enderror" id="receipt_date" name="receipt_date" value="{{ old('receipt_date') }}" required>
            @error('receipt_date')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Ministry -->
        <div class="form-group">
            <label for="ministry_id" class="text-white">Ministry</label>
            <select class="form-control @error('ministry_id') is-invalid @enderror" id="ministry_id" name="ministry_id" required>
                <option value="">Select Ministry</option>
                @foreach($ministries as $ministry)
                    <option value="{{ $ministry->id }}" {{ old('ministry_id') == $ministry->id ? 'selected' : '' }}>{{ $ministry->name }}</option>
                @endforeach
            </select>
            @error('ministry_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Status -->
        <div class="form-group">
            <label for="status" class="text-white">Status</label>
            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
                <option value="Pending" {{ old('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="In Review" {{ old('status') == 'In Review' ? 'selected' : '' }}>In Review</option>
                <option value="Approved" {{ old('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                <option value="Published" {{ old('status') == 'Published' ? 'selected' : '' }}>Published</option>
                <option value="Rejected" {{ old('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            @error('status')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Priority -->
        <div class="form-group">
            <label for="priority" class="text-white">Priority</label>
            <select class="form-control @error('priority') is-invalid @enderror" id="priority" name="priority">
                <option value="Normal" {{ old('priority') == 'Normal' ? 'selected' : '' }}>Normal</option>
                <option value="Urgent" {{ old('priority') == 'Urgent' ? 'selected' : '' }}>Urgent</option>
                <option value="High Priority" {{ old('priority') == 'High Priority' ? 'selected' : '' }}>High Priority</option>
            </select>
            @error('priority')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Comments -->
        <div class="form-group">
            <label for="comments" class="text-white">Comments</label>
            <textarea class="form-control @error('comments') is-invalid @enderror" id="comments" name="comments">{{ old('comments') }}</textarea>
            @error('comments')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Target Completion Date -->
        <div class="form-group">
            <label for="target_completion_date" class="text-white">Target Completion Date</label>
            <input type="date" class="form-control @error('target_completion_date') is-invalid @enderror" id="target_completion_date" name="target_completion_date" value="{{ old('target_completion_date') }}">
            @error('target_completion_date')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Actual Completion Date -->
        <div class="form-group">
            <label for="actual_completion_date" class="text-white">Actual Completion Date</label>
            <input type="date" class="form-control @error('actual_completion_date') is-invalid @enderror" id="actual_completion_date" name="actual_completion_date" value="{{ old('actual_completion_date') }}">
            @error('actual_completion_date')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Version -->
        <div class="form-group">
            <label for="version" class="text-white">Version</label>
            <input type="text" class="form-control @error('version') is-invalid @enderror" id="version" name="version" value="{{ old('version', '1.0') }}">
            @error('version')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Requires Cabinet Approval -->
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="requires_cabinet_approval" name="requires_cabinet_approval" {{ old('requires_cabinet_approval') ? 'checked' : '' }}>
            <label class="form-check-label text-white" for="requires_cabinet_approval">Requires Cabinet Approval</label>
        </div>

        <button type="submit" class="btn btn-primary btn-block mt-4">Create Regulation</button>
    </form>
</div>
@endsection
