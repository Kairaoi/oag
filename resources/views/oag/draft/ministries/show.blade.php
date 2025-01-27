@extends('layouts.app')

@section('content')
<div class="container mt-5">

    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('draft.ministry.index') }}">Ministries</a></li>
            <li class="breadcrumb-item active" aria-current="page">View Ministry</li>
        </ol>
    </nav>

    <h1 class="text-center mb-4">Ministry Details</h1>

    <div class="card p-4 shadow-lg rounded" style="background: linear-gradient(90deg, #ff416c, #ff4b2b);">
        <div class="form-group">
            <label for="name" class="text-white">Ministry Name</label>
            <p>{{ $ministry->name }}</p>
        </div>

        <div class="form-group">
            <label for="code" class="text-white">Ministry Code</label>
            <p>{{ $ministry->code }}</p>
        </div>

        <div class="form-group">
            <label for="is_active" class="text-white">Is Active</label>
            <p>{{ $ministry->is_active ? 'Yes' : 'No' }}</p>
        </div>

        <div class="form-group">
            <label for="created_at" class="text-white">Created At</label>
            <p>{{ $ministry->created_at }}</p>
        </div>

        <div class="form-group">
            <label for="updated_at" class="text-white">Updated At</label>
            <p>{{ $ministry->updated_at }}</p>
        </div>

        <a href="{{ route('draft.ministry.index') }}" class="btn btn-secondary btn-block mt-4">Back to List</a>
    </div>
</div>
@endsection
