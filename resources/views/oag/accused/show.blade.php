@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
    {{ Breadcrumbs::render() }}
    </nav>

    <h1 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #333; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);">Show Accused</h1>

    <div class="p-4 shadow-lg rounded" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); border-radius: 20px;">
        <div class="form-group">
            <label for="case_id" class="text-white">Case</label>
            <p class="form-control-plaintext">{{ $cases[$accused->case_id] ?? 'N/A' }}</p>
        </div>

        <div class="form-group">
            <label for="council_id" class="text-white">Council</label>
            <p class="form-control-plaintext">{{ $councils[$accused->council_id] ?? 'N/A' }}</p>
        </div>

        <div class="form-group">
            <label for="island_id" class="text-white">Island</label>
            <p class="form-control-plaintext">{{ $islands[$accused->island_id] ?? 'N/A' }}</p>
        </div>

        <div class="form-group">
            <label for="first_name" class="text-white">First Name</label>
            <p class="form-control-plaintext">{{ $accused->first_name }}</p>
        </div>

        <div class="form-group">
            <label for="last_name" class="text-white">Last Name</label>
            <p class="form-control-plaintext">{{ $accused->last_name }}</p>
        </div>

        <div class="form-group">
            <label for="accused_particulars" class="text-white">Accused Particulars</label>
            <p class="form-control-plaintext">{{ $accused->accused_particulars }}</p>
        </div>

        <div class="form-group">
            <label for="gender" class="text-white">Gender</label>
            <p class="form-control-plaintext">{{ $accused->gender }}</p>
        </div>

        <div class="form-group">
            <label for="date_of_birth" class="text-white">Date of Birth</label>
            <p class="form-control-plaintext">{{ $accused->date_of_birth }}</p>
        </div>

        <a href="{{ route('crime.accused.index') }}" class="btn btn-light btn-lg btn-block" style="border-radius: 30px; font-weight: bold;">Back to List</a>
    </div>
</div>
@endsection
