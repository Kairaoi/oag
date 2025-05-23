@extends('layouts.app')

@section('content')
<div class="container mt-5">
   <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
    {{ Breadcrumbs::render() }}
    </nav>


    <!-- Heading -->
    <h1 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #333; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);">
        Show Victim
    </h1>

    <!-- Victim Details -->
    <div class="p-4 shadow-lg rounded" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); border-radius: 20px;">
        <div class="form-group">
            <label for="case_id" class="text-white">Case</label>
            <p class="form-control-plaintext">{{ $cases[$victim->case_id] ?? 'N/A' }}</p>
        </div>

        <div class="form-group">
            <label for="council_id" class="text-white">Council</label>
            <p class="form-control-plaintext">{{ $councils[$victim->council_id] ?? 'N/A' }}</p>
        </div>

        <div class="form-group">
            <label for="island_id" class="text-white">Island</label>
            <p class="form-control-plaintext">{{ $islands[$victim->island_id] ?? 'N/A' }}</p>
        </div>

        <div class="form-group">
            <label for="first_name" class="text-white">First Name</label>
            <p class="form-control-plaintext">{{ $victim->first_name }}</p>
        </div>

        <div class="form-group">
            <label for="last_name" class="text-white">Last Name</label>
            <p class="form-control-plaintext">{{ $victim->last_name }}</p>
        </div>

        <div class="form-group">
            <label for="victim_particulars" class="text-white">Victim Particulars</label>
            <p class="form-control-plaintext">{{ $victim->victim_particulars }}</p>
        </div>

        <div class="form-group">
            <label for="gender" class="text-white">Gender</label>
            <p class="form-control-plaintext">{{ $victim->gender }}</p>
        </div>

        <div class="form-group">
            <label for="date_of_birth" class="text-white">Date of Birth</label>
            <p class="form-control-plaintext">{{ $victim->date_of_birth }}</p>
        </div>

        <a href="{{ route('crime.victim.index') }}" class="btn btn-light btn-lg btn-block" style="border-radius: 30px; font-weight: bold;">Back to List</a>
    </div>
</div>
@endsection
