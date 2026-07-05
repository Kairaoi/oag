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

    <div class="official-doc">
        <div class="doc-watermark">Official Copy</div>

        <!-- Letterhead -->
        <div class="doc-letterhead">
            <img src="{{ asset('images/oag_logo.png') }}" alt="Coat of Arms of the Republic of Kiribati" class="doc-crest">
            <div class="doc-state">Republic of Kiribati</div>
            <div class="doc-office">Office of the Attorney General</div>
        </div>
        <div class="doc-rule doc-rule--double"></div>

        <!-- Title block -->
        <div class="doc-title-block">
            <h1 class="doc-title">Case Proof</h1>
            <div class="doc-title-sub">Certified Record of Case Proceedings</div>

            <p class="doc-attestation">
                This is to certify that the particulars set out below constitute a true and accurate
                extract of the official case record maintained by the Office of the Attorney General,
                Republic of Kiribati, in respect of the matter referenced herein.
            </p>

            <table class="doc-refs">
                <tr>
                    <td class="doc-refs-label">Document Reference</td>
                    <td>{{ $docRef }}</td>
                    <td class="doc-refs-label">Case File Number</td>
                    <td>{{ $case->case_file_number }}</td>
                </tr>
                <tr>
                    <td class="doc-refs-label">Date Issued</td>
                    <td>{{ now()->format('d F Y') }}</td>
                    <td class="doc-refs-label">Classification</td>
                    <td>Official &mdash; For Internal Use</td>
                </tr>
            </table>
        </div>

        <!-- 1. Particulars of the Case -->
        <div class="doc-section">
            <h2 class="doc-heading">1. Particulars of the Case</h2>
            <table class="doc-table doc-table--kv">
                <tr>
                    <th>Case Name</th>
                    <td>{{ $case->case_name }}</td>
                </tr>
                <tr>
                    <th>Case File Number</th>
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

        <!-- 6. Register of Proceedings -->
        <div class="doc-section">
            <h2 class="doc-heading">6. Register of Proceedings</h2>
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

        <!-- Certification / Attestation -->
        <div class="doc-certification">
            <h2 class="doc-heading">Certification</h2>
            <p>
                I certify that the foregoing particulars have been extracted from, and are consistent with,
                the official case management records of the Office of the Attorney General as at the date
                of issue of this document, and that this Case Proof may be relied upon as an accurate
                statement of the case record for the purpose stated.
            </p>

            <table class="doc-attest-grid">
                <tr>
                    <td class="doc-attest-cell">
                        <div class="doc-signline"></div>
                        <div class="doc-attest-label">Signature</div>
                    </td>
                    <td class="doc-attest-cell">
                        <div class="doc-signline"></div>
                        <div class="doc-attest-label">Full Name &amp; Position</div>
                    </td>
                    <td class="doc-attest-cell">
                        <div class="doc-signline"></div>
                        <div class="doc-attest-label">Date</div>
                    </td>
                    <td class="doc-attest-cell doc-attest-cell--seal">
                        <div class="doc-seal">Official<br>Seal</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="doc-footer">
            <span>{{ $docRef }}</span>
            <span>Office of the Attorney General &middot; Bairiki, Tarawa &middot; Republic of Kiribati</span>
            <span>Page 1 of 1</span>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .official-doc * {
        background-image: none !important;
        text-shadow: none !important;
    }
    .official-doc *::before,
    .official-doc *::after {
        content: none !important;
    }

    .official-doc {
        position: relative;
        background-color: #ffffff;
        padding: 55px 65px;
        font-family: 'Times New Roman', Times, serif;
        color: #111;
        line-height: 1.7;
        font-size: 15px;
        overflow: hidden;
    }

    .doc-watermark {
        position: absolute;
        top: 45%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-32deg);
        font-size: 84px;
        font-weight: bold;
        letter-spacing: 4px;
        text-transform: uppercase;
        color: #000;
        opacity: 0.035;
        white-space: nowrap;
        pointer-events: none;
        z-index: 0;
    }

    .doc-letterhead,
    .doc-title-block,
    .doc-section,
    .doc-certification,
    .doc-footer,
    .doc-rule {
        position: relative;
        z-index: 1;
    }

    .doc-letterhead {
        text-align: center;
    }

    .doc-crest {
        max-height: 70px;
        margin-bottom: 8px;
    }

    .doc-state {
        font-size: 20px;
        font-weight: bold;
        letter-spacing: 1px;
    }

    .doc-office {
        font-size: 14px;
        letter-spacing: 0.5px;
        color: #333;
    }

    .doc-rule {
        height: 1px;
        background: #111;
        margin: 18px 0 26px;
    }

    .doc-rule--double {
        height: 3px;
        background: linear-gradient(#111, #111) top / 100% 1px no-repeat,
                    linear-gradient(#111, #111) bottom / 100% 1px no-repeat;
    }

    .doc-title-block {
        margin-bottom: 34px;
    }

    .doc-title {
        font-size: 24px;
        font-weight: bold;
        text-align: center;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 4px;
    }

    .doc-title-sub {
        text-align: center;
        font-size: 13.5px;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: #555;
        margin-bottom: 20px;
    }

    .doc-attestation {
        font-style: italic;
        text-align: justify;
        color: #222;
        margin: 0 0 22px;
        padding: 0 10px;
    }

    .doc-refs {
        width: 100%;
        font-size: 14px;
    }

    .doc-refs td {
        padding: 3px 0;
    }

    .doc-refs-label {
        color: #555;
        width: 150px;
        white-space: nowrap;
    }

    .doc-section {
        margin-bottom: 28px;
    }

    .doc-heading {
        font-size: 15px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
    }

    .doc-empty {
        font-style: italic;
        color: #666;
        margin: 0;
    }

    .doc-table {
        width: 100%;
        font-size: 14.5px;
    }

    .doc-table thead th {
        text-align: left;
        font-weight: bold;
        padding: 0 12px 8px 0;
        border-bottom: 1px solid #111;
    }

    .doc-table tbody td {
        padding: 8px 12px 8px 0;
        border-bottom: 1px solid #ccc;
        vertical-align: top;
    }

    .doc-table tbody tr:last-child td {
        border-bottom: none;
    }

    .doc-table--kv th {
        width: 220px;
        text-align: left;
        font-weight: bold;
        padding: 6px 12px 6px 0;
        border-bottom: 1px solid #eee;
        vertical-align: top;
    }

    .doc-table--kv td {
        padding: 6px 0;
        border-bottom: 1px solid #eee;
    }

    .doc-table--kv tr:last-child th,
    .doc-table--kv tr:last-child td {
        border-bottom: none;
    }

    .doc-table--register td:first-child {
        font-weight: bold;
    }

    .doc-list {
        margin: 0;
        padding-left: 22px;
    }

    .doc-certification {
        margin-top: 42px;
        padding-top: 22px;
        border-top: 3px double #111;
    }

    .doc-certification > p {
        text-align: justify;
        font-size: 14px;
    }

    .doc-attest-grid {
        width: 100%;
        margin-top: 34px;
        table-layout: fixed;
    }

    .doc-attest-cell {
        text-align: center;
        vertical-align: bottom;
        padding: 0 10px;
    }

    .doc-signline {
        border-bottom: 1px solid #111;
        height: 34px;
    }

    .doc-attest-label {
        font-size: 11.5px;
        color: #555;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 6px;
    }

    .doc-attest-cell--seal {
        vertical-align: middle;
    }

    .doc-seal {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 76px;
        height: 76px;
        border: 2px dashed #999;
        border-radius: 50%;
        font-size: 10.5px;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        color: #999;
        text-align: center;
        line-height: 1.3;
    }

    .doc-footer {
        position: relative;
        z-index: 1;
        margin-top: 40px;
        padding-top: 14px;
        border-top: 1px solid #111;
        display: flex;
        justify-content: space-between;
        font-size: 11px;
        color: #555;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    @media print {
        .no-print { display: none; }
    }
</style>
@endpush
