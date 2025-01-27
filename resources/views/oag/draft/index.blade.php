@extends('layouts.app')

@section('content')

<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb" style="background: none; box-shadow: none;">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color: #ff4b2b; font-weight: bold;">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page" style="color: #333; font-weight: bold;">Legislation Board</li>
        </ol>
    </nav>

    <!-- Main Title -->
    <h1 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #333; text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.3); border-bottom: 4px solid #ff4b2b; padding-bottom: 12px; transition: transform 0.3s ease;">
        Legislation Board
    </h1>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs nav-justified" id="myTab" role="tablist" style="border-bottom: none;">
    @foreach ([
    ['name' => 'Home', 'route' => route('home'), 'active' => true, 'background' => 'linear-gradient(135deg, #ff416c, #ff4b2b)', 'color' => 'white'],
    ['name' => 'Bill Drafting', 'route' => route('draft.bills.index'), 'background' => 'linear-gradient(135deg, #4caf50, #388e3c)', 'color' => 'white'],
    ['name' => 'Regulation Drafting', 'route' => route('draft.regulations.index'), 'background' => 'linear-gradient(135deg, #2196f3, #1976d2)', 'color' => 'white'],
    ['name' => 'Bill Tracking', 'route' => route('draft.bills.index'), 'background' => 'linear-gradient(135deg, #ffeb3b, #fbc02d)', 'color' => 'black'],
    ['name' => 'Regulation Tracking', 'route' => route('draft.regulations.index'), 'background' => 'linear-gradient(135deg, #ff5722, #e64a19)', 'color' => 'white']
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
        
    </ul>

    <!-- Tab content -->
    <div class="tab-content mt-4" id="myTabContent">
        <!-- Home Tab -->
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            <div class="mt-3">
                <p class="lead" style="font-family: 'Arial', sans-serif; font-size: 18px; color: #555;">
                    Welcome to the Legislation Board managed by the Office of the Attorney General. This platform provides comprehensive information about the drafting, tracking, and legal framework of bills and regulations. Explore various aspects of the legislative process and stay updated on the latest developments.
                </p>

                <div class="row">
                    <!-- Info Card 1 -->
                    <div class="col-md-4 mb-3">
                        <div class="card" style="border: 2px solid #ff4b2b; border-radius: 20px; box-shadow: 0 6px 14px rgba(0, 0, 0, 0.4); overflow: hidden; transition: transform 0.3s ease;">
                            <div class="card-header" style="background: linear-gradient(90deg, #ff416c, #ff4b2b); color: white; border-radius: 20px 20px 0 0; padding: 15px; font-weight: bold;">
                                Latest Drafts
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">Recent Bill and Regulation Drafts</h5>
                                <p class="card-text">Stay informed about the latest bill and regulation drafts created by our office, including new proposals and modifications.</p>
                                <a href="#" class="btn btn-danger" style="background: #ff4b2b; border: none; transition: background 0.3s ease;">View Drafts</a>
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
                                <h5 class="card-title">Upcoming Legislative Events</h5>
                                <p class="card-text">Learn about upcoming initiatives and events related to legislation, including workshops and public consultations.</p>
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
                                <p class="card-text">For any inquiries or support regarding legislative processes, feel free to reach out to our office. We are here to assist you.</p>
                                <a href="#" class="btn btn-danger" style="background: #ff4b2b; border: none; transition: background 0.3s ease;">Contact Support</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bill Drafting Tab -->
        <div class="tab-pane fade" id="bill" role="tabpanel" aria-labelledby="bill-tab">
            <!-- Bill Drafting content goes here -->
        </div>

        <!-- Regulation Drafting Tab -->
        <div class="tab-pane fade" id="regulation" role="tabpanel" aria-labelledby="regulation-tab">
            <!-- Regulation Drafting content goes here -->
        </div>

        <!-- Bill Tracking Tab -->
        <div class="tab-pane fade" id="billTracking" role="tabpanel" aria-labelledby="billTracking-tab">
            <!-- Bill Tracking content goes here -->
        </div>

        <!-- Regulation Tracking Tab -->
        <div class="tab-pane fade" id="regulationTracking" role="tabpanel" aria-labelledby="regulationTracking-tab">
            <!-- Regulation Tracking content goes here -->
        </div>
    </div>
</div>

@endsection
