@php
    $docRef = 'RD/' . str_pad($dispatch->id, 4, '0', STR_PAD_LEFT) . '/' . ($dispatch->created_at?->format('Y') ?? now()->format('Y'));
@endphp

<x-official-document
    title="Registry Dispatch Certificate"
    subtitle="Certified Transmission of Case File to the High Court Registry"
    :doc-ref="$docRef"
    secondary-label="Police Case File Number"
    :secondary-value="$dispatch->case->case_file_number ?? 'N/A'"
    :date-issued="\Carbon\Carbon::parse($dispatch->date_dispatched)->format('d F Y')"
    attestation="This is to certify that the charge file for the case referenced below has, following approval by the Attorney General, been formally dispatched by the Registry of the Office of the Attorney General to the High Court of the Republic of Kiribati for filing."
    certification-text="I certify that the particulars set out below are a true and accurate record of the dispatch of this case file, extracted from the official case management records of the Office of the Attorney General as at the date of issue of this document."
>
    <!-- 1. Case Particulars -->
    <div class="doc-section">
        <h2 class="doc-heading">1. Case Particulars</h2>
        <table class="doc-table doc-table--kv">
            <tr>
                <th>Case Name</th>
                <td>{{ $dispatch->case->case_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Police Case File Number</th>
                <td>{{ $dispatch->case->case_file_number ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Island</th>
                <td>{{ $dispatch->case->island->island_name ?? 'Not on record' }}</td>
            </tr>
            <tr>
                <th>Counsel Responsible</th>
                <td>{{ $dispatch->case->lawyer->name ?? 'Not on record' }}</td>
            </tr>
        </table>
    </div>

    <!-- 2. AG Approval -->
    <div class="doc-section">
        <h2 class="doc-heading">2. Attorney General Approval</h2>
        <table class="doc-table doc-table--kv">
            <tr>
                <th>AG Decision</th>
                <td>{{ $agReview ? ucfirst($agReview->ag_decision) : 'Not on record' }}</td>
            </tr>
            <tr>
                <th>Decision Date</th>
                <td>{{ $agReview && $agReview->decision_date ? \Carbon\Carbon::parse($agReview->decision_date)->format('d F Y') : 'Not on record' }}</td>
            </tr>
        </table>
    </div>

    <!-- 3. Dispatch Particulars -->
    <div class="doc-section">
        <h2 class="doc-heading">3. Dispatch Particulars</h2>
        <table class="doc-table doc-table--kv">
            <tr>
                <th>Date Dispatched</th>
                <td>{{ \Carbon\Carbon::parse($dispatch->date_dispatched)->format('d F Y') }}</td>
            </tr>
            <tr>
                <th>Dispatched To</th>
                <td>{{ $dispatch->dispatched_to }}</td>
            </tr>
            <tr>
                <th>Dispatched By</th>
                <td>{{ $dispatch->dispatchedBy->name ?? 'Not on record' }}</td>
            </tr>
        </table>
    </div>
</x-official-document>
