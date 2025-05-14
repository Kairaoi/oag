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
  
  .badge-status {
    font-size: 14px;
    padding: 5px 10px;
    border-radius: 4px;
  }
  
  .badge-accepted {
    background-color: #d4edda;
    color: #155724;
  }
  
  .badge-guilty {
    background-color: #f8d7da;
    color: #721c24;
  }
  
  .badge-win {
    background-color: #cce5ff;
    color: #004085;
  }
</style>
@endpush

@section('content')
<div class="container my-5">

  @if($appealDetails->isEmpty())
    <div class="alert alert-warning">No appeal details found.</div>
  @else
    <div id="appeal-details-report">
      @foreach($appealDetails as $appeal)
      <div class="appeal-document p-5 border rounded shadow-sm bg-white mb-5 page-break-avoid" style="font-family: 'Georgia', serif; line-height: 1.7;">
        <div class="reference-box mb-3">
          <h2>APPEAL REFERENCE: {{ $appeal->appeal_case_number }}</h2>
        </div>

        <h2 class="text-center mb-4 text-uppercase" style="font-weight: bold;">Appeal Case Report</h2>

        <div class="case-header d-flex justify-content-between align-items-center mb-4">
          <div>
            <p class="fs-5 mb-1"><strong>Case Name:</strong> <span class="text-primary">{{ $appeal->case_name }}</span></p>
            <p class="mb-0"><strong>Appeal Filed:</strong> {{ date('F j, Y', strtotime($appeal->appeal_filing_date)) }}</p>
          </div>
          <div>
            <span class="badge-status badge-{{ $appeal->case_status }}">{{ ucfirst($appeal->case_status) }}</span>
          </div>
        </div>

        <hr class="my-4">

        <h4 class="text-decoration-underline">I. Appeal Overview</h4>
        <p>This appeal, referenced as <strong>{{ $appeal->appeal_case_number }}</strong>, was filed on <strong>{{ date('F j, Y', strtotime($appeal->appeal_filing_date)) }}</strong>. The judgment was delivered on <strong>{{ date('F j, Y', strtotime($appeal->judgment_delivered_date)) }}</strong>.</p>

        <div class="row gx-5 mt-4">
          <div class="col-md-6">
            <div class="outcome-box p-3 rounded" style="background-color: #f8f9fa;">
              <h5 class="mb-2">Court Outcome</h5>
              <p class="mb-1"><strong>Decision:</strong> <span class="text-{{ $appeal->court_outcome == 'guilty' ? 'danger' : 'success' }}">{{ ucfirst($appeal->court_outcome) }}</span></p>
              <p class="mb-1"><strong>Date:</strong> {{ date('F j, Y', strtotime($appeal->court_outcome_date)) }}</p>
              <p class="mb-0"><strong>Verdict:</strong> {{ ucfirst($appeal->verdict) }}</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="details-box p-3 rounded" style="background-color: #f8f9fa;">
              <h5 class="mb-2">Outcome Details</h5>
              <div class="border-start ps-3 text-muted fst-italic">
                {{ $appeal->court_outcome_details }}
              </div>
            </div>
          </div>
        </div>

        <div class="principle-box mt-4 p-3 rounded" style="background-color: #f8f9fa;">
          <h5 class="mb-2">Principle of Law Established</h5>
          <div class="border-start ps-3 text-muted fst-italic">
            {{ $appeal->decision_principle_established }}
          </div>
        </div>

        <hr class="my-4">

        <h4 class="text-decoration-underline">II. Legal Classifications</h4>
        <div class="row gx-4">
          <div class="col-md-6">
            <p><strong>Offences:</strong> 
              <span class="badge bg-secondary">{{ $appeal->offence_names }}</span>
            </p>
          </div>
          <div class="col-md-6">
            <p><strong>Categories:</strong> 
              <span class="badge bg-info text-dark">{{ $appeal->category_names }}</span>
            </p>
          </div>
        </div>

        <hr class="my-4">

        <h4 class="text-decoration-underline">III. Parties Information</h4>
        
        <div class="card mb-4">
          <div class="card-header bg-light">
            <h5 class="mb-0">Accused Details</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <p><strong>Name:</strong> {{ $appeal->accused_names }}</p>
                <p><strong>Contact:</strong> {{ $appeal->accused_contacts }}</p>
                <p><strong>Phone:</strong> {{ $appeal->accused_phones }}</p>
              </div>
              <div class="col-md-6">
                <p><strong>Gender:</strong> {{ $appeal->accused_genders }}</p>
                <p><strong>Age:</strong> {{ $appeal->accused_ages }} years</p>
                <p><strong>Date of Birth:</strong> {{ date('F j, Y', strtotime($appeal->accused_dob)) }}</p>
              </div>
            </div>
            <div class="row mt-2">
              <div class="col-12">
                <p><strong>Address:</strong><br>{!! nl2br(e($appeal->accused_addresses)) !!}</p>
                <p><strong>Island:</strong> {{ $appeal->accused_islands }}</p>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header bg-light">
            <h5 class="mb-0">Victim Details</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <p><strong>Name:</strong> {{ $appeal->victim_names }}</p>
                <p><strong>Contact:</strong> {{ $appeal->victim_contacts }}</p>
                <p><strong>Phone:</strong> {{ $appeal->victim_phones }}</p>
              </div>
              <div class="col-md-6">
                <p><strong>Gender:</strong> {{ $appeal->victim_genders }}</p>
                <p><strong>Age:</strong> {{ $appeal->victim_ages }} years ({{ $appeal->victim_age_groups }})</p>
                <p><strong>Date of Birth:</strong> {{ date('F j, Y', strtotime($appeal->victim_dob)) }}</p>
              </div>
            </div>
            <div class="row mt-2">
              <div class="col-12">
                <p><strong>Address:</strong><br>{!! nl2br(e($appeal->victim_addresses)) !!}</p>
                <p><strong>Island:</strong> {{ $appeal->victim_islands }}</p>
              </div>
            </div>
          </div>
        </div>

        <hr class="my-4">

        <h4 class="text-decoration-underline">IV. Record Information</h4>
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

        <div class="action-buttons mt-4 text-center">
          <button id="download-btn" class="btn btn-primary">Download Appeal Report PDF</button>
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
          <h3>Generating Appeal Report PDF</h3>
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

      const appealDocument = buttonElement.closest('.appeal-document');
      const actionButtons = appealDocument.querySelector('.action-buttons');
      const originalDisplay = actionButtons.style.display;

      actionButtons.style.display = 'none';
      appealDocument.classList.add('pdf-generating');

      setTimeout(() => {
        let appealRef = 'appeal-report';
        const refElement = appealDocument.querySelector('.reference-box h2');
        if (refElement) {
          const match = refElement.textContent.match(/APPEAL REFERENCE:\s*(.*)/i);
          if (match && match[1]) {
            appealRef = match[1].trim();
          }
        }
        const filename = 'appeal-report-' + appealRef.replace(/[^a-z0-9]/gi, '-').toLowerCase() + '.pdf';

        html2canvas(appealDocument, {
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
          alert('Appeal report PDF has been successfully generated and downloaded.');
          cleanup();
        }).catch(error => {
          alert('Error generating appeal report: ' + error.message);
          cleanup();
        });

        function cleanup() {
          loadingOverlay.remove();
          actionButtons.style.display = originalDisplay;
          appealDocument.classList.remove('pdf-generating');
        }
      }, 200);
    }
  });
</script>
@endpush