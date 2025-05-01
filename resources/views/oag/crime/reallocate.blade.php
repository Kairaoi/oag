@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Reallocate Case #{{ $case->id }}</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('crime.criminalCase.reallocate', $case->id) }}">
        @csrf

        <label for="to_lawyer_id" class="text-white">Lawyer</label>
<select class="form-control @error('to_lawyer_id') is-invalid @enderror" id="to_lawyer_id" name="to_lawyer_id" required>
    <option value="">Select a lawyer</option>
    @foreach($lawyers as $id => $name)
        <option value="{{ $id }}" {{ old('to_lawyer_id') == $id ? 'selected' : '' }}>
            {{ $name }}
        </option>
    @endforeach
</select>
@error('to_lawyer_id')
    <span class="invalid-feedback" role="alert">
        <strong>{{ $message }}</strong>
    </span>
@enderror


        <div class="mb-3">
            <label for="reallocation_reason" class="form-label">Reason</label>
            <textarea name="reallocation_reason" id="reallocation_reason" class="form-control" required></textarea>
            @error('reallocation_reason') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="reallocation_date" class="form-label">Reallocation Date</label>
            <input type="date" name="reallocation_date" id="reallocation_date" class="form-control" required>
            @error('reallocation_date') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-primary">Reallocate</button>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
