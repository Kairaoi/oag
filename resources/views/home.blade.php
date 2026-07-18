@extends('layouts.app')

@section('content')
<style>
    .section-heading {
        font-family: Georgia, 'Times New Roman', serif;
        font-weight: 700;
        color: #0a2463;
        letter-spacing: 0.3px;
        border-bottom: 2px solid #dfe3ea;
        padding-bottom: 0.5rem;
    }
    .feature-card {
        border: 1px solid #dfe3ea;
        border-top: 3px solid #0a2463;
        border-radius: 4px;
        box-shadow: none;
        transition: box-shadow 0.2s ease;
        height: 100%;
    }
    .feature-card:hover {
        box-shadow: 0 2px 12px rgba(10, 36, 99, 0.1);
    }
    .feature-card .card-body {
        padding: 1.5rem;
    }
    .feature-card .icon-box {
        width: 48px;
        height: 48px;
        border-radius: 4px;
        background: #0a2463;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }
    .feature-card .icon-box i {
        color: #fff !important;
    }
    .feature-card .card-title {
        font-weight: 700;
        color: #1a1a1a;
    }
    .btn-action {
        border-radius: 3px;
        padding: 0.6rem 1.2rem;
        font-weight: 600;
        letter-spacing: 0.2px;
        background-color: #0a2463;
        border-color: #0a2463;
        color: #fff;
    }
    .btn-action:hover {
        background-color: #08194d;
        border-color: #08194d;
        color: #fff;
    }
    .reports-section {
        background: #0a2463;
        border-radius: 4px;
        padding: 2.25rem 2.5rem;
        color: #fff;
        margin-bottom: 1.5rem;
    }
    .hero-badge {
        display: inline-block;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: #7fe3ee;
        border: 1px solid rgba(127, 227, 238, 0.4);
        border-radius: 3px;
        padding: 4px 10px;
        margin-bottom: 1rem;
    }
    .reports-section h3 {
        font-family: Georgia, 'Times New Roman', serif;
        font-size: 1.85rem;
    }
    .reports-section h3 .accent {
        color: #4fd6e6;
    }
    .reports-section p {
        color: #c3ccdf;
    }
    .reports-section .motto {
        font-size: 12.5px;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: #8b98b8;
        margin-bottom: 1.25rem;
    }
    .reports-section .btn-accent {
        background: #17a2b8;
        border-color: #17a2b8;
        color: #fff;
        border-radius: 3px;
        font-weight: 600;
    }
    .reports-section .btn-accent:hover {
        background: #128a9d;
        border-color: #128a9d;
        color: #fff;
    }
    .reports-section .btn-outline-light {
        border-radius: 3px;
        font-weight: 600;
    }
    .hero-stat {
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 4px;
        padding: 0.75rem 1rem;
        margin-bottom: 0.75rem;
    }
    .hero-stat .label {
        font-size: 10.5px;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: #8b98b8;
        margin-bottom: 2px;
    }
    .hero-stat .value {
        font-size: 14.5px;
        font-weight: 600;
        color: #fff;
    }
    .quick-action {
        border-radius: 4px;
        padding: 1.1rem 1.4rem;
        margin-bottom: 0.9rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .quick-action--primary {
        background: #0a2463;
        color: #fff;
    }
    .quick-action--primary .qa-label {
        color: #7fe3ee;
    }
    .quick-action--primary .qa-desc {
        color: #c3ccdf;
    }
    .quick-action--secondary {
        background: #fff;
        border: 1px solid #dfe3ea;
        border-left: 4px solid #17a2b8;
    }
    .quick-action--secondary .qa-label {
        color: #17a2b8;
    }
    .quick-action--secondary .qa-desc {
        color: #667;
    }
    .qa-label {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-bottom: 4px;
    }
    .qa-title {
        font-weight: 700;
        margin-bottom: 2px;
    }
    .qa-desc {
        font-size: 13.5px;
        color: #667;
        margin: 0;
    }
    .card-number {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1px;
        color: #9aa5bd;
        margin-bottom: 0.5rem;
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

    <!-- Hero / Reports Section -->
    <div class="reports-section">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="hero-badge">Official Case Management System</span>
                <h3 class="fw-bold mb-3">Criminal Case <span class="accent">Management Portal</span></h3>
                <p class="mb-2">Register, review and track criminal cases through every stage &mdash; from initial complaint to court hearing and appeal &mdash; in one system used across the Office of the Attorney General.</p>
                <div class="motto">Te Mauri &middot; Te Raoi &middot; Ao Te Tabomoa</div>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('crime.criminalCase.create') }}" class="btn btn-accent btn-lg">
                        <i class="fas fa-plus-circle me-2"></i> New Case
                    </a>
                    <a href="{{ route('crime.reports.index') }}" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-file-alt me-2"></i> Generate Reports
                    </a>
                </div>
            </div>
            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="hero-stat">
                    <div class="label">Case Types</div>
                    <div class="value">Criminal &middot; Civil &middot; Appeals</div>
                </div>
                <div class="hero-stat">
                    <div class="label">Access</div>
                    <div class="value">Role-based &amp; fully audited</div>
                </div>
                <div class="hero-stat mb-0">
                    <div class="label">Availability</div>
                    <div class="value">Internal system &middot; 24/7</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-action quick-action--primary">
        <div>
            <div class="qa-label">Looking for an existing case?</div>
            <div class="qa-title">Search the full criminal case register</div>
            <p class="qa-desc">Filter by status, offence or party to find a case already in the system.</p>
        </div>
        <a href="{{ route('crime.criminalCase.index') }}" class="btn btn-accent">View All Cases &rarr;</a>
    </div>
    <div class="quick-action quick-action--secondary">
        <div>
            <div class="qa-label">Have a case awaiting action?</div>
            <div class="qa-title">Continue a pending case review</div>
            <p class="qa-desc">Pick up cases pending or completed review without re-entering details.</p>
        </div>
        <a href="{{ route('crime.CaseReview.index') }}" class="btn btn-action">Review Cases &rarr;</a>
    </div>

    <!-- Main Features -->
    <h4 class="section-heading mb-4 mt-5">Case Management Tools</h4>
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="feature-card card">
                <div class="card-body">
                    <div class="card-number">MODULE 01</div>
                    <div class="icon-box">
                        <i class="fas fa-folder-open fa-lg"></i>
                    </div>
                    <h5 class="card-title">Criminal Case List</h5>
                    <p class="card-text text-muted">Browse, filter and manage all registered criminal cases in the system.</p>
                    <a href="{{ route('crime.criminalCase.index') }}" class="btn btn-action w-100">
                        <i class="fas fa-list me-2"></i> View All Cases
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-card card">
                <div class="card-body">
                    <div class="card-number">MODULE 02</div>
                    <div class="icon-box">
                        <i class="fas fa-plus fa-lg"></i>
                    </div>
                    <h5 class="card-title">New Criminal Case</h5>
                    <p class="card-text text-muted">Initiate a new criminal case with all required legal details.</p>
                    <a href="{{ route('crime.criminalCase.create') }}" class="btn btn-action w-100">
                        <i class="fas fa-plus-circle me-2"></i> Create Case
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-card card">
                <div class="card-body">
                    <div class="card-number">MODULE 03</div>
                    <div class="icon-box">
                        <i class="fas fa-search fa-lg"></i>
                    </div>
                    <h5 class="card-title">Case Reviews</h5>
                    <p class="card-text text-muted">View cases pending or completed review processes.</p>
                    <a href="{{ route('crime.CaseReview.index') }}" class="btn btn-action w-100">
                        <i class="fas fa-clipboard-list me-2"></i> Review Cases
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-card card">
                <div class="card-body">
                    <div class="card-number">MODULE 04</div>
                    <div class="icon-box">
                        <i class="fas fa-balance-scale fa-lg"></i>
                    </div>
                    <h5 class="card-title">Appeal Cases</h5>
                    <p class="card-text text-muted">Track and manage appeal cases with detailed documentation.</p>
                    <a href="{{ route('crime.appeal.index') }}" class="btn btn-action w-100">
                        <i class="fas fa-gavel me-2"></i> Manage Appeals
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-card card">
                <div class="card-body">
                    <div class="card-number">MODULE 05</div>
                    <div class="icon-box">
                        <i class="fas fa-calendar-alt fa-lg"></i>
                    </div>
                    <h5 class="card-title">Court Hearings</h5>
                    <p class="card-text text-muted">Monitor upcoming hearings and courtroom schedules.</p>
                    <a href="{{ route('crime.court-hearings.index') }}" class="btn btn-action w-100">
                        <i class="fas fa-calendar me-2"></i> View Hearings
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-card card">
                <div class="card-body">
                    <div class="card-number">MODULE 06</div>
                    <div class="icon-box">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                    <h5 class="card-title">Victim Management</h5>
                    <p class="card-text text-muted">Register and update details of victims involved in cases.</p>
                    <a href="{{ route('crime.victim.index') }}" class="btn btn-action w-100">
                        <i class="fas fa-user-shield me-2"></i> Manage Victims
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection