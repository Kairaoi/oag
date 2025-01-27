@extends('layouts.app')

@section('content')

<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb" style="background: none; box-shadow: none;">
            <li class="breadcrumb-item"><a href="#" style="color: #ff4b2b; font-weight: bold;">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page" style="color: #333; font-weight: bold;">Civil Matters Board</li>
        </ol>
    </nav>

    <!-- Main Title -->
    <h1 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #333; text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.3); border-bottom: 4px solid #ff4b2b; padding-bottom: 12px; transition: transform 0.3s ease;">
        Civil Matters Board
    </h1>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs nav-justified" id="myTab" role="tablist" style="border-bottom: none;">
        @foreach ([ 
            ['name' => 'Home', 'active' => true, 'background' => 'linear-gradient(135deg, #ff416c, #ff4b2b)', 'color' => 'white', 'route' => null],
            ['name' => 'Civil Case', 
             'route' => route('civil.civilcase.index'), 
             'background' => 'linear-gradient(135deg, #4caf50, #388e3c)', 
             'color' => 'white', 'active' => false],
            ['name' => 'Parties', 'background' => 'linear-gradient(135deg, #2196f3, #1976d2)', 'color' => 'white', 'route' => null],
            ['name' => 'Claimant', 'background' => 'linear-gradient(135deg, #ffeb3b, #fbc02d)', 'color' => 'black', 'route' => null],
            ['name' => 'Incident', 'background' => 'linear-gradient(135deg, #ff5722, #e64a19)', 'color' => 'white', 'route' => null],
            ['name' => 'Court Attendance', 'background' => 'linear-gradient(135deg, #673ab7, #5e35b1)', 'color' => 'white', 'route' => route('civil.courtattendance.index')]
        ] as $tab)
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ $tab['active'] ?? false ? 'active' : '' }}" 
                   id="{{ strtolower(str_replace(' ', '-', $tab['name'])) }}-tab" 
                   href="{{ $tab['route'] ?? '#' }}" 
                   role="tab" 
                   aria-controls="{{ strtolower(str_replace(' ', '-', $tab['name'])) }}" 
                   aria-selected="{{ $tab['active'] ?? false ? 'true' : 'false' }}" 
                   style="background: {{ $tab['background'] }}; color: {{ $tab['color'] }}; border-radius: 25px; font-weight: bold; text-transform: uppercase; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4); transition: background 0.3s ease, transform 0.3s ease;">
                    {{ $tab['name'] }}
                </a>
            </li>
        @endforeach
    </ul>

    <!-- Tab content -->
    <div class="tab-content mt-4" id="myTabContent">
        <!-- Home Tab -->
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            <div class="mt-3">
                <p class="lead" style="font-family: 'Arial', sans-serif; font-size: 18px; color: #555;">
                    Welcome to the Civil Matters Board managed by the Office of the Attorney General. This platform provides comprehensive information about civil cases, issues, involved parties, and claimants. Explore various aspects of civil matters and stay updated on the latest developments.
                </p>

                <div class="row">
                    <!-- Info Cards -->
                    <div class="col-md-4 mb-3">
                        <div class="card" style="border: 2px solid #ff4b2b; border-radius: 20px; box-shadow: 0 6px 14px rgba(0, 0, 0, 0.4); overflow: hidden; transition: transform 0.3s ease;">
                            <div class="card-header" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); color: white; border-radius: 20px 20px 0 0; padding: 15px; font-weight: bold;">
                                Latest Updates
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">Recent Civil Case Highlights</h5>
                                <p class="card-text">Stay informed about the latest case developments and important legal updates handled by our office.</p>
                                <a href="#" class="btn btn-danger" style="background: #ff4b2b; border: none; transition: background 0.3s ease;">View Details</a>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Info Cards -->
                    <!-- Similar layout for other info cards here -->

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
