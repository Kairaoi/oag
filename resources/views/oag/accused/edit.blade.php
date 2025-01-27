@extends('layouts.app')

@section('content')
<div class="container mt-5">

    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('crime.accused.index') }}">Accused</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Accused</li>
        </ol>
    </nav>
    
    <h1 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #333; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);">Edit Accused</h1>

    <form action="{{ route('crime.accused.update', $accused->id) }}" method="POST" class="p-4 shadow-lg rounded" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); border-radius: 20px;">
        @csrf
        @method('PUT')
        
        <!-- Case Selection -->
        <div class="form-group">
            <label for="case_id" class="text-white">Case</label>
            <select class="form-control @error('case_id') is-invalid @enderror" id="case_id" name="case_id" required>
                <option value="">Select a case</option>
                @foreach($cases as $id => $name)
                    <option value="{{ $id }}" {{ old('case_id', $accused->case_id) == $id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
            @error('case_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Council Selection -->
        <div class="form-group">
            <label for="lawyer_id" class="text-white">Council</label>
            <select class="form-control @error('lawyer_id') is-invalid @enderror" id="lawyer_id" name="lawyer_id" required>
                <option value="">Select a council</option>
                @foreach($councils as $id => $name)
                    <option value="{{ $id }}" {{ old('lawyer_id', $accused->lawyer_id) == $id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
            @error('lawyer_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Island Selection -->
        <div class="form-group">
            <label for="island_id" class="text-white">Island</label>
            <select class="form-control @error('island_id') is-invalid @enderror" id="island_id" name="island_id" required>
                <option value="">Select an island</option>
                @foreach($islands as $id => $name)
                    <option value="{{ $id }}" {{ old('island_id', $accused->island_id) == $id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
            @error('island_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- First Name -->
        <div class="form-group">
            <label for="first_name" class="text-white">First Name</label>
            <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name', $accused->first_name) }}" required>
            @error('first_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Last Name -->
        <div class="form-group">
            <label for="last_name" class="text-white">Last Name</label>
            <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name', $accused->last_name) }}" required>
            @error('last_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Accused Particulars -->
        <div class="form-group">
            <label for="accused_particulars" class="text-white">Accused Particulars</label>
            <textarea class="form-control @error('accused_particulars') is-invalid @enderror" id="accused_particulars" name="accused_particulars" rows="3" required>{{ old('accused_particulars', $accused->accused_particulars) }}</textarea>
            @error('accused_particulars')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Gender Selection -->
        <div class="form-group">
            <label for="gender" class="text-white">Gender</label>
            <select class="form-control @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                <option value="">Select gender</option>
                <option value="Male" {{ old('gender', $accused->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ old('gender', $accused->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                <option value="Other" {{ old('gender', $accused->gender) == 'Other' ? 'selected' : '' }}>Other</option>
            </select>
            @error('gender')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Date of Birth -->
        <div class="form-group">
            <label for="date_of_birth" class="text-white">Date of Birth</label>
            <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $accused->date_of_birth) }}" required>
            @error('date_of_birth')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Offences -->
        <div class="form-group">
            <label class="text-white">Offences Group By Offence Categories</label>

            <div class="row">
                @foreach($offencesByCategory as $category => $offences)
                    <div class="col-md-4 mb-3">
                        <h5 class="text-white">{{ $category }}</h5>
                        @foreach($offences as $id => $name)
                            <div class="form-check">
                                <input 
                                    type="checkbox" 
                                    name="offences[]" 
                                    id="offence_{{ $id }}" 
                                    value="{{ $id }}" 
                                    class="form-check-input @error('offences') is-invalid @enderror"
                                    {{ in_array($id, old('offences', $accused->offences->pluck('id')->toArray())) ? 'checked' : '' }}
                                >
                                <label class="form-check-label text-white" for="offence_{{ $id }}">
                                    {{ $name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>

            <!-- Checkbox for adding a custom offence -->
            <div class="form-check">
                <input type="checkbox" id="add_custom_offence" class="form-check-input">
                <label class="form-check-label text-white" for="add_custom_offence">
                    Add Custom Offence
                </label>
            </div>

            <!-- Input for custom offence -->
            <input type="text" name="custom_offence" id="custom_offence_input" class="form-control mt-2" placeholder="Enter custom offence" style="display: none;">

            @error('offences')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary btn-block">Update</button>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('add_custom_offence').addEventListener('change', function() {
