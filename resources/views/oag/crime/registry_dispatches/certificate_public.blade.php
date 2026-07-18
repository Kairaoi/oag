@extends('layouts.public')

@section('content')
<div class="container mt-4 mb-5">
    <div class="mb-3 no-print text-center">
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-print"></i> Print
        </button>
    </div>

    @include('oag.crime.registry_dispatches._certificate_content')
</div>
@endsection
