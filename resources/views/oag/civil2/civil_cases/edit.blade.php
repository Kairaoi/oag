@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb" style="background: none; box-shadow: none;">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color: #ff4b2b; font-weight: bold;">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('civil.civilcase.index') }}" style="color: #ff4b2b; font-weight: bold;">Civil Cases</a></li>
            <li class="breadcrumb-item active" aria-current="page" style="color: #333; font-weight: bold;">Edit Civil Case</li>
        </ol>
    </nav>

    <!-- Main Title -->
    <h1 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #333; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);">
        Edit Civil Case
    </h1>

    <!-- Form -->
    <form action="{{ route('civil.civilcase.update', $civilCase->id) }}" method="POST" class="p-4 shadow-lg rounded" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); border-radius: 20px;">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Left Column -->
            <div class="col-md-6">
                <!-- Court Category -->
                <div class="form-group">
                    <label for="court_category_id" class="text-white">Court Category *</label>
                    <select class="form-control @error('court_category_id') is-invalid @enderror" id="court_category_id" name="court_category_id" required>
                        <option value="">Select Court Category</option>
                        @foreach($courtCategories as $id => $name)
                            <option value="{{ $id }}" {{ $civilCase->court_category_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('court_category_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Case Type -->
                <div class="form-group">
                    <label for="case_type_id" class="text-white">Case Type *</label>
                    <select class="form-control @error('case_type_id') is-invalid @enderror" id="case_type_id" name="case_type_id" required>
                        <option value="">Select Case Type</option>
                        @foreach($caseTypes as $id => $name)
                            <option value="{{ $id }}" {{ $civilCase->case_type_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('case_type_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Case Number Details -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="number" class="text-white">Case Number</label>
                            <input type="number" class="form-control @error('number') is-invalid @enderror" id="number" name="number" value="{{ old('number', $civilCase->number) }}">
                            @error('number')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="year" class="text-white">Year *</label>
                            <input type="number" class="form-control @error('year') is-invalid @enderror" id="year" name="year" value="{{ old('year', $civilCase->year) }}" required>
                            @error('year')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="primary_number" class="text-white">Primary Number</label>
                            <input type="text" class="form-control @error('primary_number') is-invalid @enderror" id="primary_number" name="primary_number" value="{{ old('primary_number', $civilCase->primary_number) }}" readonly>
                            @error('primary_number')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Case Name -->
                <div class="form-group">
                    <label for="case_name" class="text-white">Case Name *</label>
                    <textarea class="form-control @error('case_name') is-invalid @enderror" id="case_name" name="case_name" required>{{ old('case_name', $civilCase->case_name) }}</textarea>
                    @error('case_name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Case Description -->
                <div class="form-group">
                    <label for="case_description" class="text-white">Case Description</label>
                    <textarea class="form-control @error('case_description') is-invalid @enderror" id="case_description" name="case_description">{{ old('case_description', $civilCase->case_description) }}</textarea>
                    @error('case_description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-md-6">
                <!-- Current Status -->
                <div class="form-group">
                    <label for="current_status" class="text-white">Current Status *</label>
                    <textarea class="form-control @error('current_status') is-invalid @enderror" id="current_status" name="current_status" required>{{ old('current_status', $civilCase->current_status) }}</textarea>
                    @error('current_status')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Status Date -->
                <div class="form-group">
                    <label for="status_date" class="text-white">Status Date *</label>
                    <input type="date" class="form-control @error('status_date') is-invalid @enderror" id="status_date" name="status_date" value="{{ old('status_date', $civilCase->status_date) }}" required>
                    @error('status_date')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Action Required -->
                <div class="form-group">
                    <label for="action_required" class="text-white">Action Required *</label>
                    <textarea class="form-control @error('action_required') is-invalid @enderror" id="action_required" name="action_required" required>{{ old('action_required', $civilCase->action_required) }}</textarea>
                    @error('action_required')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Monitoring Status -->
                <div class="form-group">
                    <label for="monitoring_status" class="text-white">Monitoring Status *</label>
                    <select class="form-control @error('monitoring_status') is-invalid @enderror" id="monitoring_status" name="monitoring_status" required>
                        <option value="">Select Status</option>
                        <option value="Active" {{ $civilCase->monitoring_status == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Pending" {{ $civilCase->monitoring_status == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Closed" {{ $civilCase->monitoring_status == 'Closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                    @error('monitoring_status')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Counsels Section -->
                <div class="card mt-3">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Case Counsels *</h5>
                    </div>
                    <div class="card-body">
                        <!-- Plaintiff Counsel -->
<div class="form-group">
    <label for="plaintiff_counsel">Plaintiff Counsel *</label>
    <select class="form-control @error('counsels.0.user_id') is-invalid @enderror" name="counsels[0][user_id]" required>
        <option value="">Select Plaintiff Counsel</option>
        @foreach($lawyers as $id => $name)
            <option value="{{ $id }}" 
                {{ isset($civilCase->counsels[0]) && $civilCase->counsels[0]->user_id == $id ? 'selected' : '' }}>
                {{ $name }}
            </option>
        @endforeach
    </select>
    <input type="hidden" name="counsels[0][type]" value="Plaintiff">
    @error('counsels.0.user_id')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>


                        <!-- Defendant Counsel -->
<div class="form-group">
    <label for="defendant_counsel">Defendant Counsel *</label>
    <select class="form-control @error('counsels.1.user_id') is-invalid @enderror" name="counsels[1][user_id]" required>
        <option value="">Select Defendant Counsel</option>
        @foreach($lawyers as $id => $name)
            <option value="{{ $id }}" 
                {{ isset($civilCase->counsels[1]) && $civilCase->counsels[1]->user_id == $id ? 'selected' : '' }}>
                {{ $name }}
            </option>
        @endforeach
    </select>
    <input type="hidden" name="counsels[1][type]" value="Defendant">
    @error('counsels.1.user_id')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Form Submit Button -->
        <div class="form-group text-center mt-4">
            <button type="submit" class="btn btn-light btn-lg" style="background-color: #333; color: #fff;">Update Civil Case</button>
        </div>
    </form>
</div>
@endsection
