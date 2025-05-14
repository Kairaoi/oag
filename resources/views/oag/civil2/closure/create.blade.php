@extends('layouts.app')

@section('content')
<div class="container max-w-3xl mx-auto">
    <h2 class="text-xl font-bold mb-4">Close Case: {{ $case->case_name }} ({{ $case->case_file_number }})</h2>

    <form method="POST" action="{{ route('civil2.case_closure.store', $case->id) }}">
        @csrf

        <div class="mb-4">
            <label for="memo_date" class="block text-sm font-medium text-gray-700">Memo Date</label>
            <input type="date" name="memo_date" id="memo_date" class="mt-1 block w-full rounded border-gray-300" required>
        </div>

        <div class="mb-4">
            <label class="block font-medium">SG Clearance</label>
            <label class="inline-flex items-center mr-4">
                <input type="radio" name="sg_clearance" value="1" class="form-radio"> Yes
            </label>
            <label class="inline-flex items-center">
                <input type="radio" name="sg_clearance" value="0" class="form-radio" checked> No
            </label>

            <input type="date" name="sg_clearance_date" class="mt-2 block w-full rounded border-gray-300" placeholder="SG Clearance Date (optional)">
        </div>

        <div class="mb-4">
            <label class="block font-medium">AG Endorsement</label>
            <label class="inline-flex items-center mr-4">
                <input type="radio" name="ag_endorsement" value="1" class="form-radio"> Yes
            </label>
            <label class="inline-flex items-center">
                <input type="radio" name="ag_endorsement" value="0" class="form-radio" checked> No
            </label>

            <input type="date" name="ag_endorsement_date" class="mt-2 block w-full rounded border-gray-300" placeholder="AG Endorsement Date (optional)">
        </div>

        <div class="mb-4">
            <label class="block font-medium">File Archived</label>
            <label class="inline-flex items-center mr-4">
                <input type="radio" name="file_archived" value="1" class="form-radio"> Yes
            </label>
            <label class="inline-flex items-center">
                <input type="radio" name="file_archived" value="0" class="form-radio" checked> No
            </label>

            <input type="date" name="file_archived_date" class="mt-2 block w-full rounded border-gray-300" placeholder="File Archived Date (optional)">
        </div>

        <div class="mb-4">
            <label for="closure_notes" class="block text-sm font-medium text-gray-700">Closure Notes</label>
            <textarea name="closure_notes" id="closure_notes" rows="4" class="mt-1 block w-full rounded border-gray-300" placeholder="Any relevant comments or notes..."></textarea>
        </div>

        <div class="mt-6">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Submit Closure
            </button>
        </div>
    </form>
</div>
@endsection
