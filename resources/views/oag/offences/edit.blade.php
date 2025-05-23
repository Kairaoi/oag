@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        
        {{ Breadcrumbs::render() }}
        
    </nav>

    <!-- Heading -->
    <h1 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #333; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);">Edit Offence</h1>

    <!-- Form -->
    <form action="{{ route('crime.offence.update', $offence->id) }}" method="POST" class="p-4 shadow-lg rounded" style="background: linear-gradient(90deg, #007bff, #00c6ff); border-radius: 20px;">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="offence_name" class="text-white">Offence Name</label>
            <input type="text" class="form-control @error('offence_name') is-invalid @enderror" id="offence_name" name="offence_name" value="{{ old('offence_name', $offence->offence_name) }}" required>
            @error('offence_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group">
            <label for="offence_category_id" class="text-white">Offence Category</label>
            <select class="form-control @error('offence_category_id') is-invalid @enderror" id="offence_category_id" name="offence_category_id" required>
                <option value="">Select a category</option>
                @foreach($categories as $id => $name)
                    <option value="{{ $id }}" {{ old('offence_category_id', $offence->offence_category_id) == $id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
            @error('offence_category_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <button type="submit" class="btn btn-light btn-lg btn-block" style="border-radius: 30px; font-weight: bold; background-color: #ffffff; color: #007bff;">Update</button>
    </form>
</div>
@endsection
