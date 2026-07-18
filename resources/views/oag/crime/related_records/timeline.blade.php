@extends('layouts.app')

@section('content')
<div class="container mt-4 mb-5">
    <nav aria-label="breadcrumb">
        {{ Breadcrumbs::render() }}
    </nav>

    <div class="mb-3 no-print">
        <a href="{{ route('crime.relatedRecords', $case->id) }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-table"></i> View as Tabs
        </a>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-print"></i> Print
        </button>
    </div>

    @php
        $docRef = 'CP/' . str_pad($case->id, 4, '0', STR_PAD_LEFT) . '/' . ($case->created_at?->format('Y') ?? now()->format('Y'));
    @endphp

    <x-official-document
        title="Case Proof"
        subtitle="Certified Record of Case Proceedings"
        :doc-ref="$docRef"
        secondary-label="Police Case File Number"
        :secondary-value="$case->case_file_number"
    >
        <!-- 1. Particulars of the Case -->
        <div class="doc-section">
            <h2 class="doc-heading">1. Particulars of the Case</h2>
            <table class="doc-table doc-table--kv">
                <tr>
                    <th>Case Name</th>
                    <td>{{ $case->case_name }}</td>
                </tr>
                <tr>
                    <th>Police Case File Number</th>
                    <td>{{ $case->case_file_number }}</td>
                </tr>
                <tr>
                    <th>Present Status</th>
                    <td>{{ ucfirst($case->status) }}</td>
                </tr>
                @if($case->status === 'closed')
                    <tr>
                        <th>Reason for Closure</th>
                        <td>{{ $closureReasonDescription ?? 'Not on record' }}</td>
                    </tr>
                    <tr>
                        <th>Date Closed</th>
                        <td>{{ $dateFileClosed ? \Carbon\Carbon::parse($dateFileClosed)->format('d F Y') : 'Not on record' }}</td>
                    </tr>
                @endif
                <tr>
                    <th>Island</th>
                    <td>{{ $case->island->island_name ?? 'Not on record' }}</td>
                </tr>
                <tr>
                    <th>Counsel Responsible</th>
                    <td>{{ $case->lawyer->name ?? 'Not yet allocated' }}</td>
                </tr>
            </table>
        </div>

        <!-- 2. Particulars of the Incident -->
        <div class="doc-section">
            <h2 class="doc-heading">2. Particulars of the Incident</h2>
            @if($case->incidents->isEmpty())
                @if($case->date_of_incident || $case->island)
                    <table class="doc-table doc-table--kv">
                        @if($case->date_of_incident)
                            <tr>
                                <th>Date of Incident</th>
                                <td>{{ \Carbon\Carbon::parse($case->date_of_incident)->format('d F Y') }}</td>
                            </tr>
                        @endif
                        @if($case->island)
                            <tr>
                                <th>Place of Incident</th>
                                <td>{{ $case->island->island_name }}</td>
                            </tr>
                        @endif
                    </table>
                    <p class="doc-empty mt-2 mb-0">No further incident particulars (specific location or date range) have been recorded.</p>
                @else
                    <p class="doc-empty">No incident details recorded.</p>
                @endif
            @else
                <table class="doc-table">
                    <thead>
                        <tr>
                            <th>When</th>
                            <th>Island</th>
                            <th>Place of Incident</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($case->incidents as $incident)
                            <tr>
                                <td>
                                    @if($incident->date_of_incident_start)
                                        {{ $incident->date_of_incident_start->format('d F Y') }}
                                        @if($incident->date_of_incident_end && !$incident->date_of_incident_end->equalTo($incident->date_of_incident_start))
                                            &ndash; {{ $incident->date_of_incident_end->format('d F Y') }}
                                        @endif
                                    @else
                                        Not on record
                                    @endif
                                </td>
                                <td>{{ $incident->island->island_name ?? 'Not on record' }}</td>
                                <td>{{ $incident->place_of_incident }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <!-- 3. Particulars of the Accused -->
        <div class="doc-section">
            <h2 class="doc-heading">3. Particulars of the Accused</h2>
            @if($case->accused->isEmpty())
                <p class="doc-empty">No accused persons recorded.</p>
            @else
                <table class="doc-table">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Gender</th>
                            <th>Age</th>
                            <th>Island</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($case->accused as $accused)
                            <tr>
                                <td>{{ $accused->first_name }} {{ $accused->last_name }}</td>
                                <td>{{ $accused->gender }}</td>
                                <td>{{ $accused->age }}</td>
                                <td>{{ $accused->island->island_name ?? '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <!-- 4. Particulars of the Victim(s) -->
        <div class="doc-section">
            <h2 class="doc-heading">4. Particulars of the Victim(s)</h2>
            @if($case->victims->isEmpty())
                <p class="doc-empty">No victims recorded.</p>
            @else
                <table class="doc-table">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Gender</th>
                            <th>Age</th>
                            <th>Age Group</th>
                            <th>Island</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($case->victims as $victim)
                            <tr>
                                <td>{{ $victim->first_name }} {{ $victim->last_name }}</td>
                                <td>{{ $victim->gender }}</td>
                                <td>{{ $victim->age }}</td>
                                <td>{{ $victim->age_group ?? '' }}</td>
                                <td>{{ $victim->island->island_name ?? '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <!-- 5. Offence(s) Charged -->
        <div class="doc-section">
            <h2 class="doc-heading">5. Offence(s) Charged</h2>
            @if($case->offences->isEmpty())
                <p class="doc-empty">No offences recorded.</p>
            @else
                <ol class="doc-list">
                    @foreach($case->offences as $offence)
                        <li>{{ $offence->offence_name }}</li>
                    @endforeach
                </ol>
            @endif
        </div>

        <!-- 6. Appeal Proceedings -->
        <div class="doc-section">
            <h2 class="doc-heading">6. Appeal Proceedings</h2>
            @if($appealDetails->isEmpty())
                <p class="doc-empty">No appeal has been filed for this case.</p>
            @else
                @foreach($appealDetails as $appeal)
                    <table class="doc-table doc-table--kv" @if(!$loop->last) style="margin-bottom:18px;" @endif>
                        <tr>
                            <th>Case Name</th>
                            <td>{{ $appeal->case_name }}</td>
                        </tr>
                        <tr>
                            <th>Appeal Reference</th>
                            <td>{{ $appeal->appeal_case_number }}</td>
                        </tr>
                        <tr>
                            <th>Filing Date</th>
                            <td>
                                {{ \Carbon\Carbon::parse($appeal->appeal_filing_date)->format('d F Y') }}
                                @if($appeal->filing_date_source)
                                    ({{ ucfirst($appeal->filing_date_source) }} filing)
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Judgment Delivered</th>
                            <td>{{ $appeal->judgment_delivered_date ? \Carbon\Carbon::parse($appeal->judgment_delivered_date)->format('d F Y') : 'Not on record' }}</td>
                        </tr>
                        <tr>
                            <th>Verdict</th>
                            <td>{{ $appeal->verdict ? ucfirst($appeal->verdict) : 'Not on record' }}</td>
                        </tr>
                        <tr>
                            <th>Court Outcome</th>
                            <td>{{ $appeal->court_outcome ? ucfirst($appeal->court_outcome) : 'Not on record' }}</td>
                        </tr>
                    </table>
                    @if($appeal->decision_principle_established)
                        <p class="doc-quote">{{ $appeal->decision_principle_established }}</p>
                    @endif
                @endforeach
            @endif
        </div>

        <!-- 7. Register of Proceedings -->
        <div class="doc-section">
            <h2 class="doc-heading">7. Register of Proceedings</h2>
            @if(count($events) === 0)
                <p class="doc-empty">No recorded milestones for this case yet.</p>
            @else
                <table class="doc-table doc-table--register">
                    <thead>
                        <tr>
                            <th style="width:6%">No.</th>
                            <th style="width:16%">Date</th>
                            <th style="width:24%">Stage</th>
                            <th>Particulars</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($events as $i => $event)
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>
                                <td>{{ $event['date'] ? \Carbon\Carbon::parse($event['date'])->format('d M Y') : 'N/A' }}</td>
                                <td>{{ $event['stage'] }}</td>
                                <td>{{ $event['summary'] ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </x-official-document>
</div>
@endsection
