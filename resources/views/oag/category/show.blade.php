@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
    {{ Breadcrumbs::render() }}
    </nav>

    <h1 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #333; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);">Offence Category Details</h1>

    <div class="p-4 shadow-lg rounded-3" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); border-radius: 20px;">
        <div class="form-group mb-3">
            <label for="category_name" class="text-white" style="font-weight: bold;">Category Name</label>
            <input type="text" class="form-control" id="category_name" name="category_name" value="{{ $offenceCategory->category_name }}" readonly>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('crime.category.edit', $offenceCategory->id) }}" class="btn btn-light btn-lg" style="border-radius: 30px; font-weight: bold; background: radial-gradient(circle, #ffffff, #e0e0e0); border: 1px solid #ddd; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); transition: background 0.3s, transform 0.3s;">
                Edit
            </a>
            <form action="{{ route('crime.category.destroy', $offenceCategory->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-lg" style="border-radius: 30px; font-weight: bold; background: radial-gradient(circle, #dc3545, #c82333); border: 1px solid #b21f2d; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); transition: background 0.3s, transform 0.3s;">
                    Delete
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
