@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        
        {{ Breadcrumbs::render() }}
        
    </nav>

    <!-- Heading -->
    <h1 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #333; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2); border-bottom: 4px double #007bff; padding-bottom: 10px;">
        Offence Details
    </h1>

    <!-- Offence Details Card -->
    <div class="p-4 shadow-lg rounded-3" style="background: linear-gradient(90deg, #007bff, #00c6ff); border-radius: 20px;">
        <!-- Offence Name -->
        <div class="form-group mb-3">
            <label for="offence_name" class="text-light" style="font-weight: bold;">Offence Name</label>
            <p class="form-control-plaintext" style="font-size: 1.2em; color: #f8f9fa;">{{ $offence->offence_name }}</p>
        </div>

        <!-- Offence Category -->
        <div class="form-group mb-4">
            <label for="offence_category_id" class="text-light" style="font-weight: bold;">Offence Category</label>
            <p class="form-control-plaintext" style="font-size: 1.2em; color: #f8f9fa;">{{ $categories[$offence->offence_category_id] ?? 'N/A' }}</p>
        </div>

        <!-- Action Buttons -->
        <div class="d-grid gap-2">
            <a href="{{ route('crime.offence.edit', $offence->id) }}" class="btn btn-primary btn-lg" style="border-radius: 30px; font-weight: bold; box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2); transition: background 0.3s, transform 0.3s; background: radial-gradient(circle, #007bff, #0056b3); color: #fff;">
                Edit
            </a>
            <a href="{{ route('crime.offence.index') }}" class="btn btn-secondary btn-lg" style="border-radius: 30px; font-weight: bold; box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2); transition: background 0.3s, transform 0.3s; background: radial-gradient(circle, #6c757d, #5a6268); color: #fff;">
                Back to List
            </a>
        </div>
    </div>
</div>
@endsection
