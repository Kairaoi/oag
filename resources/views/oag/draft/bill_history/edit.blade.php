@extends('layouts.app')

@section('content')
<div class="container mt-5">

    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('counsels.index') }}">Counsels</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Counsel</li>
        </ol>
    </nav>

    <h1 class="text-center mb-4">Edit Counsel</h1>

    <form action="{{ route('counsels.update', $counsel->id) }}" method="POST" class="p-4 shadow-lg rounded" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); border-radius: 20px;">
        @csrf
        @method('PUT')

        <!-- Similar form fields as create.blade.php -->
        <!-- Pre-fill the fields with existing values from the $counsel variable -->
        <!-- e.g., {{ old('name', $counsel->name) }} for the name field -->
        
        <button type="submit" class="btn btn-primary btn-block mt-4">Update Counsel</button>
    </form>
</div>
@endsection
