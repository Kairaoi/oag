@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('civil.courtcategory.index') }}">Court Categories</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create New Court Category</li>
        </ol>
    </nav>

    <!-- Heading -->
    <h1 class="text-center mb-4">Create New Court Category</h1>

    <!-- Form -->
    <form action="{{ route('civil.courtcategory.store') }}" method="POST" class="p-4 shadow-lg rounded bg-light">
        @csrf
        <div class="form-group">
            <label for="name">Category Name</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
            @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        
        <div class="form-group mt-3">
            <label for="code">Category Code</label>
            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" required>
            @error('code')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        
        <button type="submit" class="btn btn-primary btn-lg btn-block mt-3">Create</button>
    </form>
</div>
@endsection
