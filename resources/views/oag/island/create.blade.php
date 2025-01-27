@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('crime.island.index') }}">Islands</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create New Island</li>
        </ol>
    </nav>

    <!-- Main Content -->
    <h1 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #333; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);">Create New Island</h1>

    <form action="{{ route('crime.island.store') }}" method="POST" class="p-4 shadow-lg rounded-3" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); border-radius: 20px;">
        @csrf
        <div class="form-group mb-3">
            <label for="island_name" class="text-white" style="font-weight: bold;">Island Name</label>
            <input type="text" class="form-control @error('island_name') is-invalid @enderror" id="island_name" name="island_name" value="{{ old('island_name') }}" required>
            @error('island_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <button type="submit" class="btn btn-light btn-lg btn-block" style="border-radius: 30px; font-weight: bold; background: radial-gradient(circle, #ffffff, #e0e0e0); border: 1px solid #ddd; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); transition: background 0.3s, transform 0.3s;">
            Create
        </button>
    </form>
</div>
@endsection
