@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('civil.courtcategory.index') }}">Court Categories</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $courtCategory->name }}</li>
        </ol>
    </nav>
    
    <!-- Heading -->
    <h1 class="text-center mb-4">Court Category Details</h1>

   <!-- Details -->
   <div class="card shadow-lg">
        <div class="card-body">
            <h5 class="card-title">Category Name:</h5>
            <p class="card-text">{{ $courtCategory->name }}</p>

            <h5 class="card-title">Category Code:</h5>
            <p class="card-text">{{ $courtCategory->code }}</p>

            <h5 class="card-title">Created By:</h5>
            <p class="card-text">{{ optional($courtCategory->createdBy)->name ?? 'N/A' }}</p>

            <h5 class="card-title">Updated By:</h5>
            <p class="card-text">{{ optional($courtCategory->updatedBy)->name ?? 'N/A' }}</p>

            <h5 class="card-title">Created At:</h5>
            <p class="card-text">{{ $courtCategory->created_at ? $courtCategory->created_at->format('d-m-Y H:i:s') : 'N/A' }}</p>

            <h5 class="card-title">Updated At:</h5>
            <p class="card-text">{{ $courtCategory->updated_at ? $courtCategory->updated_at->format('d-m-Y H:i:s') : 'N/A' }}</p>

            <a href="{{ route('civil.courtcategory.edit', $courtCategory->id) }}" class="btn btn-primary mt-3">Edit</a>
            <a href="{{ route('civil.courtcategory.index') }}" class="btn btn-secondary mt-3">Back to List</a>
        </div>
    </div>
</div>
@endsection
