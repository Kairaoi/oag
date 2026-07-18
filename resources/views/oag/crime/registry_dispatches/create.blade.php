@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        {{ Breadcrumbs::render() }}
    </nav>

    <!-- Title -->
    <h1 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #333; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);">
        Registry Dispatch
    </h1>

    <!-- Info Card -->
    <div class="card bg-light mb-4">
        <div class="card-body">
            <h5 class="card-title">Dispatch to the High Court Registry</h5>
            <p class="card-text mb-0">Once the AG approves, the charge file is returned to the Registry for formal dispatch to the High Court.</p>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('crime.registry-dispatches.store') }}" method="POST" class="p-4 shadow-lg rounded" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); border-radius: 20px;">
        @csrf

        <!-- Case (read-only) -->
        <div class="form-group">
            <label class="text-white">Case</label>
            <input type="text" class="form-control" value="{{ $case->case_name }}" disabled>
            <input type="hidden" name="case_id" value="{{ $case->id }}">
        </div>

        <!-- Date Dispatched -->
        <div class="form-group">
            <label for="date_dispatched" class="text-white">Date Dispatched</label>
            <input type="date" name="date_dispatched" id="date_dispatched"
                   class="form-control @error('date_dispatched') is-invalid @enderror"
                   value="{{ old('date_dispatched', now()->format('Y-m-d')) }}" required>
            @error('date_dispatched')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- Dispatched To -->
        <div class="form-group">
            <label for="dispatched_to" class="text-white">Dispatched To</label>
            <input type="text" name="dispatched_to" id="dispatched_to"
                   class="form-control @error('dispatched_to') is-invalid @enderror"
                   value="{{ old('dispatched_to', 'High Court Registry') }}" required>
            @error('dispatched_to')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- Submit and Cancel -->
        <div class="row mt-4">
            <div class="col-md-6 mb-2">
                <a href="{{ route('crime.criminalCase.index') }}" class="btn btn-light btn-lg btn-block rounded-pill font-weight-bold">Cancel</a>
            </div>
            <div class="col-md-6">
                <button type="submit" class="btn btn-light btn-lg btn-block rounded-pill font-weight-bold">Dispatch</button>
            </div>
        </div>
    </form>
</div>
@endsection
