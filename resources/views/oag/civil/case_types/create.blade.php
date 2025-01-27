@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('civil.casetype.index') }}">Case Types</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create New Case Type</li>
        </ol>
    </nav>
    
    <!-- Heading -->
    <h1 class="text-center mb-4">Create New Case Type</h1>

    <!-- Form -->
    <form action="{{ route('civil.casetype.store') }}" method="POST" class="p-4 shadow-lg rounded bg-light">
        @csrf
        <div class="form-group">
            <label for="name">Case Type Name</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
            @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary btn-lg btn-block mt-3">Create</button>
    </form>
</div>
@endsection
