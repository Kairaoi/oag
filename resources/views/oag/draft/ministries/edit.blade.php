@extends('layouts.app')

@section('content')
<div class="container mt-5">

    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('draft.ministry.index') }}">Ministries</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Ministry</li>
        </ol>
    </nav>

    <h1 class="text-center mb-4">Edit Ministry</h1>

    <form action="{{ route('draft.ministry.update', $ministry->id) }}" method="POST" class="p-4 shadow-lg rounded" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); border-radius: 20px;">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="name" class="text-white">Ministry Name</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $ministry->name) }}" required>
            @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group">
            <label for="code" class="text-white">Ministry Code</label>
            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $ministry->code) }}" required>
            @error('code')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" {{ old('is_active', $ministry->is_active) ? 'checked' : '' }}>
            <label class="form-check-label text-white" for="is_active">Is Active</label>
        </div>

        <button type="submit" class="btn btn-primary btn-block mt-4">Update Ministry</button>
    </form>
</div>
@endsection
