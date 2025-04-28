@extends('layouts.app')

@section('content')
<h2>Available Reports</h2>

<select id="reportDropdown" class="form-control">
    <option value="">-- Select a Report --</option>
    @foreach($reports as $report)
        <option value="{{ route('crime.reports.show', $report->id) }}">{{ $report->name }}</option>
    @endforeach
</select>

<script>
    document.getElementById('reportDropdown').addEventListener('change', function () {
        var url = this.value;
        if (url) {
            window.location.href = url;
        }
    });
</script>
@endsection
