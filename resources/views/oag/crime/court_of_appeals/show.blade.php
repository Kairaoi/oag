@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('crime.courtOfAppeal.index') }}">Court of Appeal</a></li>
            <li class="breadcrumb-item active" aria-current="page">Record #{{ $appeal->id }}</li>
        </ol>
    </nav>

    <h2 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace;">
        Court of Appeal Record
    </h2>

    <div class="card shadow-lg rounded-3">
        <div class="card-body">
            <table class="table table-striped">
                <tr>
                    <th style="width:30%">Case Name</th>
                    <td>{{ $appeal->case_name ?? '' }}</td>
                </tr>
                <tr>
                    <th>Appeal Case Number</th>
                    <td>{{ $appeal->appeal_case_number }}</td>
                </tr>
                <tr>
                    <th>Filing Date</th>
                    <td>{{ $appeal->appeal_filing_date ? \Carbon\Carbon::parse($appeal->appeal_filing_date)->format('d M Y') : '' }}</td>
                </tr>
                <tr>
                    <th>Filing Date Source</th>
                    <td>{{ ucfirst($appeal->filing_date_source) }}</td>
                </tr>
                <tr>
                    <th>Judgment Delivered Date</th>
                    <td>{{ $appeal->judgment_delivered_date ? \Carbon\Carbon::parse($appeal->judgment_delivered_date)->format('d M Y') : '' }}</td>
                </tr>
                <tr>
                    <th>Court Outcome</th>
                    <td>{{ $appeal->court_outcome ? ucfirst($appeal->court_outcome) : '' }}</td>
                </tr>
                <tr>
                    <th>Decision Principle Established</th>
                    <td>{{ $appeal->decision_principle_established }}</td>
                </tr>
                <tr>
                    <th>Created By</th>
                    <td>{{ $appeal->creator->name ?? '' }}</td>
                </tr>
                <tr>
                    <th>Updated By</th>
                    <td>{{ $appeal->updater->name ?? '' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('crime.courtOfAppeal.edit', $appeal->id) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit
        </a>
        <a href="{{ route('crime.courtOfAppeal.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>
@endsection
