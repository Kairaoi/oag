@extends('layouts.app')

@section('content')

<div class="container mt-5">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb" style="background: none; box-shadow: none;">
            <li class="breadcrumb-item"><a href="#" style="color: #ff4b2b; font-weight: bold;">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page" style="color: #333; font-weight: bold;">Legal Advice Board</li>
        </ol>
    </nav>

    <!-- Main Title -->
    <h1 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #333; text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.3); border-bottom: 4px solid #ff4b2b; padding-bottom: 12px; transition: transform 0.3s ease;">
        Legal Advice Board
    </h1>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs nav-justified" id="myTab" role="tablist" style="border-bottom: none;">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" 
               id="legal-advice-tasks-tab" 
               href="{{ route('legal.legal_tasks.index') }}" 
               role="tab" 
               aria-controls="legal-advice-tasks" 
               aria-selected="true" 
               style="background: linear-gradient(135deg, #4caf50, #388e3c); color: white; border-radius: 25px; font-weight: bold; text-transform: uppercase; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4); transition: background 0.3s ease, transform 0.3s ease;">
                Legal Advice Tasks
            </a>
        </li>
    </ul>

    <!-- Tab content -->
    <div class="tab-content mt-4" id="myTabContent">
        <!-- Legal Advice Tab -->
        <div class="tab-pane fade show active" id="legal-advice-tasks" role="tabpanel" aria-labelledby="legal-advice-tasks-tab">
            <div class="mt-3">
                <p class="lead" style="font-family: 'Arial', sans-serif; font-size: 18px; color: #555;">
                    Welcome to the Legal Advice Board managed by the Office of the Attorney General. This platform provides expert legal advice, resources, and tools for managing various legal matters. Explore the tasks and stay informed about the latest updates in legal advisory services.
                </p>

                <div class="row">
                    <!-- Info Card -->
                    <div class="col-md-4 mb-3">
                        <div class="card" style="border: 2px solid #4caf50; border-radius: 20px; box-shadow: 0 6px 14px rgba(0, 0, 0, 0.4); overflow: hidden; transition: transform 0.3s ease;">
                            <div class="card-header" style="background: linear-gradient(90deg, #4caf50, #388e3c); color: white; border-radius: 20px 20px 0 0; padding: 15px; font-weight: bold;">
                                Task Overview
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">Manage Legal Tasks</h5>
                                <p class="card-text">Access tools and resources to effectively manage legal advisory tasks and track progress.</p>
                                <a href="{{ route('legal.legal_tasks.index') }}" class="btn btn-success" style="background: #388e3c; border: none; transition: background 0.3s ease;">Go to Tasks</a>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Info Cards -->
                    <!-- You can add more cards here if necessary -->

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
