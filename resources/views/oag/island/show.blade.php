@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('crime.island.index') }}">Islands</a></li>
            <li class="breadcrumb-item active" aria-current="page">Island Details</li>
        </ol>
    </nav>
    
    <!-- Heading -->
    <h1 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #333; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);">Island Details</h1>

    <!-- Island Details Card -->
    <div class="card shadow-lg rounded" style="border: 2px solid #007bff; border-radius: 20px;">
        <div class="card-header text-white" style="background: linear-gradient(90deg, #007bff, #00c6ff);">
            <h5>Island Name: <strong>{{ $island->island_name }}</strong></h5>
        </div>
        <div class="card-body">
            <p><strong>Created By:</strong> {{ $island->creator->name ?? 'N/A' }}</p>
            <p><strong>Updated By:</strong> {{ $island->updater->name ?? 'N/A' }}</p>
            <p><strong>Created At:</strong> {{ $island->created_at ? $island->created_at->format('Y-m-d') : 'N/A' }}</p>
            <p><strong>Updated At:</strong> {{ $island->updated_at ? $island->updated_at->format('Y-m-d') : 'N/A' }}</p>
        </div>
        <div class="card-footer text-center">
            <a href="{{ route('crime.island.index') }}" class="btn btn-secondary btn-lg" style="border-radius: 30px;">Back to List</a>
            <a href="{{ route('crime.island.edit', $island->id) }}" class="btn btn-warning btn-lg" style="border-radius: 30px;">Edit</a>
            <form action="{{ route('crime.island.destroy', $island->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-lg" style="border-radius: 30px;" onclick="return confirm('Are you sure you want to delete this island?')">Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection
