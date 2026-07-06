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
    @php
        $reviewDocRef = 'CR/' . str_pad($review->id, 4, '0', STR_PAD_LEFT) . '/' . date('Y', strtotime($review->created_at));
    @endphp
    <div class="case-review-document mb-5" data-doc-ref="{{ $review->id }}">
        <x-official-document
            title="Case Review Report"
            subtitle="Certified Record of Case Review"
            :doc-ref="$reviewDocRef"
            secondary-label="Matter Reference"
            :secondary-value="$review->case_name"
            attestation="This is to certify that the particulars set out below constitute a true and accurate extract of the official case review record maintained by the Office of the Attorney General, Republic of Kiribati, in respect of the matter referenced herein."
            certification-text="I certify that the foregoing particulars have been extracted from, and are consistent with, the official case review record of the Office of the Attorney General as at the date of issue of this document, and that this document may be relied upon as an accurate statement of the review for the purpose stated."
        >
            <!-- 1. Review Reference -->
            <div class="doc-section">
                <h2 class="doc-heading">1. Review Reference</h2>
                <table class="doc-table doc-table--kv">
                    <tr>
                        <th>Matter</th>
                        <td>{{ $review->case_name }}</td>
                    </tr>
                    <tr>
                        <th>Review Date</th>
                        <td>{{ \Carbon\Carbon::parse($review->review_date)->format('F j, Y') }}</td>
                    </tr>
                    <tr>
                        <th>Reviewing Attorney</th>
                        <td>{{ $review->created_by_name ?? 'Not assigned' }}</td>
                    </tr>
                </table>
            </div>

            <!-- 2. Summary of Proceedings -->
            <div class="doc-section">
                <h2 class="doc-heading">2. Summary of Proceedings</h2>
                <p>This memorandum constitutes a formal review of the criminal proceedings against <strong>{{ $review->accused_names ?? 'the Accused' }}</strong> ("the Accused") in relation to charges of <strong>{{ $review->offence_names ?? 'Not yet specified' }}</strong>. This matter falls within the category of <strong>{{ $review->category_names ?? 'Not yet specified' }}</strong> under the jurisdiction of the Kiribati Courts.</p>
                <p>The Office of the Attorney General has conducted this review to assess the current status of evidence, evaluate legal positions, and determine appropriate next steps in accordance with the interests of justice and the laws of Kiribati.</p>
            </div>

            <!-- 3. Accused Particulars -->
            <div class="doc-section">
                <h2 class="doc-heading">3. Accused Particulars</h2>
                <table class="doc-table doc-table--kv">
                    <tr>
                        <th>Full Name</th>
                        <td>{{ $review->accused_names ?? 'Not on record' }}</td>
                    </tr>
                    <tr>
                        <th>Gender</th>
                        <td>{{ $review->accused_genders ?? 'Not on record' }}</td>
                    </tr>
                    <tr>
                        <th>Date of Birth</th>
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
                        <th>Island of Origin</th>
                        <td>{{ $review->accused_islands ?? 'Not on record' }}</td>
                    </tr>
                    <tr>
                        <th>Residential Address</th>
                        <td>{{ $review->accused_addresses ?? 'Not on record' }}</td>
                    </tr>
                    <tr>
                        <th>Contact Information</th>
                        <td>{{ $review->accused_phones ?? 'Not on record' }} &middot; {{ $review->accused_contacts ?? 'Not on record' }}</td>
                    </tr>
                </table>
            </div>

            <!-- 4. Victim Particulars -->
            <div class="doc-section">
                <h2 class="doc-heading">4. Victim Particulars</h2>
                <table class="doc-table doc-table--kv">
                    <tr>
                        <th>Full Name</th>
                        <td>{{ $review->victim_names ?? 'Not on record' }}</td>
                    </tr>
                    <tr>
                        <th>Gender</th>
                        <td>{{ $review->victim_genders ?? 'Not on record' }}</td>
                    </tr>
                    <tr>
                        <th>Date of Birth</th>
                        <td>
                            @if($review->victim_dob)
                                {{ \Carbon\Carbon::parse($review->victim_dob)->format('F j, Y') }} ({{ $review->victim_ages }} years of age)
                            @else
                                Not on record
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Age Category</th>
                        <td>{{ $review->victim_age_groups ?? 'Not on record' }}</td>
                    </tr>
                    <tr>
                        <th>Island of Origin</th>
                        <td>{{ $review->victim_islands ?? 'Not on record' }}</td>
                    </tr>
                    <tr>
                        <th>Residential Address</th>
                        <td>{{ $review->victim_addresses ?? 'Not on record' }}</td>
                    </tr>
                    <tr>
                        <th>Contact Information</th>
                        <td>{{ $review->victim_phones ?? 'Not on record' }} &middot; {{ $review->victim_contacts ?? 'Not on record' }}</td>
                    </tr>
                </table>
            </div>

            <!-- 5. Evidence Assessment -->
            <div class="doc-section">
                <h2 class="doc-heading">5. Evidence Assessment</h2>
                <table class="doc-table doc-table--kv">
                    <tr>
                        <th>Current Status of Evidence</th>
                        <td>{{ ucfirst(str_replace('_', ' ', $review->evidence_status)) }}</td>
                    </tr>
                </table>

                <p class="doc-heading" style="font-size:13px; margin-top:18px;">Key Evidentiary Materials</p>
                <ol class="doc-list">
                    <li>Witness statements</li>
                    <li>Material evidence</li>
                    <li>Police reports</li>
                    <li>Medical reports (if applicable)</li>
                    <li>Expert testimony (if applicable)</li>
                </ol>

                <p class="doc-heading" style="font-size:13px; margin-top:18px;">Offence Particulars</p>
                <p class="doc-quote">{{ $review->offence_particulars ?: 'Not provided.' }}</p>
            </div>

            <!-- 6. Case Assignment History -->
            <div class="doc-section">
                <h2 class="doc-heading">6. Case Assignment History</h2>
                @if($review->to_lawyer_name)
                    <p>This matter was initially assigned to <strong>{{ $review->from_lawyer_name ?? 'no lawyer' }}</strong> and has subsequently been reassigned to <strong>{{ $review->to_lawyer_name }}</strong> as of <strong>{{ \Carbon\Carbon::parse($review->reallocation_date)->format('F j, Y') }}</strong>.</p>
                    <p class="doc-heading" style="font-size:13px; margin-top:18px;">Reassignment Rationale</p>
                    <p class="doc-quote">{{ $review->reallocation_details ?: 'Not provided.' }}</p>
                @else
                    <p>No reassignment has been recorded for this matter.</p>
                @endif
            </div>

            <!-- 7. Legal Issues and Considerations -->
            <div class="doc-section">
                <h2 class="doc-heading">7. Legal Issues and Considerations</h2>
                <p>The prosecution of this matter involves consideration of the following legal elements:</p>

                <p class="doc-heading" style="font-size:13px; margin-top:14px;">1. Elements of the Offence</p>
                <ul class="doc-list">
                    <li>The prosecution must establish beyond reasonable doubt that the Accused committed the acts constituting the offence of {{ $review->offence_names ?? 'the alleged offence' }}.</li>
                    <li>Each element of the offence must be proven according to the Criminal Code of Kiribati.</li>
                </ul>

                <p class="doc-heading" style="font-size:13px; margin-top:14px;">2. Potential Defences</p>
                <ul class="doc-list">
                    <li>Assessment of available defences under Kiribati law.</li>
                    <li>Evaluation of mitigating factors that may be relevant to sentencing.</li>
                </ul>

                <p class="doc-heading" style="font-size:13px; margin-top:14px;">3. Evidentiary Challenges</p>
                <ul class="doc-list">
                    <li>Analysis of any evidentiary gaps or challenges.</li>
                    <li>Consideration of admissibility issues.</li>
                </ul>

                <p class="doc-heading" style="font-size:13px; margin-top:14px;">4. Public Interest Considerations</p>
                <ul class="doc-list">
                    <li>Assessment of prosecution in light of public interest factors.</li>
                    <li>Consideration of victim interests and community impact.</li>
                </ul>
            </div>

            <!-- 8. Recommendations and Next Steps -->
            <div class="doc-section">
                <h2 class="doc-heading">8. Recommendations and Next Steps</h2>
                <p>Based on the comprehensive review of this matter, the following recommendations are made:</p>
                <ol class="doc-list">
                    <li><strong>[Further Investigation Required / Proceed to Trial / Consider Resolution]</strong></li>
                    <li><strong>[Specific Evidence to be Gathered]</strong></li>
                    <li><strong>[Legal Strategy Recommendations]</strong></li>
                    <li><strong>[Timeline for Next Procedural Steps]</strong></li>
                </ol>
            </div>

            <!-- 9. Conclusion -->
            <div class="doc-section">
                <h2 class="doc-heading">9. Conclusion</h2>
                <p>This review has been conducted in accordance with the prosecutorial guidelines of the Office of the Attorney General of Kiribati and represents a thorough assessment of the available evidence and applicable law in this matter. The recommendations provided aim to ensure that justice is served while upholding the rights of all parties involved.</p>
            </div>

            <!-- Confidentiality Notice -->
            <div class="doc-section">
                <h2 class="doc-heading">Confidentiality Notice</h2>
                <p class="doc-quote">This document contains legally privileged and confidential information intended solely for the use of authorized personnel within the Office of the Attorney General. Unauthorized disclosure, copying, distribution, or use of the contents of this document is strictly prohibited and may result in legal action.</p>
            </div>
        </x-official-document>

        <!-- Action Buttons -->
        <div class="action-buttons no-print text-center mt-4">
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
    @media print {
        .page-break {
            page-break-after: always;
        }
    }

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

    .pdf-generating .action-buttons {
        display: none !important;
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
            const caseRef = reviewDocument.dataset.docRef || 'case-review';
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
