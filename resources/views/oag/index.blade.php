@extends('layouts.app')

@section('content')

<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb" style="background: none; box-shadow: none;">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color: #ff4b2b; font-weight: bold;">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page" style="color: #333; font-weight: bold;">Crime Board</li>
        </ol>
    </nav>

    <!-- Main Title -->
    <h1 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #333; text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.3); border-bottom: 4px solid #ff4b2b; padding-bottom: 12px; transition: transform 0.3s ease;">
        Crime Board
    </h1>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs nav-justified" id="myTab" role="tablist" style="border-bottom: none;">
        @foreach ([
            ['name' => 'Home', 'route' => '#home', 'active' => true, 'background' => 'linear-gradient(135deg, #ff416c, #ff4b2b)', 'color' => 'white'],
            ['name' => 'Case', 'route' => route('crime.criminalCase.index'), 'background' => 'linear-gradient(135deg, #4caf50, #388e3c)', 'color' => 'white'],
            ['name' => 'Accused', 'route' => route('crime.accused.index'), 'background' => 'linear-gradient(135deg, #2196f3, #1976d2)', 'color' => 'white'],
            ['name' => 'Victim', 'route' => route('crime.victim.index'), 'background' => 'linear-gradient(135deg, #ffeb3b, #fbc02d)', 'color' => 'black'],
            ['name' => 'Incident', 'route' => route('crime.incident.index'), 'background' => 'linear-gradient(135deg, #ff5722, #e64a19)', 'color' => 'white']
        ] as $tab)
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ $tab['active'] ?? false ? 'active' : '' }}" id="{{ strtolower($tab['name']) }}-tab" 
                   href="{{ $tab['route'] }}" 
                   role="tab" 
                   aria-controls="{{ strtolower($tab['name']) }}" 
                   aria-selected="{{ $tab['active'] ?? false ? 'true' : 'false' }}" 
                   style="background: {{ $tab['background'] }}; color: {{ $tab['color'] }}; border-radius: 25px; font-weight: bold; text-transform: uppercase; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4); transition: background 0.3s ease, transform 0.3s ease;">
                    {{ $tab['name'] }}
                </a>
            </li>
        @endforeach
        <li class="nav-item dropdown" role="presentation">
            <a class="nav-link dropdown-toggle" id="reference-dropdown" data-bs-toggle="dropdown" aria-expanded="false" 
               style="background: radial-gradient(circle at top left, #ff416c, #ff4b2b); color: white; border-radius: 25px; font-weight: bold; text-transform: uppercase; box-shadow: 0 8px 16px rgba(0, 0, 0, 0.6); transition: background 0.4s ease, transform 0.4s ease;">
                Reference Tables
            </a>
            <ul class="dropdown-menu" aria-labelledby="reference-dropdown" style="background: linear-gradient(145deg, #f0f0f0, #e0e0e0); border-radius: 20px; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5); padding: 10px;">
                @foreach ([
                    ['name' => 'Offence', 'route' => route('crime.offence.index')],
                    ['name' => 'Reason', 'route' => route('crime.reason.index')],
                    ['name' => 'Category', 'route' => route('crime.category.index')],
                    ['name' => 'Island', 'route' => route('crime.island.index')],
                ] as $item)
                    <li>
                        <a class="dropdown-item" href="{{ $item['route'] }}" style="color: #ff4b2b; background: radial-gradient(circle, #ffffff, #f0f0f0); border-radius: 10px; padding: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); transition: background 0.3s ease, transform 0.3s ease; font-weight: bold;">
                            {{ $item['name'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </li>
    </ul>

    <!-- Tab content -->
    <div class="tab-content mt-4" id="myTabContent">
        <!-- Home Tab -->
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            <div class="mt-3">
                <p class="lead" style="font-family: 'Arial', sans-serif; font-size: 18px; color: #555;">
                    Welcome to the Crime Board managed by the Office of the Attorney General. This platform provides comprehensive information about criminal cases, offences, accused individuals, and victims. Explore various aspects of the criminal justice system and stay updated on the latest developments.
                </p>

                <div class="row">
                    <!-- Info Card 1 -->
                    <div class="col-md-4 mb-3">
                        <div class="card" style="border: 2px solid #ff4b2b; border-radius: 20px; box-shadow: 0 6px 14px rgba(0, 0, 0, 0.4); overflow: hidden; transition: transform 0.3s ease;">
                            <div class="card-header" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); color: white; border-radius: 20px 20px 0 0; padding: 15px; font-weight: bold;">
                                Latest Updates
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">Recent Case Highlights</h5>
                                <p class="card-text">Stay informed about the latest case developments and important legal updates handled by our office.</p>
                                <a href="#" class="btn btn-danger" style="background: #ff4b2b; border: none; transition: background 0.3s ease;">View Details</a>
                            </div>
                        </div>
                    </div>

                    <!-- Info Card 2 -->
                    <div class="col-md-4 mb-3">
                        <div class="card" style="border: 2px solid #ff4b2b; border-radius: 20px; box-shadow: 0 6px 14px rgba(0, 0, 0, 0.4); overflow: hidden; transition: transform 0.3s ease;">
                            <div class="card-header" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); color: white; border-radius: 20px 20px 0 0; padding: 15px; font-weight: bold;">
                                Office Initiatives
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">Upcoming Events</h5>
                                <p class="card-text">Learn about upcoming initiatives and events organized by the Office of the Attorney General.</p>
                                <a href="#" class="btn btn-danger" style="background: #ff4b2b; border: none; transition: background 0.3s ease;">Learn More</a>
                            </div>
                        </div>
                    </div>

                    <!-- Info Card 3 -->
                    <div class="col-md-4 mb-3">
                        <div class="card" style="border: 2px solid #ff4b2b; border-radius: 20px; box-shadow: 0 6px 14px rgba(0, 0, 0, 0.4); overflow: hidden; transition: transform 0.3s ease;">
                            <div class="card-header" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); color: white; border-radius: 20px 20px 0 0; padding: 15px; font-weight: bold;">
                                Contact Us
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">Get in Touch</h5>
                                <p class="card-text">For any inquiries or support, feel free to reach out to our office. We are here to assist you.</p>
                                <a href="#" class="btn btn-danger" style="background: #ff4b2b; border: none; transition: background 0.3s ease;">Contact Support</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Case Tab -->
        <div class="tab-pane fade" id="case" role="tabpanel" aria-labelledby="case-tab">
            <!-- Case content goes here -->
        </div>
        
        <!-- Accused Tab -->
        <div class="tab-pane fade" id="accused" role="tabpanel" aria-labelledby="accused-tab">
            <!-- Accused content goes here -->
        </div>

        <!-- Victim Tab -->
        <div class="tab-pane fade" id="victim" role="tabpanel" aria-labelledby="victim-tab">
            <!-- Victim content goes here -->
        </div>

        <!-- Incident Tab -->
        <div class="tab-pane fade" id="incident" role="tabpanel" aria-labelledby="incident-tab">
            <!-- Incident content goes here -->
        </div>
    </div>
</div>

@endsection
