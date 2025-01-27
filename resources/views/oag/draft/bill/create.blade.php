@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('draft.bills.index') }}">Bills</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create New Bill</li>
        </ol>
    </nav>
    
    <h1 class="text-center mb-4">Create New Bill</h1>

    <form action="{{ route('draft.bills.store') }}" method="POST" class="p-4 shadow-lg rounded">
        @csrf

        <!-- Name -->
        <div class="form-group">
            <label for="name">Bill Name</label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <!-- Receipt Date -->
        <div class="form-group">
            <label for="receipt_date">Receipt Date</label>
            <input type="date" name="receipt_date" id="receipt_date" class="form-control @error('receipt_date') is-invalid @enderror" value="{{ old('receipt_date') }}" required>
            @error('receipt_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <!-- Ministry -->
        <div class="form-group">
            <label for="ministry_id">Ministry</label>
            <select class="form-control @error('ministry_id') is-invalid @enderror" name="ministry_id" required>
                <option value="">Select Ministry</option>
                @foreach($ministries as $id => $ministry)
                    <option value="{{ $id }}" {{ old('ministry_id') == $id ? 'selected' : '' }}>{{ $ministry }}</option>
                @endforeach
            </select>
            @error('ministry_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <!-- Status -->
        <div class="form-group">
            <label for="status">Status</label>
            <select class="form-control @error('status') is-invalid @enderror" name="status">
                @foreach(['Draft', 'First Reading', 'Second Reading', 'Third Reading', 'Passed', 'Rejected'] as $status)
                    <option value="{{ $status }}" {{ old('status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                @endforeach
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <!-- Priority -->
        <div class="form-group">
            <label for="priority">Priority</label>
            <select class="form-control @error('priority') is-invalid @enderror" name="priority">
                @foreach(['Normal', 'Urgent', 'High Priority'] as $priority)
                    <option value="{{ $priority }}" {{ old('priority') == $priority ? 'selected' : '' }}>{{ $priority }}</option>
                @endforeach
            </select>
            @error('priority')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <!-- Task -->
        <div class="form-group">
            <label for="task">Task</label>
            <input type="text" name="task" id="task" class="form-control @error('task') is-invalid @enderror" value="{{ old('task') }}">
            @error('task')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <!-- Progress Status -->
        <div class="form-group">
            <label for="progress_status">Progress Status</label>
            <select class="form-control @error('progress_status') is-invalid @enderror" name="progress_status">
                @foreach(['Not Started', 'Ongoing', 'Achieved'] as $progress)
                    <option value="{{ $progress }}" {{ old('progress_status') == $progress ? 'selected' : '' }}>{{ $progress }}</option>
                @endforeach
            </select>
            @error('progress_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <!-- Comments -->
        <div class="form-group">
            <label for="comments">Comments</label>
            <textarea name="comments" id="comments" class="form-control @error('comments') is-invalid @enderror">{{ old('comments') }}</textarea>
            @error('comments')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <!-- Target Completion Date -->
        <div class="form-group">
            <label for="target_completion_date">Target Completion Date</label>
            <input type="date" name="target_completion_date" id="target_completion_date" class="form-control @error('target_completion_date') is-invalid @enderror" value="{{ old('target_completion_date') }}">
            @error('target_completion_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <!-- Actual Completion Date -->
        <div class="form-group">
            <label for="actual_completion_date">Actual Completion Date</label>
            <input type="date" name="actual_completion_date" id="actual_completion_date" class="form-control @error('actual_completion_date') is-invalid @enderror" value="{{ old('actual_completion_date') }}">
            @error('actual_completion_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <!-- Version -->
        <div class="form-group">
            <label for="version">Version</label>
            <input type="text" name="version" id="version" class="form-control @error('version') is-invalid @enderror" value="{{ old('version', '1.0') }}">
            @error('version')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <!-- Submit -->
        <button type="submit" class="btn btn-primary">Create Bill</button>
    </form>
</div>
@endsection
