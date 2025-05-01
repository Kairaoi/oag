@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumbs -->
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mb-3">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-primary fw-medium">Home</a></li>
                <li class="breadcrumb-item active fw-medium" aria-current="page">Crime Board</li>
            </ol>
        </nav>
    </div>

    <!-- Header Section -->
    <div class="container mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="fw-bold mb-0">Crime Board</h1>
                <p class="text-muted mb-0">Office of the Attorney General</p>
            </div>
            <div class="col-md-4 text-md-end">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#quickSearchModal">
                    <i class="fas fa-search me-2"></i>Quick Search
                </button>
            </div>
        </div>
        <hr class="mt-3 mb-4">
    </div>

    <!-- Stats Overview -->
    <div class="container mb-4">
        <div class="row g-3">
            <div class="col-md-4 col-lg-2">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-primary-subtle rounded-3 p-3 me-3">
                                <i class="fas fa-gavel text-primary"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Pending Cases</h6>
                                <h3 class="mb-0">{{ $pendingCount }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-lg-2">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-success-subtle rounded-3 p-3 me-3">
                                <i class="fas fa-check-circle text-success"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Allocated</h6>
                                <h3 class="mb-0">{{ $allocatedCount }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-lg-2">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-info-subtle rounded-3 p-3 me-3">
                                <i class="fas fa-thumbs-up text-info"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Accepted</h6>
                                <h3 class="mb-0">{{ $acceptedCount }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-lg-2">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-danger-subtle rounded-3 p-3 me-3">
                                <i class="fas fa-times-circle text-danger"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Rejected</h6>
                                <h3 class="mb-0">{{ $rejectedCount }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-lg-2">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-warning-subtle rounded-3 p-3 me-3">
                                <i class="fas fa-balance-scale text-warning"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Lawyers</h6>
                                <h3 class="mb-0">{{ $lawyerCount }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-lg-2">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-secondary-subtle rounded-3 p-3 me-3">
                                <i class="fas fa-folder-open text-secondary"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Total Cases</h6>
                                <h3 class="mb-0">{{ $pendingCount + $allocatedCount + $acceptedCount + $rejectedCount }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation Tabs -->
    <div class="container mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <ul class="nav nav-tabs nav-fill border-0" id="crimeNavTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active py-3 px-4 border-0" id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard" type="button" role="tab" aria-controls="dashboard" aria-selected="true">
                            <i class="fas fa-home me-2"></i>Dashboard
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link py-3 px-4 border-0" href="{{ route('crime.criminalCase.index') }}" role="tab">
                            <i class="fas fa-briefcase me-2"></i>Cases
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link py-3 px-4 border-0" href="{{ route('crime.accused.index') }}" role="tab">
                            <i class="fas fa-user-shield me-2"></i>Accused
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link py-3 px-4 border-0" href="{{ route('crime.victim.index') }}" role="tab">
                            <i class="fas fa-user-injured me-2"></i>Victims
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link py-3 px-4 border-0" href="{{ route('crime.incident.index') }}" role="tab">
                            <i class="fas fa-exclamation-triangle me-2"></i>Incidents
                        </a>
                    </li>
                    <li class="nav-item dropdown" role="presentation">
                        <a class="nav-link dropdown-toggle py-3 px-4 border-0" href="#" id="referenceDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-book me-2"></i>References
                        </a>
                        <ul class="dropdown-menu border-0 shadow" aria-labelledby="referenceDropdown">
                            <li><a class="dropdown-item py-2" href="{{ route('crime.offence.index') }}">Offences</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('crime.reason.index') }}">Reasons</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('crime.category.index') }}">Categories</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('crime.island.index') }}">Islands</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Content Area -->
    <div class="container">
        <div class="tab-content" id="crimeTabContent">
            <!-- Dashboard Tab -->
            <div class="tab-pane fade show active" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
                <div class="row g-4">
                    <!-- Recent Cases -->
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Recent Cases</h5>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="caseFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            Filter
                                        </button>
                                        <ul class="dropdown-menu border-0 shadow-sm" aria-labelledby="caseFilterDropdown">
                                            <li><a class="dropdown-item" href="#">All Cases</a></li>
                                            <li><a class="dropdown-item" href="#">Pending</a></li>
                                            <li><a class="dropdown-item" href="#">Allocated</a></li>
                                            <li><a class="dropdown-item" href="#">Accepted</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <ul class="nav nav-tabs card-header-tabs border-0 mt-2">
                                    <li class="nav-item">
                                        <button class="nav-link active small py-2 px-3" data-bs-toggle="tab" data-bs-target="#allCases">All</button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link small py-2 px-3" data-bs-toggle="tab" data-bs-target="#pendingCases">Pending</button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link small py-2 px-3" data-bs-toggle="tab" data-bs-target="#allocatedCases">Allocated</button>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="allCases">
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Case #</th>
                                                        <th>Description</th>
                                                        <th>Date</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Sample case data - replace with actual data -->
                                                    <tr>
                                                        <td>C-2025-001</td>
                                                        <td>Theft at Main Street</td>
                                                        <td>12 Apr 2025</td>
                                                        <td><span class="badge bg-warning text-dark">Pending</span></td>
                                                        <td><a href="#" class="btn btn-sm btn-outline-primary">View</a></td>
                                                    </tr>
                                                    <tr>
                                                        <td>C-2025-002</td>
                                                        <td>Assault case in North District</td>
                                                        <td>10 Apr 2025</td>
                                                        <td><span class="badge bg-success">Allocated</span></td>
                                                        <td><a href="#" class="btn btn-sm btn-outline-primary">View</a></td>
                                                    </tr>
                                                    <tr>
                                                        <td>C-2025-003</td>
                                                        <td>Property dispute in East Town</td>
                                                        <td>08 Apr 2025</td>
                                                        <td><span class="badge bg-info">Accepted</span></td>
                                                        <td><a href="#" class="btn btn-sm btn-outline-primary">View</a></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="pendingCases">
                                        <!-- Pending cases would go here -->
                                        <p class="text-muted py-4 text-center">Loading pending cases...</p>
                                    </div>
                                    <div class="tab-pane fade" id="allocatedCases">
                                        <!-- Allocated cases would go here -->
                                        <p class="text-muted py-4 text-center">Loading allocated cases...</p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-top-0 text-end">
                                <a href="{{ route('crime.criminalCase.index') }}" class="btn btn-sm btn-primary">View All Cases</a>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side Widgets -->
                    <div class="col-lg-4">
                        <div class="row g-4">
                            <!-- Quick Actions -->
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-white border-bottom-0">
                                        <h5 class="mb-0">Quick Actions</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <a href="{{ route('crime.criminalCase.create') }}" class="btn btn-outline-primary w-100 py-3">
                                                    <i class="fas fa-plus-circle d-block mb-2"></i>
                                                    New Case
                                                </a>
                                            </div>
                                            <div class="col-6">
                                                <a href="{{ route('crime.accused.create') }}" class="btn btn-outline-primary w-100 py-3">
                                                    <i class="fas fa-user-plus d-block mb-2"></i>
                                                    New Accused
                                                </a>
                                            </div>
                                            <div class="col-6">
                                                <a href="{{ route('crime.victim.create') }}" class="btn btn-outline-primary w-100 py-3">
                                                    <i class="fas fa-user-shield d-block mb-2"></i>
                                                    New Victim
                                                </a>
                                            </div>
                                            <div class="col-6">
                                                <a href="{{ route('crime.incident.create') }}" class="btn btn-outline-primary w-100 py-3">
                                                    <i class="fas fa-exclamation-circle d-block mb-2"></i>
                                                    New Incident
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Case Analytics -->
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-white border-bottom-0">
                                        <h5 class="mb-0">Analytics Overview</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label small text-muted">Cases by Status</label>
                                            <div class="progress" style="height: 25px;">
                                                <div class="progress-bar bg-warning" style="width: {{ ($pendingCount/($pendingCount+$allocatedCount+$acceptedCount+$rejectedCount))*100 }}%" role="progressbar" aria-valuenow="{{ $pendingCount }}" aria-valuemin="0" aria-valuemax="100">Pending</div>
                                                <div class="progress-bar bg-success" style="width: {{ ($allocatedCount/($pendingCount+$allocatedCount+$acceptedCount+$rejectedCount))*100 }}%" role="progressbar" aria-valuenow="{{ $allocatedCount }}" aria-valuemin="0" aria-valuemax="100">Allocated</div>
                                                <div class="progress-bar bg-info" style="width: {{ ($acceptedCount/($pendingCount+$allocatedCount+$acceptedCount+$rejectedCount))*100 }}%" role="progressbar" aria-valuenow="{{ $acceptedCount }}" aria-valuemin="0" aria-valuemax="100">Accepted</div>
                                                <div class="progress-bar bg-danger" style="width: {{ ($rejectedCount/($pendingCount+$allocatedCount+$acceptedCount+$rejectedCount))*100 }}%" role="progressbar" aria-valuenow="{{ $rejectedCount }}" aria-valuemin="0" aria-valuemax="100">Rejected</div>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <a href="#" class="btn btn-sm btn-outline-secondary">View Detailed Reports</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Notifications -->
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-white border-bottom-0">
                                        <h5 class="mb-0">Notifications</h5>
                                    </div>
                                    <div class="card-body p-0">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item border-0 py-3">
                                                <div class="d-flex">
                                                    <div class="flex-shrink-0">
                                                        <span class="badge rounded-pill bg-info text-white p-2">
                                                            <i class="fas fa-bell"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <p class="mb-0">New case has been assigned to you</p>
                                                        <small class="text-muted">2 hours ago</small>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="list-group-item border-0 py-3">
                                                <div class="d-flex">
                                                    <div class="flex-shrink-0">
                                                        <span class="badge rounded-pill bg-success text-white p-2">
                                                            <i class="fas fa-check"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <p class="mb-0">Case C-2025-002 has been updated</p>
                                                        <small class="text-muted">Yesterday</small>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="card-footer bg-white border-top-0 text-center">
                                        <a href="#" class="btn btn-sm btn-outline-secondary">View All Notifications</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Search Modal -->
<div class="modal fade" id="quickSearchModal" tabindex="-1" aria-labelledby="quickSearchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="quickSearchModalLabel">Quick Search</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0 bg-light" placeholder="Search for case, accused, victim...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Search in</label>
                        <div class="d-flex flex-wrap gap-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="searchCases" checked>
                                <label class="form-check-label" for="searchCases">Cases</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="searchAccused" checked>
                                <label class="form-check-label" for="searchAccused">Accused</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="searchVictims" checked>
                                <label class="form-check-label" for="searchVictims">Victims</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="searchIncidents">
                                <label class="form-check-label" for="searchIncidents">Incidents</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Search</button>
            </div>
        </div>
    </div>
</div>

@endsection