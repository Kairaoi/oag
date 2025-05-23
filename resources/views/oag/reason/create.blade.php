@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        
        {{ Breadcrumbs::render() }}
        
    </nav>

    <!-- Heading -->
    <h1 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #333; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);">Create Reason for Closure</h1>

    <!-- Create Reason Form -->
    <form action="{{ route('crime.reason.store') }}" method="POST" class="p-4 shadow-lg rounded" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); border-radius: 20px;">
        @csrf
        <div class="form-group">
            <label for="reason_description" class="text-white">Reason Description</label>
            <textarea class="form-control @error('reason_description') is-invalid @enderror" id="reason_description" name="reason_description" rows="4" required>{{ old('reason_description') }}</textarea>
            @error('reason_description')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-light btn-lg btn-block" style="border-radius: 30px; font-weight: bold;">Create</button>
    </form>
</div>
@endsection
