@props([
    'title',
    'subtitle' => 'Certified Record of Case Proceedings',
    'docRef',
    'docRefLabel' => 'Document Reference',
    'secondaryLabel' => 'Case File Number',
    'secondaryValue' => null,
    'dateIssued' => null,
    'classification' => 'Official — For Internal Use',
    'attestation' => 'This is to certify that the particulars set out below constitute a true and accurate extract of the official case record maintained by the Office of the Attorney General, Republic of Kiribati, in respect of the matter referenced herein.',
    'certificationText' => 'I certify that the foregoing particulars have been extracted from, and are consistent with, the official case management records of the Office of the Attorney General as at the date of issue of this document, and that this document may be relied upon as an accurate statement of the record for the purpose stated.',
    'watermark' => 'Official Copy',
])

<div class="official-doc">
    <div class="doc-watermark">{{ $watermark }}</div>

    <!-- Letterhead -->
    <div class="doc-letterhead">
        <img src="{{ asset('images/oag_logo.png') }}" alt="Coat of Arms of the Republic of Kiribati" class="doc-crest">
        <div class="doc-state">Republic of Kiribati</div>
        <div class="doc-office">Office of the Attorney General</div>
    </div>
    <div class="doc-rule doc-rule--double"></div>

    <!-- Title block -->
    <div class="doc-title-block">
        <h1 class="doc-title">{{ $title }}</h1>
        <div class="doc-title-sub">{{ $subtitle }}</div>

        <p class="doc-attestation">{{ $attestation }}</p>

        <table class="doc-refs">
            <tr>
                <td class="doc-refs-label">{{ $docRefLabel }}</td>
                <td>{{ $docRef }}</td>
                <td class="doc-refs-label">{{ $secondaryLabel }}</td>
                <td>{{ $secondaryValue }}</td>
            </tr>
            <tr>
                <td class="doc-refs-label">Date Issued</td>
                <td>{{ $dateIssued ?? now()->format('d F Y') }}</td>
                <td class="doc-refs-label">Classification</td>
                <td>{{ $classification }}</td>
            </tr>
        </table>
    </div>

    {{ $slot }}

    <!-- Certification / Attestation -->
    <div class="doc-certification">
        <h2 class="doc-heading">Certification</h2>
        <p>{{ $certificationText }}</p>

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

@push('styles')
<link rel="stylesheet" href="{{ asset('css/official-document.css') }}">
@endpush
