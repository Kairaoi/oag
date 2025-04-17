@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('crime.victim.index') }}">Victims</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create New Victim</li>
        </ol>
    </nav>

    <h1 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #333; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);">Create New Victim</h1>

    <form action="{{ route('crime.victim.store') }}" method="POST" class="p-4 shadow-lg rounded" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); border-radius: 20px;">
        @csrf

        @if(isset($selected_case_id))
        <input type="hidden" name="from_case" value="1">
        @endif

        <!-- Case Selection -->
        <div class="form-group">
            <label for="case_id" class="text-white">Case</label>
            <select class="form-control @error('case_id') is-invalid @enderror" id="case_id" name="case_id" required>
                <option value="">Select a case</option>
                @foreach($cases as $id => $name)
                    <option value="{{ $id }}" {{ old('case_id', $selected_case_id ?? '') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
            @error('case_id') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
        </div>

      

        <!-- Island -->
        <div class="form-group">
            <label for="island_id" class="text-white">Island</label>
            <select class="form-control @error('island_id') is-invalid @enderror" id="island_id" name="island_id" required>
                <option value="">Select an island</option>
                @foreach($islands as $id => $name)
                    <option value="{{ $id }}" {{ old('island_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
            @error('island_id') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
        </div>

        <!-- First Name -->
        <div class="form-group">
            <label for="first_name" class="text-white">First Name</label>
            <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name') }}" required>
            @error('first_name') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
        </div>

        <!-- Last Name -->
        <div class="form-group">
            <label for="last_name" class="text-white">Last Name</label>
            <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name') }}" required>
            @error('last_name') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
        </div>

        <!-- Address -->
        <div class="form-group">
            <label for="address" class="text-white">Address</label>
            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address">{{ old('address') }}</textarea>
            @error('address') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
        </div>

        <!-- Contact -->
        <div class="form-group">
            <label for="contact" class="text-white">Contact</label>
            <input type="text" class="form-control @error('contact') is-invalid @enderror" id="contact" name="contact" value="{{ old('contact') }}">
            @error('contact') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
        </div>

        <!-- Phone -->
        <div class="form-group">
            <label for="phone" class="text-white">Phone</label>
            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
            @error('phone') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
        </div>

        <!-- Gender -->
        <div class="form-group">
            <label for="gender" class="text-white">Gender</label>
            <select class="form-control @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                <option value="">Select gender</option>
                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
            </select>
            @error('gender') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
        </div>

        <!-- Age -->
        <div class="form-group">
            <label for="age" class="text-white">Age</label>
            <input type="text" class="form-control @error('age') is-invalid @enderror" id="age" name="age" value="{{ old('age') }}" required>
            @error('age') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
        </div>

        <!-- DOB -->
        <div class="form-group">
            <label for="date_of_birth" class="text-white">Date of Birth</label>
            <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
            @error('date_of_birth') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
        </div>

        <!-- Age Group -->
        <div class="form-group">
            <label for="age_group" class="text-white">Age Group</label>
            <select class="form-control @error('age_group') is-invalid @enderror" id="age_group" name="age_group">
                <option value="">Select age group</option>
                <option value="Under 13" {{ old('age_group') == 'Under 13' ? 'selected' : '' }}>Under 13</option>
                <option value="Under 15" {{ old('age_group') == 'Under 15' ? 'selected' : '' }}>Under 15</option>
                <option value="Under 18" {{ old('age_group') == 'Under 18' ? 'selected' : '' }}>Under 18</option>
                <option value="Above 18" {{ old('age_group') == 'Above 18' ? 'selected' : '' }}>Above 18</option>
            </select>
            @error('age_group') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
        </div>

        <!-- Submit -->
        <div class="form-group mt-4 text-center">
            <button type="submit" class="btn btn-light btn-lg px-5">Create Victim</button>
        </div>
    </form>
</div>
@endsection
