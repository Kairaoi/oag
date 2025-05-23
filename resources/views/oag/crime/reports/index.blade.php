@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
       
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-primary text-white py-3">
                    <h2 class="mb-0 fw-bold">Available Reports</h2>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted mb-4">Please select a report from the dropdown menu below to view detailed information.</p>
                    
                    <div class="form-group">
                        <label for="reportDropdown" class="form-label fw-bold mb-2">Select Report</label>
                        <select id="reportDropdown" class="form-select form-select-lg mb-3">
                            <option value="">-- Select a Report --</option>
                            @foreach($reports as $report)
                                <option value="{{ route('crime.reports.show', $report->id) }}">{{ $report->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <a href="" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                        </a>
                        <button type="button" id="viewReportBtn" class="btn btn-primary">
                            <i class="fas fa-file-alt me-2"></i> View Report
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Recently Viewed Reports (Optional) -->
            <div class="mt-4">
                <h4 class="mb-3">Recently Viewed Reports</h4>
                <div class="list-group">
                    <!-- This would typically be populated from your backend -->
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        Monthly Case Summary
                        <span class="badge bg-primary rounded-pill">2 days ago</span>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        Prosecution Success Rate
                        <span class="badge bg-primary rounded-pill">5 days ago</span>
                    </a>
                </div>
            </div>
        
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const reportDropdown = document.getElementById('reportDropdown');
        const viewReportBtn = document.getElementById('viewReportBtn');
        
        // Dropdown change navigation
        reportDropdown.addEventListener('change', function() {
            if (this.value) {
                window.location.href = this.value;
            }
        });
        
        // Button click handler (alternative to dropdown change)
        viewReportBtn.addEventListener('click', function() {
            const selectedValue = reportDropdown.value;
            if (selectedValue) {
                window.location.href = selectedValue;
            } else {
                alert('Please select a report first');
            }
        });
    });
</script>
@endpush
@endsection