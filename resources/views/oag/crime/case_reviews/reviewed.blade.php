@extends('layouts.app')

@section('content')
@php
    $review = $caseReviews->first(); // since it's a collection with 1 item
    $overlayReasons = ['insufficient_evidence', 'returned_to_police'];
@endphp

@if($review && $review->case_status === 'closed' && in_array($review->evidence_status, $overlayReasons))
    <div style="
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.75);
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
        color: white;
        text-align: center;
        padding: 2rem;
    ">
        <div>
            <h1>Case Closed</h1>
            <p>This case was closed due to <strong>{{ ucwords(str_replace('_', ' ', $review->evidence_status)) }}</strong>.</p>
        </div>
    </div>
@endif

<div class="container mt-4 mb-5">
    @foreach($caseReviews as $review)
    <div class="case-review-document">
        <!-- Document Header / Letterhead -->
        <div class="document-header">
            <div class="header-content text-center">
                <div class="coat-of-arms">
                    <img src="{{ asset('images/oag_logo.png') }}" alt="Republic of Kiribati Coat of Arms" class="coat-of-arms-img">
                </div>
                <div class="document-kicker">Republic of Kiribati</div>
                <h1 class="document-title">Case Review Report</h1>
                <div class="document-subtitle">Office of the Attorney General</div>
                <div class="confidential-stamp">Confidential Legal Document</div>
            </div>
        </div>

        <!-- Case Reference -->
        <div class="section">
            <div class="reference-box">
                <h2 class="section-title">Matter Reference: {{ $review->case_name }}</h2>
                <div class="d-flex justify-content-between reference-meta">
                    <div><span class="meta-label">Review Date:</span> {{ \Carbon\Carbon::parse($review->review_date)->format('F j, Y') }}</div>
                    <div><span class="meta-label">Reviewing Attorney:</span> {{ $review->created_by_name ?? 'Not assigned' }}</div>
                </div>
            </div>
        </div>

        <!-- Summary Section -->
        <div class="section">
            <h2 class="section-title">Summary of Proceedings</h2>
            <div class="section-content">
                <p>This memorandum constitutes a formal review of the criminal proceedings against <strong>{{ $review->accused_names ?? 'the Accused' }}</strong> ("the Accused") in relation to charges of <strong>{{ $review->offence_names ?? 'Not yet specified' }}</strong>. This matter falls within the category of <strong>{{ $review->category_names ?? 'Not yet specified' }}</strong> under the jurisdiction of the Kiribati Courts.</p>

                <p>The Office of the Attorney General has conducted this review to assess the current status of evidence, evaluate legal positions, and determine appropriate next steps in accordance with the interests of justice and the laws of Kiribati.</p>
            </div>
        </div>

        <!-- Accused Particulars -->
        <div class="section">
            <h2 class="section-title">Accused Particulars</h2>
            <div class="section-content">
                <p>The Accused in this matter is identified as follows:</p>

                <div class="particulars-table">
                    <table class="table table-striped">
                        <tr>
                            <th width="30%">Full Name:</th>
                            <td>{{ $review->accused_names ?? 'Not on record' }}</td>
                        </tr>
                        <tr>
                            <th>Gender:</th>
                            <td>{{ $review->accused_genders ?? 'Not on record' }}</td>
                        </tr>
                        <tr>
                            <th>Date of Birth:</th>
                            <td>
                                @if($review->accused_dob)
                                    @foreach(explode(',', $review->accused_dob) as $dob)
                                        {{ \Carbon\Carbon::parse(trim($dob))->format('F j, Y') }}<br>
                                    @endforeach
                                    ({{ $review->accused_ages }} years of age)
                                @else
                                    Not on record
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Island of Origin:</th>
                            <td>{{ $review->accused_islands ?? 'Not on record' }}</td>
                        </tr>
                        <tr>
                            <th>Current Residential Address:</th>
                            <td>{{ $review->accused_addresses ?? 'Not on record' }}</td>
                        </tr>
                        <tr>
                            <th>Contact Information:</th>
                            <td>
                                <div>Telephone: {{ $review->accused_phones ?? 'Not on record' }}</div>
                                <div>Other Contact: {{ $review->accused_contacts ?? 'Not on record' }}</div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Victim Particulars -->
        <div class="section">
            <h2 class="section-title">Victim Particulars</h2>
            <div class="section-content">
                <p>The alleged victim in this matter is identified as follows:</p>

                <div class="particulars-table">
                    <table class="table table-striped">
                        <tr>
                            <th width="30%">Full Name:</th>
                            <td>{{ $review->victim_names ?? 'Not on record' }}</td>
                        </tr>
                        <tr>
                            <th>Gender:</th>
                            <td>{{ $review->victim_genders ?? 'Not on record' }}</td>
                        </tr>
                        <tr>
                            <th>Date of Birth:</th>
                            <td>
                                @if($review->victim_dob)
                                    {{ \Carbon\Carbon::parse($review->victim_dob)->format('F j, Y') }} ({{ $review->victim_ages }} years of age)
                                @else
                                    Not on record
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Age Category:</th>
                            <td>{{ $review->victim_age_groups ?? 'Not on record' }}</td>
                        </tr>
                        <tr>
                            <th>Island of Origin:</th>
                            <td>{{ $review->victim_islands ?? 'Not on record' }}</td>
                        </tr>
                        <tr>
                            <th>Current Residential Address:</th>
                            <td>{{ $review->victim_addresses ?? 'Not on record' }}</td>
                        </tr>
                        <tr>
                            <th>Contact Information:</th>
                            <td>
                                <div>Telephone: {{ $review->victim_phones ?? 'Not on record' }}</div>
                                <div>Other Contact: {{ $review->victim_contacts ?? 'Not on record' }}</div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Evidence Assessment -->
        <div class="section">
            <h2 class="section-title">Evidence Assessment</h2>
            <div class="section-content">
                <div class="evidence-status">
                    <span class="meta-label">Current Status of Evidence:</span>
                    <span class="status-badge status-{{ strtolower(str_replace(' ', '-', str_replace('_', '-', $review->evidence_status))) }}">
                        {{ ucfirst(str_replace('_', ' ', $review->evidence_status)) }}
                    </span>
                </div>

                <h3>Key Evidentiary Materials:</h3>
                <ul class="evidence-list">
                    <li>Witness statements</li>
                    <li>Material evidence</li>
                    <li>Police reports</li>
                    <li>Medical reports (if applicable)</li>
                    <li>Expert testimony (if applicable)</li>
                </ul>

                <h3>Offence Particulars:</h3>
                <div class="evidence-analysis">
                    {{ $review->offence_particulars ?: 'Not provided.' }}
                </div>
            </div>
        </div>

        <!-- Case Assignment History -->
        <div class="section">
            <h2 class="section-title">Case Assignment History</h2>
            <div class="section-content">
                @if($review->to_lawyer_name)
                    <p>This matter was initially assigned to <strong>{{ $review->from_lawyer_name ?? 'no lawyer' }}</strong> and has subsequently been reassigned to <strong>{{ $review->to_lawyer_name }}</strong> as of <strong>{{ \Carbon\Carbon::parse($review->reallocation_date)->format('F j, Y') }}</strong>.</p>

                    <h3>Reassignment Rationale:</h3>
                    <div class="reassignment-details">
                        {{ $review->reallocation_details ?: 'Not provided.' }}
                    </div>
                @else
                    <p>No reassignment has been recorded for this matter.</p>
                @endif
            </div>
        </div>

        <!-- Legal Issues and Considerations -->
        <div class="section">
            <h2 class="section-title">Legal Issues and Considerations</h2>
            <div class="section-content">
                <p>The prosecution of this matter involves consideration of the following legal elements:</p>

                <h3>1. Elements of the Offence:</h3>
                <ul>
                    <li>The prosecution must establish beyond reasonable doubt that the Accused committed the acts constituting the offence of {{ $review->offence_names ?? 'the alleged offence' }}.</li>
                    <li>Each element of the offence must be proven according to the Criminal Code of Kiribati.</li>
                </ul>

                <h3>2. Potential Defences:</h3>
                <ul>
                    <li>Assessment of available defences under Kiribati law.</li>
                    <li>Evaluation of mitigating factors that may be relevant to sentencing.</li>
                </ul>

                <h3>3. Evidentiary Challenges:</h3>
                <ul>
                    <li>Analysis of any evidentiary gaps or challenges.</li>
                    <li>Consideration of admissibility issues.</li>
                </ul>

                <h3>4. Public Interest Considerations:</h3>
                <ul>
                    <li>Assessment of prosecution in light of public interest factors.</li>
                    <li>Consideration of victim interests and community impact.</li>
                </ul>
            </div>
        </div>

        <!-- Recommendations and Next Steps -->
        <div class="section">
            <h2 class="section-title">Recommendations and Next Steps</h2>
            <div class="section-content">
                <p>Based on the comprehensive review of this matter, the following recommendations are made:</p>

                <ol class="recommendations-list">
                    <li><strong>[Further Investigation Required / Proceed to Trial / Consider Resolution]</strong></li>
                    <li><strong>[Specific Evidence to be Gathered]</strong></li>
                    <li><strong>[Legal Strategy Recommendations]</strong></li>
                    <li><strong>[Timeline for Next Procedural Steps]</strong></li>
                </ol>
            </div>
        </div>

        <!-- Conclusion -->
        <div class="section">
            <h2 class="section-title">Conclusion</h2>
            <div class="section-content">
                <p>This review has been conducted in accordance with the prosecutorial guidelines of the Office of the Attorney General of Kiribati and represents a thorough assessment of the available evidence and applicable law in this matter. The recommendations provided aim to ensure that justice is served while upholding the rights of all parties involved.</p>
            </div>
        </div>

        <!-- Document Footer -->
        <div class="document-footer">
            <div class="footer-info">
                <div><span class="meta-label">Document Prepared By:</span> {{ $review->created_by_name ?? 'Not on record' }}</div>
                <div><span class="meta-label">Position:</span> Prosecuting Attorney</div>
                <div><span class="meta-label">Date of Report:</span> {{ \Carbon\Carbon::parse($review->created_at)->format('F j, Y') }}</div>
                <div><span class="meta-label">Last Updated:</span> {{ $review->updated_at ? \Carbon\Carbon::parse($review->updated_at)->format('F j, Y') : 'Not updated' }}</div>
            </div>

            <div class="confidentiality-notice">
                <h3>Confidentiality Notice</h3>
                <p>This document contains legally privileged and confidential information intended solely for the use of authorized personnel within the Office of the Attorney General. Unauthorized disclosure, copying, distribution, or use of the contents of this document is strictly prohibited and may result in legal action.</p>
            </div>

            <div class="government-footer text-center mt-4">
                <div class="government-seal-line"></div>
                <div><strong>Government of Kiribati</strong></div>
                <div>Office of the Attorney General</div>
                <div>Bairiki, Tarawa &middot; Republic of Kiribati</div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons text-center mt-4">
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print"></i> Print Report
            </button>
            <a href="{{ route('crime.criminalCase.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to All Reviews
            </a>
            <a href="{{ route('crime.CaseReview.edit', $review->id) }}" class="btn btn-info">
                <i class="fas fa-edit"></i> Edit Review
            </a>
            <button id="download-btn" class="btn btn-success">
                <i class="fas fa-download"></i> Download PDF
            </button>
        </div>
    </div>

    @if(!$loop->last)
    <div class="page-break"></div>
    @endif
    @endforeach
</div>

@push('styles')
<style>
    /* Scoped reset: neutralize any site-wide decorative ::before/::after
       rules (icons, dividers, etc.) that would otherwise bleed into this
       formal document and cut across the text. */
    .case-review-document * {
        background-image: none !important;
        text-shadow: none !important;
    }
    .case-review-document *::before,
    .case-review-document *::after {
        content: none !important;
    }

    /* General Document Styling */
    .case-review-document {
        background-color: #fffdf8;
        padding: 50px 60px;
        box-shadow: 0 0 0 1px #d8cfa9, 0 8px 24px rgba(0,0,0,0.08);
        border: 1px solid #b9a76a;
        margin-bottom: 30px;
        font-family: 'Georgia', 'Times New Roman', Times, serif;
        color: #1c1c1c;
        line-height: 1.65;
        position: relative;
    }

    /* Document Header / Letterhead */
    .document-header {
        margin-bottom: 35px;
        padding-bottom: 22px;
        border-bottom: 3px double #1a2b4a;
    }

    .coat-of-arms-img {
        max-height: 90px;
        margin-bottom: 12px;
    }

    .document-kicker {
        font-size: 13px;
        letter-spacing: 3px;
        text-transform: uppercase;
        color: #7a1f1f;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .document-title {
        font-size: 27px;
        font-weight: bold;
        letter-spacing: 1px;
        color: #1a2b4a;
        margin-bottom: 6px;
    }

    .document-subtitle {
        font-size: 15px;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: #3a3a3a;
        margin-bottom: 14px;
    }

    .confidential-stamp {
        margin-top: 6px;
        color: #7a1f1f;
        font-weight: bold;
        font-size: 13px;
        letter-spacing: 2px;
        text-transform: uppercase;
        padding: 6px 18px;
        border: 2px solid #7a1f1f;
        display: inline-block;
        transform: rotate(-1.5deg);
    }

    /* Section Styling */
    .section {
        margin-bottom: 25px;
    }

    .section-title {
        background-color: #1a2b4a;
        color: #f5efe0;
        padding: 8px 16px;
        font-size: 15px;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-bottom: 15px;
        border-left: 4px solid #b9975a;
    }

    .section-content {
        padding: 0 15px;
    }

    .meta-label {
        font-weight: bold;
        color: #1a2b4a;
    }

    /* Reference Box */
    .reference-box {
        border: 1px solid #b9a76a;
        padding: 15px 18px;
        background-color: #f7f2e2;
    }

    .reference-meta {
        font-size: 14px;
        flex-wrap: wrap;
        gap: 8px;
    }

    /* Tables */
    .particulars-table {
        margin: 15px 0;
    }

    .table {
        border: 1px solid #d8cfa9;
    }

    .table th {
        background-color: #f2ede0;
        color: #1a2b4a;
        font-weight: bold;
    }

    /* Evidence Status */
    .evidence-status {
        margin-bottom: 15px;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 2px;
        font-weight: bold;
        font-size: 13px;
        letter-spacing: 0.5px;
        border: 1px solid currentColor;
    }

    .status-sufficient-evidence {
        background-color: #e7f2e9;
        color: #245c33;
    }

    .status-insufficient-evidence,
    .status-returned-to-police {
        background-color: #f7ecec;
        color: #7a1f1f;
    }

    .status-pending-review {
        background-color: #eef1f6;
        color: #1a2b4a;
    }

    /* Evidence Analysis */
    .evidence-analysis,
    .reassignment-details {
        background-color: #f7f2e2;
        padding: 15px;
        border-left: 3px solid #b9975a;
        font-style: italic;
    }

    /* Lists */
    .evidence-list, .recommendations-list {
        margin-left: 20px;
        margin-bottom: 15px;
    }

    /* Document Footer */
    .document-footer {
        margin-top: 40px;
        padding-top: 20px;
        border-top: 1px solid #d8cfa9;
    }

    .footer-info {
        margin-bottom: 20px;
        font-size: 14px;
    }

    .confidentiality-notice {
        background-color: #f7f2e2;
        padding: 15px 18px;
        border: 1px solid #d8cfa9;
        margin-bottom: 20px;
    }

    .confidentiality-notice h3 {
        color: #7a1f1f;
        font-size: 13px;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-bottom: 10px;
    }

    .government-footer {
        margin-top: 30px;
        font-size: 13px;
        color: #3a3a3a;
    }

    .government-seal-line {
        width: 80px;
        height: 2px;
        background-color: #b9975a;
        margin: 0 auto 12px;
    }

    /* Print Specific Styles */
    @media print {
        body {
            background-color: white;
            font-size: 12pt;
        }

        .case-review-document {
            box-shadow: none;
            border: none;
            padding: 0;
        }

        .action-buttons {
            display: none;
        }

        .page-break {
            page-break-after: always;
        }

        .section-title {
            background-color: #f0f0f0 !important;
            color: black !important;
            -webkit-print-color-adjust: exact;
        }
    }

    /* PDF generation specific styles */
    .pdf-generating .action-buttons {
        display: none !important;
    }

    @media print {
        body {
            background-color: white !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .section-title {
            background-color: #1a2b4a !important;
            color: #f5efe0 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .status-badge {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .confidential-stamp {
            color: #7a1f1f !important;
            border-color: #7a1f1f !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }

    /* Loading spinner for PDF generation */
    .pdf-loading {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .pdf-loading-content {
        text-align: center;
        padding: 20px;
        background-color: white;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    }

    .spinner {
        margin: 20px auto;
        width: 50px;
        height: 40px;
        text-align: center;
        font-size: 10px;
    }

    .spinner > div {
        background-color: #1a2b4a;
        height: 100%;
        width: 6px;
        display: inline-block;
        animation: sk-stretchdelay 1.2s infinite ease-in-out;
        margin: 0 2px;
    }

    .spinner .rect2 { animation-delay: -1.1s; }
    .spinner .rect3 { animation-delay: -1.0s; }
    .spinner .rect4 { animation-delay: -0.9s; }
    .spinner .rect5 { animation-delay: -0.8s; }

    @keyframes sk-stretchdelay {
        0%, 40%, 100% { transform: scaleY(0.4); }
        20% { transform: scaleY(1.0); }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
   document.addEventListener('DOMContentLoaded', function() {
      document.querySelectorAll('#download-btn').forEach(button => {
         button.addEventListener('click', function(e) {
            e.preventDefault();
            generatePDF(this);
         });
      });

      function generatePDF(buttonElement) {
         const loadingOverlay = document.createElement('div');
         loadingOverlay.className = 'pdf-loading';
         loadingOverlay.innerHTML = `
            <div class="pdf-loading-content">
               <h3>Generating PDF</h3>
               <div class="spinner">
                  <div class="rect1"></div>
                  <div class="rect2"></div>
                  <div class="rect3"></div>
                  <div class="rect4"></div>
                  <div class="rect5"></div>
               </div>
               <p>Please wait while we prepare your document...</p>
            </div>
         `;
         document.body.appendChild(loadingOverlay);

         const reviewDocument = buttonElement.closest('.case-review-document');
         const actionButtons = reviewDocument.querySelector('.action-buttons');
         const originalDisplay = actionButtons.style.display;
         actionButtons.style.display = 'none';
         reviewDocument.classList.add('pdf-generating');

         setTimeout(() => {
            let caseRef = 'case-review';
            const refElement = reviewDocument.querySelector('.reference-box h2');
            if (refElement) {
               const refText = refElement.textContent;
               const match = refText.match(/Matter Reference:\s*(.*)/i);
               if (match && match[1]) {
                  caseRef = match[1].trim();
               }
            }

            const filename = 'case-review-' + caseRef.replace(/[^a-z0-9]/gi, '-').toLowerCase() + '.pdf';

            html2canvas(reviewDocument, {
               scale: 2,
               useCORS: true,
               logging: false,
               allowTaint: true,
               backgroundColor: '#ffffff'
            }).then(canvas => {
               try {
                  const { jsPDF } = window.jspdf;
                  const imgWidth = 210;
                  const pageHeight = 295;
                  const imgHeight = canvas.height * imgWidth / canvas.width;
                  const pdf = new jsPDF('p', 'mm', 'a4');

                  let position = 0;
                  let heightLeft = imgHeight;

                  pdf.addImage(canvas, 'PNG', 0, 0, imgWidth, imgHeight);
                  heightLeft -= pageHeight;

                  while (heightLeft > 0) {
                     position = heightLeft - imgHeight;
                     pdf.addPage();
                     pdf.addImage(canvas, 'PNG', 0, position, imgWidth, imgHeight);
                     heightLeft -= pageHeight;
                  }

                  pdf.save(filename);
                  alert('PDF has been successfully generated and downloaded.');
               } catch (error) {
                  console.error('Error generating PDF:', error);
                  alert('There was an error generating the PDF: ' + error.message);
               }

               cleanupAfterPdfGeneration();
            }).catch(error => {
               console.error('Error capturing document:', error);
               alert('Failed to capture the document for PDF generation: ' + error.message);
               cleanupAfterPdfGeneration();
            });
         }, 200);

         function cleanupAfterPdfGeneration() {
            if (loadingOverlay.parentNode) {
               loadingOverlay.parentNode.removeChild(loadingOverlay);
            }
            actionButtons.style.display = originalDisplay;
            reviewDocument.classList.remove('pdf-generating');
         }
      }
   });
</script>
@endpush
@endsection
