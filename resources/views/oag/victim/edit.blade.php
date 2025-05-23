@extends('layouts.app')

@section('content')
<div class="container mt-5">
   <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
    {{ Breadcrumbs::render() }}
    </nav>


    <!-- Heading -->
    <h1 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #333; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);">
        Edit Victim
    </h1>

    <!-- Edit Victim Form -->
    <form action="{{ route('crime.victim.update', $victim->id) }}" method="POST" class="p-4 shadow-lg rounded" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); border-radius: 20px;">
        @csrf
        @method('PUT') <!-- This method is necessary for updating the resource -->
        
        <div class="form-group">
            <label for="case_id" class="text-white">Case</label>
            <select class="form-control @error('case_id') is-invalid @enderror" id="case_id" name="case_id" required>
                <option value="">Select a case</option>
                @foreach($cases as $id => $name)
                    <option value="{{ $id }}" {{ old('case_id', $victim->case_id) == $id ? 'selected' : '' }}>
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

        <div class="form-group">
            <label for="council_id" class="text-white">Council</label>
            <select class="form-control @error('council_id') is-invalid @enderror" id="council_id" name="council_id" required>
                <option value="">Select a council</option>
                @foreach($councils as $id => $name)
                    <option value="{{ $id }}" {{ old('council_id', $victim->council_id) == $id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
            @error('council_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group">
            <label for="island_id" class="text-white">Island</label>
            <select class="form-control @error('island_id') is-invalid @enderror" id="island_id" name="island_id" required>
                <option value="">Select an island</option>
                @foreach($islands as $id => $name)
                    <option value="{{ $id }}" {{ old('island_id', $victim->island_id) == $id ? 'selected' : '' }}>
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

        <div class="form-group">
            <label for="first_name" class="text-white">First Name</label>
            <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name', $victim->first_name) }}" required>
            @error('first_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group">
            <label for="last_name" class="text-white">Last Name</label>
            <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name', $victim->last_name) }}" required>
            @error('last_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group">
            <label for="victim_particulars" class="text-white">Victim Particulars</label>
            <textarea class="form-control @error('victim_particulars') is-invalid @enderror" id="victim_particulars" name="victim_particulars" rows="3" required>{{ old('victim_particulars', $victim->victim_particulars) }}</textarea>
            @error('victim_particulars')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group">
            <label for="gender" class="text-white">Gender</label>
            <select class="form-control @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                <option value="">Select gender</option>
                <option value="Male" {{ old('gender', $victim->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ old('gender', $victim->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                <option value="Other" {{ old('gender', $victim->gender) == 'Other' ? 'selected' : '' }}>Other</option>
            </select>
            @error('gender')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group">
            <label for="date_of_birth" class="text-white">Date of Birth</label>
            <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $victim->date_of_birth) }}" required>
            @error('date_of_birth')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <button type="submit" class="btn btn-light btn-lg btn-block" style="border-radius: 30px; font-weight: bold;">Update</button>
    </form>
</div>
@endsection
