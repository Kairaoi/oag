@extends('layouts.app')

@push('styles')
<style>
  .page-break-avoid {
    page-break-inside: avoid;
    break-inside: avoid;
  }

  h4 {
    page-break-after: avoid;
  }

  .pdf-loading {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background-color: rgba(255, 255, 255, 0.9);
    z-index: 9999;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
  }

  .spinner {
    margin: 15px auto;
    width: 60px;
    height: 40px;
    text-align: center;
    font-size: 10px;
  }

  .spinner > div {
    background-color: #007bff;
    height: 100%;
    width: 6px;
    display: inline-block;
    animation: stretchdelay 1.2s infinite ease-in-out;
  }

  .spinner .rect2 { animation-delay: -1.1s; }
  .spinner .rect3 { animation-delay: -1.0s; }
  .spinner .rect4 { animation-delay: -0.9s; }
  .spinner .rect5 { animation-delay: -0.8s; }

  @keyframes stretchdelay {
    0%, 40%, 100% { transform: scaleY(0.4); }
    20% { transform: scaleY(1.0); }
  }
</style>
@endpush

@section('content')
<div class="container my-5">

  @if($courtCases->isEmpty())
    <div class="alert alert-warning">No court case data found.</div>
  @else
    <div id="court-case-report">
      @foreach($courtCases as $case)
      <div class="case-review-document p-5 border rounded shadow-sm bg-white mb-5 page-break-avoid" style="font-family: 'Georgia', serif; line-height: 1.7;">
        <div class="reference-box mb-3">
          <h2>MATTER REFERENCE: {{ $case->high_court_case_number }}</h2>
        </div>

        <h2 class="text-center mb-4 text-uppercase" style="font-weight: bold;">Court Case Report</h2>

        <p><strong>Case Title:</strong> <span class="text-primary">{{ $case->case_name }}</span></p>
        <p><strong>Status:</strong> <span class="text-danger">{{ ucfirst($case->case_status) }}</span></p>

        <hr class="my-4">

        <h4 class="text-decoration-underline">I. Judicial Overview</h4>
        <p>This case, identified by the High Court Case Number <strong>{{ $case->high_court_case_number }}</strong>, was filed on <strong>{{ $case->charge_file_dated }}</strong>. The judgment was delivered on <strong>{{ $case->judgment_delivered_date }}</strong>.</p>

        <p><strong>Final Court Outcome:</strong> {{ ucfirst($case->court_outcome) }} (on {{ $case->court_outcome_date }})</p>
        <p><strong>Details:</strong></p>
        <div class="border-start ps-3 text-muted fst-italic" style="background-color: #f9f9f9;">
          {{ $case->court_outcome_details }}
        </div>

        <p class="mt-3"><strong>Verdict:</strong> {{ ucfirst($case->verdict) }}</p>
        <p><strong>Principle of Law Established:</strong></p>
        <div class="border-start ps-3 text-muted fst-italic" style="background-color: #f9f9f9;">
          {{ $case->decision_principle_established }}
        </div>

        <hr class="my-4">

        <h4 class="text-decoration-underline">II. Legal Classifications</h4>
        <p><strong>Offences Involved:</strong> {{ $case->offence_names }}</p>
        <p><strong>Legal Categories:</strong> {{ $case->category_names }}</p>

        <hr class="my-4">

        <h4 class="text-decoration-underline">III. Accused Information</h4>
        <div class="row">
          <div class="col-md-6">
            <p><strong>Name:</strong> {{ $case->accused_names }}</p>
            <p><strong>Address:</strong><br>{!! nl2br(e($case->accused_addresses)) !!}</p>
            <p><strong>Contact:</strong> {{ $case->accused_contacts }}</p>
            <p><strong>Phone:</strong> {{ $case->accused_phones }}</p>
          </div>
          <div class="col-md-6">
            <p><strong>Gender:</strong> {{ $case->accused_genders }}</p>
            <p><strong>Age:</strong> {{ $case->accused_ages }}</p>
            <p><strong>Date of Birth:</strong> {{ $case->accused_dob }}</p>
            <p><strong>Island:</strong> {{ $case->accused_islands }}</p>
          </div>
        </div>

        <hr class="my-4">

        <h4 class="text-decoration-underline">IV. Victim Information</h4>
        <div class="row">
          <div class="col-md-6">
            <p><strong>Name:</strong> {{ $case->victim_names }}</p>
            <p><strong>Address:</strong><br>{!! nl2br(e($case->victim_addresses)) !!}</p>
            <p><strong>Contact:</strong> {{ $case->victim_contacts }}</p>
            <p><strong>Phone:</strong> {{ $case->victim_phones }}</p>
          </div>
          <div class="col-md-6">
            <p><strong>Gender:</strong> {{ $case->victim_genders }}</p>
            <p><strong>Age:</strong> {{ $case->victim_ages }}</p>
            <p><strong>Date of Birth:</strong> {{ $case->victim_dob }}</p>
            <p><strong>Island:</strong> {{ $case->victim_islands }}</p>
            <p><strong>Age Group:</strong> {{ $case->victim_age_groups }}</p>
          </div>
        </div>

        <hr class="my-4">

        <h4 class="text-decoration-underline">V. Record & Audit Trail</h4>
        <p><strong>Created By:</strong> {{ $case->created_by_name }}</p>
        <p><strong>Updated By:</strong> {{ $case->updated_by_name ?? 'N/A' }}</p>
        <p><strong>Created At:</strong> {{ $case->created_at }}</p>
        <p><strong>Updated At:</strong> {{ $case->updated_at }}</p>

        <div class="action-buttons mt-3">
          <button id="download-btn" class="btn btn-sm btn-outline-primary">Download PDF</button>
        </div>
      </div>
      @endforeach
    </div>
  @endif

</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('#download-btn').forEach(button => {
      button.addEventListener('click', function (e) {
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
          const match = refElement.textContent.match(/MATTER REFERENCE:\s*(.*)/i);
          if (match && match[1]) {
            caseRef = match[1].trim();
          }
        }
        const filename = 'case-review-' + caseRef.replace(/[^a-z0-9]/gi, '-').toLowerCase() + '.pdf';

        html2canvas(reviewDocument, {
          scale: 2,
          useCORS: true,
          backgroundColor: '#ffffff'
        }).then(canvas => {
          const { jsPDF } = window.jspdf;
          const imgWidth = 210;
          const pageHeight = 295;
          const imgHeight = canvas.height * imgWidth / canvas.width;

          const pdf = new jsPDF('p', 'mm', 'a4');
          let position = 0;
          let heightLeft = imgHeight;

          pdf.addImage(canvas, 'PNG', 0, position, imgWidth, imgHeight);
          heightLeft -= pageHeight;

          while (heightLeft > 0) {
            position = heightLeft - imgHeight;
            pdf.addPage();
            pdf.addImage(canvas, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
          }

          pdf.save(filename);
          alert('PDF has been successfully generated and downloaded.');
          cleanup();
        }).catch(error => {
          alert('Error capturing document: ' + error.message);
          cleanup();
        });

        function cleanup() {
          loadingOverlay.remove();
          actionButtons.style.display = originalDisplay;
          reviewDocument.classList.remove('pdf-generating');
        }
      }, 200);
    }
  });
</script>
@endpush
