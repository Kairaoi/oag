@extends('layouts.app')

@section('content')
<div class="container mt-5">

    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('draft.counsels.index') }}">Counsels</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create Counsel</li>
        </ol>
    </nav>

    <h1 class="text-center mb-4">Create New Counsel</h1>

    <form action="{{ route('draft.counsels.store') }}" method="POST" class="p-4 shadow-lg rounded" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); border-radius: 20px;">
        @csrf
        
        <!-- Name -->
        <div class="form-group">
            <label for="name" class="text-white">Counsel Name</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
            @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Position -->
        <div class="form-group">
            <label for="position" class="text-white">Position</label>
            <select class="form-control @error('position') is-invalid @enderror" id="position" name="position">
                <option value="DLD" {{ old('position') == 'DLD' ? 'selected' : '' }}>DLD</option>
                <option value="Senior Counsel" {{ old('position') == 'Senior Counsel' ? 'selected' : '' }}>Senior Counsel</option>
                <option value="Junior Counsel" {{ old('position') == 'Junior Counsel' ? 'selected' : '' }}>Junior Counsel</option>
                <option value="AG" {{ old('position') == 'AG' ? 'selected' : '' }}>AG</option>
            </select>
            @error('position')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Max Assignments -->
        <div class="form-group">
            <label for="max_assignments" class="text-white">Max Assignments</label>
            <input type="number" class="form-control @error('max_assignments') is-invalid @enderror" id="max_assignments" name="max_assignments" value="{{ old('max_assignments', 5) }}" required>
            @error('max_assignments')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Is Active -->
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" {{ old('is_active') ? 'checked' : '' }}>
            <label class="form-check-label text-white" for="is_active">Is Active</label>
        </div>

        <button type="submit" class="btn btn-primary btn-block mt-4">Create Counsel</button>
    </form>
</div>
@endsection
