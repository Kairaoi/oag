@extends('layouts.app')

@section('content')
<div class="container mt-5">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent p-0">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}" class="text-danger fw-bold">Home</a>
            </li>
            <li class="breadcrumb-item active fw-bold" aria-current="page">Crime Board</li>
        </ol>
    </nav>

    {{-- Page Title --}}
    <h1 class="text-center mb-5 border-bottom pb-3" style="font-family: 'Courier New'; color: #333;">
        Crime Board
    </h1>

    {{-- Tabs --}}
    @php
        $tabs = [
            ['name' => 'Home', 'route' => '#home', 'type' => 'internal', 'background' => '#ff416c,#ff4b2b', 'color' => 'white', 'active' => true],
            ['name' => 'Case', 'route' => route('crime.criminalCase.index'), 'background' => '#4caf50,#388e3c', 'color' => 'white'],
            ['name' => 'Accused', 'route' => route('crime.accused.index'), 'background' => '#2196f3,#1976d2', 'color' => 'white'],
            ['name' => 'Victim', 'route' => route('crime.victim.index'), 'background' => '#ffeb3b,#fbc02d', 'color' => 'black'],
            ['name' => 'Incident', 'route' => route('crime.incident.index'), 'background' => '#ff5722,#e64a19', 'color' => 'white'],
        ];
    @endphp

    <ul class="nav nav-tabs nav-justified mb-4">
        @foreach ($tabs as $tab)
            <li class="nav-item">
                <a href="{{ $tab['route'] }}"
                   class="nav-link {{ $tab['active'] ?? false ? 'active' : '' }}"
                   style="background: linear-gradient(135deg, {{ $tab['background'] }}); color: {{ $tab['color'] }}; border-radius: 25px; font-weight: bold;">
                    {{ $tab['name'] }}
                </a>
            </li>
        @endforeach

        {{-- Reference Dropdown --}}
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-white" data-bs-toggle="dropdown" style="background: radial-gradient(circle at top left, #ff416c, #ff4b2b); border-radius: 25px;">
                Reference Tables
            </a>
            <ul class="dropdown-menu">
                @foreach ([
                    'Offence' => route('crime.offence.index'),
                    'Reason' => route('crime.reason.index'),
                    'Category' => route('crime.category.index'),
                    'Island' => route('crime.island.index'),
                ] as $name => $link)
                    <li><a class="dropdown-item fw-bold" href="{{ $link }}">{{ $name }}</a></li>
                @endforeach
            </ul>
        </li>
    </ul>

    {{-- Tab Content --}}
    <div class="tab-content">

        {{-- Home --}}
        <div class="tab-pane fade show active" id="home">
            <p class="lead text-muted mb-4">
                Welcome to the Crime Board managed by the Office of the Attorney General. This platform provides
                information on criminal cases, offences, individuals involved, and more.
            </p>

            {{-- Info Cards --}}
            <div class="row g-3 mb-4">
                @foreach ([
                    ['title' => 'Latest Updates', 'text' => 'Stay informed about the latest case developments.', 'btn' => 'View Details'],
                    ['title' => 'Office Initiatives', 'text' => 'Learn about upcoming events by the Attorney General.', 'btn' => 'Learn More'],
                    ['title' => 'Contact Us', 'text' => 'Reach out to our office for inquiries or support.', 'btn' => 'Contact Support'],
                ] as $info)
                    <div class="col-md-4">
                        <div class="card border-danger h-100 shadow-sm" style="border-radius: 20px;">
                            <div class="card-header bg-danger text-white fw-bold" style="border-top-left-radius: 20px; border-top-right-radius: 20px;">
                                {{ $info['title'] }}
                            </div>
                            <div class="card-body">
                                <p>{{ $info['text'] }}</p>
                                <a href="#" class="btn btn-danger">{{ $info['btn'] }}</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Summary Stats --}}
            <div class="row g-3">
                @foreach ([
                    ['label' => 'Pending Cases', 'count' => $pendingCount, 'bg' => 'warning'],
                    ['label' => 'Allocated Cases', 'count' => $allocatedCount, 'bg' => 'success'],
                    ['label' => 'Rejected Cases', 'count' => $rejectedCount, 'bg' => 'danger'],
                    ['label' => 'Total Lawyers', 'count' => $lawyerCount, 'bg' => 'primary'],
                    ['label' => 'Accepted Cases', 'count' => $acceptedCount, 'bg' => 'primary'],
                ] as $stat)
                    <div class="col-md-3">
                        <div class="card text-white bg-{{ $stat['bg'] }} h-100 shadow-sm">
                            <div class="card-body text-center">
                                <h5>{{ $stat['label'] }}</h5>
                                <p class="display-6">{{ $stat['count'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Other Tabs Placeholders --}}
        <div class="tab-pane fade" id="case"></div>
        <div class="tab-pane fade" id="accused"></div>
        <div class="tab-pane fade" id="victim"></div>
        <div class="tab-pane fade" id="incident"></div>
    </div>
</div>
@endsection
