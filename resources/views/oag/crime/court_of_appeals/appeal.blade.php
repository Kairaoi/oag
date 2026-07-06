@extends('layouts.app')

@push('styles')
<style>
  .page-break-avoid {
    page-break-inside: avoid;
    break-inside: avoid;
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

  @if($appealDetails->isEmpty())
    <div class="alert alert-warning">No Court of Appeal details found.</div>
  @else
    <div id="appeal-details-report">
      @foreach($appealDetails as $appeal)
      @php
        $coaDocRef = 'COA/' . str_pad($appeal->id, 4, '0', STR_PAD_LEFT) . '/' . date('Y', strtotime($appeal->created_at));
      @endphp
      <div class="appeal-document page-break-avoid mb-5" data-doc-ref="{{ $appeal->appeal_case_number }}">
        <x-official-document
          title="Court of Appeal Case Report"
          subtitle="Certified Record of Court of Appeal Proceedings"
          :doc-ref="$coaDocRef"
          secondary-label="Court of Appeal Reference"
          :secondary-value="$appeal->appeal_case_number"
          attestation="This is to certify that the particulars set out below constitute a true and accurate extract of the official Court of Appeal record maintained by the Office of the Attorney General, Republic of Kiribati, in respect of the matter referenced herein."
          certification-text="I certify that the foregoing particulars have been extracted from, and are consistent with, the official Court of Appeal record of the Office of the Attorney General as at the date of issue of this document, and that this document may be relied upon as an accurate statement of the record for the purpose stated."
        >
          <!-- 1. Court of Appeal Overview -->
          <div class="doc-section">
            <h2 class="doc-heading">1. Court of Appeal Overview</h2>
            <p>
              This Court of Appeal matter, referenced as <strong>{{ $appeal->appeal_case_number }}</strong>, was filed on
              <strong>{{ date('F j, Y', strtotime($appeal->appeal_filing_date)) }}</strong>.
              @if($appeal->judgment_delivered_date)
                The judgment was delivered on <strong>{{ date('F j, Y', strtotime($appeal->judgment_delivered_date)) }}</strong>.
              @endif
            </p>

            <table class="doc-table doc-table--kv">
              <tr>
                <th>Case Name</th>
                <td>{{ $appeal->case_name }}</td>
              </tr>
              <tr>
                <th>Filing Date</th>
                <td>{{ date('F j, Y', strtotime($appeal->appeal_filing_date)) }}</td>
              </tr>
              <tr>
                <th>Filing Date Source</th>
                <td>{{ $appeal->filing_date_source ? ucfirst($appeal->filing_date_source) : 'Not specified' }}</td>
              </tr>
              <tr>
                <th>Judgment Delivered</th>
                <td>{{ $appeal->judgment_delivered_date ? date('F j, Y', strtotime($appeal->judgment_delivered_date)) : 'Not on record' }}</td>
              </tr>
              <tr>
                <th>Court Outcome</th>
                <td>{{ $appeal->court_outcome ? ucfirst($appeal->court_outcome) : 'Not on record' }}</td>
              </tr>
            </table>
          </div>

          <!-- 2. Record Information -->
          <div class="doc-section">
            <h2 class="doc-heading">2. Record Information</h2>
            <table class="doc-table doc-table--kv">
              <tr>
                <th>Created By</th>
                <td>{{ $appeal->created_by_name }}</td>
              </tr>
              <tr>
                <th>Created At</th>
                <td>{{ date('F j, Y H:i', strtotime($appeal->created_at)) }}</td>
              </tr>
              <tr>
                <th>Updated By</th>
                <td>{{ $appeal->updated_by_name ?? 'Not on record' }}</td>
              </tr>
              <tr>
                <th>Updated At</th>
                <td>{{ date('F j, Y H:i', strtotime($appeal->updated_at)) }}</td>
              </tr>
            </table>
          </div>
        </x-official-document>

        <div class="action-buttons no-print mt-4 text-center">
          <a href="{{ route('crime.courtOfAppeal.edit', $appeal->id) }}" class="btn btn-outline-primary">
            <i class="fas fa-edit"></i> Edit
          </a>
          <button id="download-btn" class="btn btn-primary">Download Court of Appeal Report PDF</button>
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
          <h3>Generating Court of Appeal Report PDF</h3>
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
        const appealRef = appealDocument.dataset.docRef || 'court-of-appeal-report';
        const filename = 'court-of-appeal-report-' + appealRef.replace(/[^a-z0-9]/gi, '-').toLowerCase() + '.pdf';

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
          alert('Court of Appeal report PDF has been successfully generated and downloaded.');
          cleanup();
        }).catch(error => {
          alert('Error generating Court of Appeal report: ' + error.message);
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
