@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Create Court Attendance</h1>

        <form action="{{ route('civil.courtattendance.store') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-header">Court Attendance Details</div>
                <div class="card-body">
                    <!-- Civil Case ID -->
                    <div class="form-group">
                        <label for="civil_case_id">Civil Case</label>
                        <select name="civil_case_id" id="civil_case_id" class="form-control" required>
                            <option value="">Select Civil Case</option>
                            @foreach($civilCases as $id => $name)
                                <option value="{{ $id }}" {{ old('civil_case_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('civil_case_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Opposing Counsel Name -->
                    <div class="form-group">
                            <label for="plaintiff_counsel">Opposing Counsel Name *</label>
                            <select class="form-control @error('counsels.0.user_id') is-invalid @enderror" name="counsels[0][user_id]" required>
                                <option value="">Select Opposing Counsel</option>
                                @foreach($lawyers as $id => $name)
                                    <option value="{{ $id }}" {{ old('counsels.0.user_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="counsels[0][type]" value="Plaintiff">
                            @error('counsels.0.user_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                    <!-- Hearing Date -->
                    <div class="form-group">
                        <label for="hearing_date">Hearing Date</label>
                        <input type="date" name="hearing_date" class="form-control" id="hearing_date" value="{{ old('hearing_date') }}" required>
                        @error('hearing_date')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Hearing Type -->
                    <div class="form-group">
                        <label for="hearing_type">Hearing Type</label>
                        <input type="text" name="hearing_type" class="form-control" id="hearing_type" value="{{ old('hearing_type') }}">
                    </div>

                    <!-- Hearing Time -->
                    <div class="form-group">
                        <label for="hearing_time">Hearing Time</label>
                        <input type="time" name="hearing_time" class="form-control" id="hearing_time" value="{{ old('hearing_time') }}">
                    </div>

                    <!-- Case Status -->
                    <div class="form-group">
                        <label for="case_status">Case Status</label>
                        <select name="case_status" class="form-control" id="case_status" required>
                            <option value="Concluded" {{ old('case_status') == 'Concluded' ? 'selected' : '' }}>Concluded</option>
                            <option value="Ongoing" {{ old('case_status') == 'Ongoing' ? 'selected' : '' }}>Ongoing</option>
                            <option value="Adjourned" {{ old('case_status') == 'Adjourned' ? 'selected' : '' }}>Adjourned</option>
                            <option value="Other" {{ old('case_status') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <!-- Status Notes -->
                    <div class="form-group">
                        <label for="status_notes">Status Notes</label>
                        <textarea name="status_notes" class="form-control" id="status_notes">{{ old('status_notes') }}</textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success">Create Court Attendance</button>
                    <a href="{{ route('civil.courtattendance.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
@endsection
