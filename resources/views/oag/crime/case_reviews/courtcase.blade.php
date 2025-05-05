@extends('layouts.app')

@section('content')
<div class="container my-5">

  @if($courtCases->isEmpty())
    <div class="alert alert-warning">No court case data found.</div>
  @else
    @foreach($courtCases as $case)
    <div class="p-5 border rounded shadow-sm bg-white" style="font-family: 'Georgia', serif; line-height: 1.7;">
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
    </div>
    @endforeach
  @endif
  <button onclick="downloadPDF()" class="btn btn-primary">Download as PDF</button>

</div>
@endsection

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
  function downloadPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    let yPosition = 20;
    const courtCases = @json($courtCases); // Pass the PHP variable to JavaScript

    doc.setFontSize(18);
    doc.text("Court Case Report", 14, yPosition);
    yPosition += 10;

    courtCases.forEach((caseData) => {
      doc.setFontSize(12);

      // Case Title and Status
      doc.text("Case Title: " + caseData.case_name, 14, yPosition);
      yPosition += 10;

      doc.text("Status: " + caseData.case_status.charAt(0).toUpperCase() + caseData.case_status.slice(1), 14, yPosition);
      yPosition += 10;

      // Judicial Overview
      doc.text("I. Judicial Overview", 14, yPosition);
      yPosition += 10;
      doc.text("Case Number: " + caseData.high_court_case_number, 14, yPosition);
      yPosition += 10;
      doc.text("Filed on: " + caseData.charge_file_dated, 14, yPosition);
      yPosition += 10;
      doc.text("Judgment Date: " + caseData.judgment_delivered_date, 14, yPosition);
      yPosition += 10;

      doc.text("Final Court Outcome: " + caseData.court_outcome.charAt(0).toUpperCase() + caseData.court_outcome.slice(1), 14, yPosition);
      yPosition += 10;

      doc.text("Verdict: " + caseData.verdict.charAt(0).toUpperCase() + caseData.verdict.slice(1), 14, yPosition);
      yPosition += 10;

      // Legal Classifications
      doc.text("II. Legal Classifications", 14, yPosition);
      yPosition += 10;
      doc.text("Offences Involved: " + caseData.offence_names, 14, yPosition);
      yPosition += 10;
      doc.text("Legal Categories: " + caseData.category_names, 14, yPosition);
      yPosition += 10;

      // Accused Information
      doc.text("III. Accused Information", 14, yPosition);
      yPosition += 10;
      doc.text("Name: " + caseData.accused_names, 14, yPosition);
      yPosition += 10;

      // Handle additional sections for Accused and Victim Information
      doc.text("Address: " + caseData.accused_addresses, 14, yPosition);
      yPosition += 10;
      
      // If the content exceeds the bottom of the page, add a new one
      if (yPosition > 270) {
        doc.addPage();
        yPosition = 20; // Reset Y position for the new page
      }
    });

    // Save the PDF
    doc.save('court_case_report.pdf');
  }
</script>
