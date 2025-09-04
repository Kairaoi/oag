@extends('layouts.app')

@push('styles')
    <!-- Your styles here -->
@endpush

@section('content')
    <div class="container my-5">
        @if($appealDetails->isEmpty())
            <div class="alert alert-warning">No Court of Appeal details found.</div>
        @else
            <div id="appeal-details-report">
                @foreach($appealDetails as $appeal)
                    <div class="appeal-document p-5 border rounded shadow-sm bg-white mb-5 page-break-avoid" style="font-family: 'Georgia', serif; line-height: 1.7;">
                        <div class="reference-box mb-3">
                            <h2>COURT OF APPEAL REFERENCE: {{ $appeal->appeal_case_number }}</h2>
                        </div>

                        <h2 class="text-center mb-4 text-uppercase" style="font-weight: bold;">Court of Appeal Case Report</h2>

                        <!-- Updated case header section with dynamic date display -->
                        <div class="case-header d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <p class="fs-5 mb-1"><strong>Case Name:</strong> <span class="text-primary">{{ $appeal->case_name }}</span></p>
                                <p class="mb-0">
                                    <strong>Appeal Filed:</strong> 
                                    {{ date('F j, Y', strtotime($appeal->appeal_filing_date)) }}
                                </p>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Updated Appeal Overview section -->
                        <h4 class="text-decoration-underline">I. Court of Appeal Overview</h4>
                        <p>
                            This Court of Appeal, referenced as <strong>{{ $appeal->appeal_case_number }}</strong>, was filed on 
                            <strong>{{ date('F j, Y', strtotime($appeal->appeal_filing_date)) }}</strong>.
                            The judgment was delivered on <strong>{{ date('F j, Y', strtotime($appeal->judgment_delivered_date)) }}</strong>.
                        </p>

                        <!-- New detailed filing information section -->
                        <div class="filing-info-box mt-4 p-3 rounded" style="background-color: #e3f2fd;">
                            <h5 class="mb-2">Filing Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Filing Date:</strong> {{ date('F j, Y', strtotime($appeal->appeal_filing_date)) }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Filing Date Source:</strong> {{ $appeal->filing_date_source }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Court Outcome:</strong> {{ ucfirst($appeal->court_outcome) }}</p>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h4 class="text-decoration-underline">II. Record Information</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Created By:</strong> {{ $appeal->created_by_name }}</p>
                                <p><strong>Created At:</strong> {{ date('F j, Y H:i', strtotime($appeal->created_at)) }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Updated By:</strong> {{ $appeal->updated_by_name ?? 'N/A' }}</p>
                                <p><strong>Updated At:</strong> {{ date('F j, Y H:i', strtotime($appeal->updated_at)) }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection