@extends('layouts.app')

@section('content')
<div class="container mt-4 mb-5">
    @foreach($caseReviews as $review)
    <div class="case-review-document">
        <!-- Document Header -->
        <div class="document-header">
            <div class="header-content text-center">
                <div class="coat-of-arms">
                    <!-- You can place an actual image here -->
                    <img src="{{ asset('images/kiribati-coat-arms.png') }}" alt="Kiribati Coat of Arms" class="coat-of-arms-img">
                </div>
                <h1 class="document-title">CASE REVIEW REPORT</h1>
                <div class="document-subtitle">OFFICE OF THE PUBLIC PROSECUTOR</div>
                <div class="document-subtitle">REPUBLIC OF KIRIBATI</div>
                <div class="confidential-stamp">CONFIDENTIAL LEGAL DOCUMENT</div>
            </div>
        </div>

        <!-- Case Reference -->
        <div class="section">
            <div class="reference-box">
                <h2 class="section-title">MATTER REFERENCE: {{ $review->case_name }}</h2>
                <div class="d-flex justify-content-between">
                    <div><strong>Review Date:</strong> {{ \Carbon\Carbon::parse($review->review_date)->format('F j, Y') }}</div>
                    <div><strong>Reviewing Attorney:</strong> {{ $review->created_by_name }}</div>
                </div>
            </div>
        </div>

        <!-- Summary Section -->
        <div class="section">
            <h2 class="section-title">SUMMARY OF PROCEEDINGS</h2>
            <div class="section-content">
                <p>This memorandum constitutes a formal review of the criminal proceedings against <strong>{{ $review->accused_names }}</strong> ("the Accused") in relation to charges of <strong>{{ $review->offence_names }}</strong>. This matter falls within the category of <strong>{{ $review->category_names }}</strong> under the jurisdiction of the Kiribati Courts.</p>
                
                <p>The Office of the Public Prosecutor has conducted this review to assess the current status of evidence, evaluate legal positions, and determine appropriate next steps in accordance with the interests of justice and the laws of Kiribati.</p>
            </div>
        </div>

        <!-- Accused Particulars -->
        <div class="section">
            <h2 class="section-title">ACCUSED PARTICULARS</h2>
            <div class="section-content">
                <p>The Accused in this matter is identified as follows:</p>
                
                <div class="particulars-table">
                    <table class="table table-striped">
                        <tr>
                            <th width="30%">Full Name:</th>
                            <td>{{ $review->accused_names }}</td>
                        </tr>
                        <tr>
                            <th>Gender:</th>
                            <td>{{ $review->accused_genders }}</td>
                        </tr>
                        <tr>
                            <th>Date of Birth:</th>
                            <td>{{ \Carbon\Carbon::parse($review->accused_dob)->format('F j, Y') }} ({{ $review->accused_ages }} years of age)</td>
                        </tr>
                        <tr>
                            <th>Island of Origin:</th>
                            <td>{{ $review->accused_islands }}</td>
                        </tr>
                        <tr>
                            <th>Current Residential Address:</th>
                            <td>{{ $review->accused_addresses }}</td>
                        </tr>
                        <tr>
                            <th>Contact Information:</th>
                            <td>
                                <div>Telephone: {{ $review->accused_phones }}</div>
                                <div>Other Contact: {{ $review->accused_contacts }}</div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Victim Particulars -->
        <div class="section">
            <h2 class="section-title">VICTIM PARTICULARS</h2>
            <div class="section-content">
                <p>The alleged victim in this matter is identified as follows:</p>
                
                <div class="particulars-table">
                    <table class="table table-striped">
                        <tr>
                            <th width="30%">Full Name:</th>
                            <td>{{ $review->victim_names }}</td>
                        </tr>
                        <tr>
                            <th>Gender:</th>
                            <td>{{ $review->victim_genders }}</td>
                        </tr>
                        <tr>
                            <th>Date of Birth:</th>
                            <td>{{ \Carbon\Carbon::parse($review->victim_dob)->format('F j, Y') }} ({{ $review->victim_ages }} years of age)</td>
                        </tr>
                        <tr>
                            <th>Age Category:</th>
                            <td>{{ $review->victim_age_groups }}</td>
                        </tr>
                        <tr>
                            <th>Island of Origin:</th>
                            <td>{{ $review->victim_islands }}</td>
                        </tr>
                        <tr>
                            <th>Current Residential Address:</th>
                            <td>{{ $review->victim_addresses }}</td>
                        </tr>
                        <tr>
                            <th>Contact Information:</th>
                            <td>
                                <div>Telephone: {{ $review->victim_phones }}</div>
                                <div>Other Contact: {{ $review->victim_contacts }}</div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Evidence Assessment -->
        <div class="section">
            <h2 class="section-title">EVIDENCE ASSESSMENT</h2>
            <div class="section-content">
                <div class="evidence-status">
                    <strong>Current Status of Evidence:</strong> 
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
                    {{ $review->offence_particulars }}
                </div>

            </div>
        </div>

        <!-- Case Assignment History -->
        <div class="section">
            <h2 class="section-title">CASE ASSIGNMENT HISTORY</h2>
            <div class="section-content">
                <p>This matter was initially assigned to <strong>{{ $review->from_lawyer_name }}</strong> and has subsequently been reassigned to <strong>{{ $review->to_lawyer_name }}</strong> as of <strong>{{ \Carbon\Carbon::parse($review->reallocation_date)->format('F j, Y') }}</strong>.</p>
                
                <h3>Reassignment Rationale:</h3>
                <div class="reassignment-details">
                    {{ $review->reallocation_details }}
                </div>
            </div>
        </div>

        <!-- Legal Issues and Considerations -->
        <div class="section">
            <h2 class="section-title">LEGAL ISSUES AND CONSIDERATIONS</h2>
            <div class="section-content">
                <p>The prosecution of this matter involves consideration of the following legal elements:</p>
                
                <h3>1. Elements of the Offence:</h3>
                <ul>
                    <li>The prosecution must establish beyond reasonable doubt that the Accused committed the acts constituting the offence of {{ $review->offence_names }}.</li>
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
            <h2 class="section-title">RECOMMENDATIONS AND NEXT STEPS</h2>
            <div class="section-content">
                <p>Based on the comprehensive review of this matter, the following recommendations are made:</p>
                
                <ol class="recommendations-list">
                    <li><strong>[Further Investigation Required/Proceed to Trial/Consider Resolution]</strong></li>
                    <li><strong>[Specific Evidence to be Gathered]</strong></li>
                    <li><strong>[Legal Strategy Recommendations]</strong></li>
                    <li><strong>[Timeline for Next Procedural Steps]</strong></li>
                </ol>
            </div>
        </div>

        <!-- Conclusion -->
        <div class="section">
            <h2 class="section-title">CONCLUSION</h2>
            <div class="section-content">
                <p>This review has been conducted in accordance with the prosecutorial guidelines of the Office of the Public Prosecutor of Kiribati and represents a thorough assessment of the available evidence and applicable law in this matter. The recommendations provided aim to ensure that justice is served while upholding the rights of all parties involved.</p>
            </div>
        </div>

        <!-- Document Footer -->
        <div class="document-footer">
            <div class="footer-info">
                <div><strong>Document Prepared By:</strong> {{ $review->created_by_name }}</div>
                <div><strong>Position:</strong> Prosecuting Attorney</div>
                <div><strong>Date of Report:</strong> {{ \Carbon\Carbon::parse($review->created_at)->format('F j, Y') }}</div>
                <div><strong>Last Updated:</strong> {{ $review->updated_at ? \Carbon\Carbon::parse($review->updated_at)->format('F j, Y') : 'Not updated' }}</div>
            </div>
            
            <div class="confidentiality-notice">
                <h3>CONFIDENTIALITY NOTICE:</h3>
                <p>This document contains legally privileged and confidential information intended solely for the use of authorized personnel within the Office of the Public Prosecutor. Unauthorized disclosure, copying, distribution, or use of the contents of this document is strictly prohibited and may result in legal action.</p>
            </div>
            
            <div class="government-footer text-center mt-4">
                <div><strong>Government of Kiribati</strong></div>
                <div>Office of the Public Prosecutor</div>
                <div>[Official Address]</div>
                <div>[Contact Information]</div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons text-center mt-4">
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print"></i> Print Report
            </button>
            <a href="" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to All Reviews
            </a>
            <a href="" class="btn btn-info">
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
    /* General Document Styling */
    .case-review-document {
        background-color: #fff;
        padding: 30px;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
        border: 1px solid #ddd;
        margin-bottom: 30px;
        font-family: 'Times New Roman', Times, serif;
        color: #333;
        line-height: 1.6;
    }
    
    /* Document Header */
    .document-header {
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #000;
    }
    
    .coat-of-arms-img {
        max-height: 100px;
        margin-bottom: 15px;
    }
    
    .document-title {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 10px;
    }
    
    .document-subtitle {
        font-size: 16px;
        margin-bottom: 5px;
    }
    
    .confidential-stamp {
        margin-top: 15px;
        color: #cc0000;
        font-weight: bold;
        font-size: 18px;
        padding: 5px 15px;
        border: 2px solid #cc0000;
        display: inline-block;
    }
    
    /* Section Styling */
    .section {
        margin-bottom: 25px;
    }
    
    .section-title {
        background-color: #003366;
        color: white;
        padding: 8px 15px;
        font-size: 18px;
        margin-bottom: 15px;
    }
    
    .section-content {
        padding: 0 15px;
    }
    
    /* Reference Box */
    .reference-box {
        border: 1px solid #ddd;
        padding: 15px;
        background-color: #f9f9f9;
    }
    
    /* Tables */
    .particulars-table {
        margin: 15px 0;
    }
    
    .table {
        border: 1px solid #ddd;
    }
    
    .table th {
        background-color: #f0f0f0;
    }
    
    /* Evidence Status */
    .evidence-status {
        margin-bottom: 15px;
    }
    
    .status-badge {
        padding: 5px 10px;
        border-radius: 3px;
        font-weight: bold;
    }
    
    .status-complete {
        background-color: #dff0d8;
        color: #3c763d;
    }
    
    .status-incomplete {
        background-color: #fcf8e3;
        color: #8a6d3b;
    }
    
    .status-pending {
        background-color: #d9edf7;
        color: #31708f;
    }
    
    /* Evidence Analysis */
    .evidence-analysis {
        background-color: #f9f9f9;
        padding: 15px;
        border-left: 3px solid #003366;
    }
    
    /* Reassignment Details */
    .reassignment-details {
        background-color: #f9f9f9;
        padding: 15px;
        border-left: 3px solid #003366;
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
        border-top: 1px solid #ddd;
    }
    
    .footer-info {
        margin-bottom: 20px;
    }
    
    .confidentiality-notice {
        background-color: #f9f9f9;
        padding: 15px;
        border: 1px solid #ddd;
        margin-bottom: 20px;
    }
    
    .confidentiality-notice h3 {
        color: #cc0000;
        font-size: 16px;
        margin-bottom: 10px;
    }
    
    .government-footer {
        margin-top: 30px;
        font-size: 14px;
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
    
    /* Improved print and PDF styling */
    @media print {
        body {
            background-color: white !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .section-title {
            background-color: #003366 !important;
            color: white !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .status-badge {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .confidential-stamp {
            color: #cc0000 !important;
            border-color: #cc0000 !important;
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
        background-color: #003366;
        height: 100%;
        width: 6px;
        display: inline-block;
        animation: sk-stretchdelay 1.2s infinite ease-in-out;
        margin: 0 2px;
    }
    
    .spinner .rect2 {
        animation-delay: -1.1s;
    }
    
    .spinner .rect3 {
        animation-delay: -1.0s;
    }
    
    .spinner .rect4 {
        animation-delay: -0.9s;
    }
    
    .spinner .rect5 {
        animation-delay: -0.8s;
    }
    
    @keyframes sk-stretchdelay {
        0%, 40%, 100% { 
            transform: scaleY(0.4);
        }  20% { 
            transform: scaleY(1.0);
        }
    }
</style>
@endpush
// Replace your current script section with this complete implementation
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
   document.addEventListener('DOMContentLoaded', function() {
      // Add PDF download functionality to all download buttons
      document.querySelectorAll('#download-btn').forEach(button => {
         button.addEventListener('click', function(e) {
            e.preventDefault();
            generatePDF(this);
         });
      });
      
      // Function to generate PDF from the case review document
      function generatePDF(buttonElement) {
         // Create and show loading overlay
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
         
         // Find the closest case review document to the button
         const reviewDocument = buttonElement.closest('.case-review-document');
         
         // Save original display state of action buttons
         const actionButtons = reviewDocument.querySelector('.action-buttons');
         const originalDisplay = actionButtons.style.display;
         
         // Hide action buttons for PDF generation
         actionButtons.style.display = 'none';
         
         // Add a class to indicate we're generating a PDF (for CSS purposes)
         reviewDocument.classList.add('pdf-generating');
         
         // Small delay to ensure DOM updates before capture
         setTimeout(() => {
            // Get case reference for filename
            let caseRef = 'case-review';
            const refElement = reviewDocument.querySelector('.reference-box h2');
            if (refElement) {
               const refText = refElement.textContent;
               const match = refText.match(/MATTER REFERENCE:\s*(.*)/i);
               if (match && match[1]) {
                  caseRef = match[1].trim();
               }
            }
            
            // Generate a clean filename
            const filename = 'case-review-' + caseRef.replace(/[^a-z0-9]/gi, '-').toLowerCase() + '.pdf';
            
            // Use html2canvas to capture the document
            html2canvas(reviewDocument, {
               scale: 2, // Higher scale for better quality
               useCORS: true,
               logging: false,
               allowTaint: true,
               backgroundColor: '#ffffff'
            }).then(canvas => {
               try {
                  // Initialize jsPDF
                  const { jsPDF } = window.jspdf;
                  
                  // Calculate dimensions for A4 paper
                  const imgWidth = 210; // A4 width in mm (210mm)
                  const pageHeight = 295; // A4 height in mm (slightly less than 297mm to account for margins)
                  const imgHeight = canvas.height * imgWidth / canvas.width;
                  
                  // Create new PDF document
                  const pdf = new jsPDF('p', 'mm', 'a4');
                  
                  // Add image to PDF with paging for long documents
                  let position = 0;
                  let heightLeft = imgHeight;
                  
                  // Add first page
                  pdf.addImage(canvas, 'PNG', 0, 0, imgWidth, imgHeight);
                  heightLeft -= pageHeight;
                  
                  // Add subsequent pages if needed
                  while (heightLeft > 0) {
                     position = heightLeft - imgHeight;
                     pdf.addPage();
                     pdf.addImage(canvas, 'PNG', 0, position, imgWidth, imgHeight);
                     heightLeft -= pageHeight;
                  }
                  
                  // Save the PDF file
                  pdf.save(filename);
                  
                  // Show success message
                  alert('PDF has been successfully generated and downloaded.');
               } catch (error) {
                  console.error('Error generating PDF:', error);
                  alert('There was an error generating the PDF: ' + error.message);
               }
               
               // Clean up
               cleanupAfterPdfGeneration();
            }).catch(error => {
               console.error('Error capturing document:', error);
               alert('Failed to capture the document for PDF generation: ' + error.message);
               cleanupAfterPdfGeneration();
            });
         }, 200);
         
         // Function to clean up after PDF generation
         function cleanupAfterPdfGeneration() {
            // Remove loading overlay
            if (loadingOverlay.parentNode) {
               loadingOverlay.parentNode.removeChild(loadingOverlay);
            }
            
            // Restore action buttons display
            actionButtons.style.display = originalDisplay;
            
            // Remove the PDF generation class
            reviewDocument.classList.remove('pdf-generating');
         }
      }
   });
</script>
@endpush
@endsection