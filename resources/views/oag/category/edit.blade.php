@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
   <!-- Breadcrumbs -->
   <nav aria-label="breadcrumb">
    {{ Breadcrumbs::render() }}
    </nav>

    <h1 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #333; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);">Edit Offence Category</h1>

    <form action="{{ route('crime.category.update', $offenceCategory->id) }}" method="POST" class="p-4 shadow-lg rounded" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); border-radius: 20px;">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="category_name" class="text-white">Category Name</label>
            <input type="text" class="form-control @error('category_name') is-invalid @enderror" id="category_name" name="category_name" value="{{ old('category_name', $offenceCategory->category_name) }}" required>
            @error('category_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Note: The created_by and updated_by fields are handled automatically, so no need for user input -->

        <button type="submit" class="btn btn-light btn-lg btn-block" style="border-radius: 30px; font-weight: bold;">Update</button>
    </form>
</div>
@endsection
