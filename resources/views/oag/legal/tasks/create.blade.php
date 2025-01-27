@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb" style="background: none; box-shadow: none;">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color: #ff4b2b; font-weight: bold;">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('legal.legal_tasks.index') }}" style="color: #ff4b2b; font-weight: bold;">Legal Tasks</a></li>
            <li class="breadcrumb-item active" aria-current="page" style="color: #333; font-weight: bold;">Create Legal Task</li>
        </ol>
    </nav>

    <!-- Main Title -->
    <h1 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #333; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);">
        Create Legal Task
    </h1>

    <!-- Form -->
    <form action="{{ route('legal.legal_tasks.store') }}" method="POST" class="p-4 shadow-lg rounded" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); border-radius: 20px;">
        @csrf

        <div class="row">
            <!-- Left Column -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="date" class="text-white">Date *</label>
                    <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date') }}" required>
                    @error('date')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="ministry" class="text-white">Ministry *</label>
                    <input type="text" class="form-control @error('ministry') is-invalid @enderror" id="ministry" name="ministry" value="{{ old('ministry') }}" required>
                    @error('ministry')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="legal_advice_meeting" class="text-white">Legal Advice/Meeting *</label>
                    <input type="text" class="form-control @error('legal_advice_meeting') is-invalid @enderror" id="legal_advice_meeting" name="legal_advice_meeting" value="{{ old('legal_advice_meeting') }}" required>
                    @error('legal_advice_meeting')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="allocated_date" class="text-white">Allocated Date</label>
                    <input type="date" class="form-control @error('allocated_date') is-invalid @enderror" id="allocated_date" name="allocated_date" value="{{ old('allocated_date') }}">
                    @error('allocated_date')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="task" class="text-white">Task *</label>
                    <textarea class="form-control @error('task') is-invalid @enderror" id="task" name="task" rows="3" required>{{ old('task') }}</textarea>
                    @error('task')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="allocated_to" class="text-white">Allocate To *</label>
                    <select name="allocated_to" id="allocated_to" class="form-control @error('allocated_to') is-invalid @enderror">
                        <option value="">Select User</option>
                        @foreach(\App\Models\User::all() as $user)
                            <option value="{{ $user->id }}" {{ old('allocated_to') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('allocated_to')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="onward_action" class="text-white">Onward Action</label>
                    <textarea class="form-control @error('onward_action') is-invalid @enderror" id="onward_action" name="onward_action" rows="3">{{ old('onward_action') }}</textarea>
                    @error('onward_action')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="notes" class="text-white">Notes</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                    @error('notes')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="date_task_achieved" class="text-white">Date Task Achieved</label>
                            <input type="date" class="form-control @error('date_task_achieved') is-invalid @enderror" id="date_task_achieved" name="date_task_achieved" value="{{ old('date_task_achieved') }}">
                            @error('date_task_achieved')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="meeting_date" class="text-white">Meeting Date</label>
                            <input type="date" class="form-control @error('meeting_date') is-invalid @enderror" id="meeting_date" name="meeting_date" value="{{ old('meeting_date') }}">
                            @error('meeting_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="time_frame" class="text-white">Time Frame</label>
                    <input type="text" class="form-control @error('time_frame') is-invalid @enderror" id="time_frame" name="time_frame" value="{{ old('time_frame') }}">
                    @error('time_frame')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Form Buttons -->
        <div class="text-center mt-4">
            <button type="submit" class="btn btn-light">Create Task</button>
            <a href="{{ route('legal.legal_tasks.index') }}" class="btn btn-outline-light ml-2">Cancel</a>
        </div>
    </form>
</div>
@endsection
