@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('civil.casetype.index') }}">Case Types</a></li>
            <li class="breadcrumb-item active" aria-current="page">Show Case Type</li>
        </ol>
    </nav>
    
    <!-- Heading -->
    <h1 class="text-center mb-4">Show Case Type</h1>

    <!-- Case Type Details -->
    <div class="card shadow-lg rounded bg-light p-4">
        <div class="form-group">
            <label for="name">Case Type Name</label>
            <p>{{ $caseType->name }}</p>
        </div>
        <div class="form-group">
            <label for="created_by">Created By</label>
            <p>{{ $caseType->createdBy ? $caseType->createdBy->name : 'N/A' }}</p>
        </div>
        <div class="form-group">
            <label for="updated_by">Updated By</label>
            <p>{{ $caseType->updatedBy ? $caseType->updatedBy->name : 'N/A' }}</p>
        </div>
        <div class="form-group">
            <label for="created_at">Created At</label>
            <p>{{ $caseType->created_at }}</p>
        </div>
        <div class="form-group">
            <label for="updated_at">Updated At</label>
            <p>{{ $caseType->updated_at }}</p>
        </div>
        <div class="form-group">
            <label for="deleted_at">Deleted At</label>
            <p>{{ $caseType->deleted_at ?? 'N/A' }}</p>
        </div>
    </div>

    <div class="mt-4 text-center">
        <a href="{{ route('civil.casetype.index') }}" class="btn btn-secondary btn-lg">Back to Case Types</a>
    </div>
</div>
@endsection
