@extends('layouts.app')

@section('content')
<style>
    .dashboard-header {
        background: linear-gradient(135deg, #0a2463 0%, #3e92cc 100%);
        padding: 2rem 0;
        margin-bottom: 2rem;
        color: white;
        border-radius: 0 0 2rem 2rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }
    .feature-card {
        border: none;
        border-radius: 1rem;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
    }
    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    .feature-card .card-body {
        padding: 1.5rem;
    }
    .feature-card .icon-box {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }
    .stats-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    .btn-action {
        border-radius: 0.75rem;
        padding: 0.6rem 1.2rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .reports-section {
        background: linear-gradient(135deg, #1e5f74 0%, #133b5c 100%);
        border-radius: 1.5rem;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
    }
</style>

<div class="container">
    <!-- Alert Messages -->
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Reports Section -->
    <div class="reports-section">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h3 class="fw-bold mb-3">Legal Reports & Analytics</h3>
                <p class="mb-4">Generate comprehensive reports on case progression, legal activities, and resource allocation for data-driven decision making.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('crime.reports.index') }}" class="btn btn-light btn-lg">
                        <i class="fas fa-file-alt me-2"></i> Generate Reports
                    </a>
                    <a href="#" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-chart-line me-2"></i> View Analytics
                    </a>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <img src="/api/placeholder/400/320" alt="Reports illustration" class="img-fluid">
            </div>
        </div>
    </div>

    <!-- Main Features -->
    <h4 class="fw-bold mb-4">Case Management Tools</h4>
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="feature-card card">
                <div class="card-body">
                    <div class="icon-box bg-primary bg-opacity-10">
                        <i class="fas fa-folder-open text-primary fa-lg"></i>
                    </div>
                    <h5 class="card-title">Criminal Case List</h5>
                    <p class="card-text text-muted">Browse, filter and manage all registered criminal cases in the system.</p>
                    <a href="{{ route('crime.criminalCase.index') }}" class="btn btn-primary btn-action w-100">
                        <i class="fas fa-list me-2"></i> View All Cases
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-card card">
                <div class="card-body">
                    <div class="icon-box bg-success bg-opacity-10">
                        <i class="fas fa-plus text-success fa-lg"></i>
                    </div>
                    <h5 class="card-title">New Criminal Case</h5>
                    <p class="card-text text-muted">Initiate a new criminal case with all required legal details.</p>
                    <a href="{{ route('crime.criminalCase.create') }}" class="btn btn-success btn-action w-100">
                        <i class="fas fa-plus-circle me-2"></i> Create Case
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-card card">
                <div class="card-body">
                    <div class="icon-box bg-info bg-opacity-10">
                        <i class="fas fa-search text-info fa-lg"></i>
                    </div>
                    <h5 class="card-title">Case Reviews</h5>
                    <p class="card-text text-muted">View cases pending or completed review processes.</p>
                    <a href="{{ route('crime.CaseReview.datatables') }}" class="btn btn-info btn-action w-100 text-white">
                        <i class="fas fa-clipboard-list me-2"></i> Review Cases
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-card card">
                <div class="card-body">
                    <div class="icon-box bg-warning bg-opacity-10">
                        <i class="fas fa-balance-scale text-warning fa-lg"></i>
                    </div>
                    <h5 class="card-title">Appeal Cases</h5>
                    <p class="card-text text-muted">Track and manage appeal cases with detailed documentation.</p>
                    <a href="{{ route('crime.criminalCase.appealDatatables') }}" class="btn btn-warning btn-action w-100 text-white">
                        <i class="fas fa-gavel me-2"></i> Manage Appeals
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-card card">
                <div class="card-body">
                    <div class="icon-box bg-dark bg-opacity-10">
                        <i class="fas fa-calendar-alt text-dark fa-lg"></i>
                    </div>
                    <h5 class="card-title">Court Hearings</h5>
                    <p class="card-text text-muted">Monitor upcoming hearings and courtroom schedules.</p>
                    <a href="{{ route('crime.court-hearings.index') }}" class="btn btn-dark btn-action w-100">
                        <i class="fas fa-calendar me-2"></i> View Hearings
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-card card">
                <div class="card-body">
                    <div class="icon-box bg-secondary bg-opacity-10">
                        <i class="fas fa-users text-secondary fa-lg"></i>
                    </div>
                    <h5 class="card-title">Victim Management</h5>
                    <p class="card-text text-muted">Register and update details of victims involved in cases.</p>
                    <a href="{{ route('crime.victim.index') }}" class="btn btn-secondary btn-action w-100">
                        <i class="fas fa-user-shield me-2"></i> Manage Victims
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection