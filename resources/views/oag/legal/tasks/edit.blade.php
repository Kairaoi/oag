@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow border-0 rounded-lg">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h4 class="mb-0">Edit Legal Task</h4>
                </div>

                <div class="card-body">
                    <form action="{{ route('legal.legal_tasks.update', $legalTask->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Date and Ministry -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', $legalTask->date ? $legalTask->date->toDateString() : '') }}" required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="ministry" class="form-label">Ministry</label>
                                <input type="text" class="form-control @error('ministry') is-invalid @enderror" id="ministry" name="ministry" value="{{ old('ministry', $legalTask->ministry) }}" required>
                                @error('ministry')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Task -->
                        <div class="mb-4">
                            <label for="task" class="form-label">Task</label>
                            <textarea class="form-control @error('task') is-invalid @enderror" id="task" name="task" rows="4" required>{{ old('task', $legalTask->task) }}</textarea>
                            @error('task')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Legal Advice/Meeting & Allocated Date -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="legal_advice_meeting" class="form-label">Legal Advice/Meeting</label>
                                <input type="text" class="form-control @error('legal_advice_meeting') is-invalid @enderror" id="legal_advice_meeting" name="legal_advice_meeting" value="{{ old('legal_advice_meeting', $legalTask->legal_advice_meeting) }}" required>
                                @error('legal_advice_meeting')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="allocated_date" class="form-label">Allocated Date</label>
                                <input type="date" class="form-control @error('allocated_date') is-invalid @enderror" id="allocated_date" name="allocated_date" value="{{ old('allocated_date', $legalTask->allocated_date ? $legalTask->allocated_date->toDateString() : '') }}">
                                @error('allocated_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Allocate To -->
                        <div class="form-group mb-4">
                            <label for="allocated_to" class="form-label">Allocate To</label>
                            <select name="allocated_to" id="allocated_to" class="form-select @error('allocated_to') is-invalid @enderror">
                                <option value="">Select User</option>
                                @foreach(\App\Models\User::all() as $user)
                                    <option value="{{ $user->id }}" {{ old('allocated_to', $legalTask->allocated_to) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('allocated_to')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Additional fields... -->

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary btn-lg px-4">Update Task</button>
                            <a href="{{ route('legal.legal_tasks.index') }}" class="btn btn-secondary btn-lg px-4">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }

        .card {
            border-radius: 1rem;
            background-color: #fff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .form-label {
            font-weight: 500;
            color: #495057;
        }

        .form-control {
            border-radius: 0.5rem;
            padding: 0.75rem;
        }

        .form-select {
            border-radius: 0.5rem;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            border-radius: 50px;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            border: none;
            border-radius: 50px;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .invalid-feedback {
            font-size: 0.875rem;
            color: #e63946;
        }
    </style>
@endpush
