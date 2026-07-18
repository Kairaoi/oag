@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <nav aria-label="breadcrumb">
        {{ Breadcrumbs::render() }}
    </nav>

    <h2 class="text-center mb-4" style="font-family: 'Courier New', Courier, monospace; color: #f8f9fa; text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.4); border-bottom: 4px double #ff6f61; padding-bottom: 20px;">
        Registry Dispatch Details
    </h2>

    <div class="card shadow-lg rounded-3">
        <div class="card-body">
            <div class="mb-3">
                <strong>Case Name:</strong> {{ $dispatch->case->case_name ?? 'N/A' }}
            </div>
            <div class="mb-3">
                <strong>Date Dispatched:</strong> {{ \Carbon\Carbon::parse($dispatch->date_dispatched)->format('d M Y') }}
            </div>
            <div class="mb-3">
                <strong>Dispatched To:</strong> {{ $dispatch->dispatched_to }}
            </div>
            <div class="mb-3">
                <strong>Dispatched By:</strong> {{ $dispatch->dispatchedBy->name ?? 'N/A' }}
            </div>
        </div>

        <div class="card-footer text-center">
            <a href="{{ route('crime.registry-dispatches.index') }}" class="btn btn-primary">Back to Registry Dispatches</a>
            <a href="{{ route('crime.registry-dispatches.certificate', $dispatch->id) }}" class="btn btn-outline-dark">
                <i class="fas fa-file-alt"></i> View / Print Certificate
            </a>
        </div>
    </div>
</div>
@endsection
