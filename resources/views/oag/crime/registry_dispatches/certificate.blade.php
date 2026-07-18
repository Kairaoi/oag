@extends('layouts.app')

@section('content')
<div class="container mt-4 mb-5">
    <nav aria-label="breadcrumb">
        {{ Breadcrumbs::render() }}
    </nav>

    <div class="mb-3 no-print d-flex flex-wrap gap-2 align-items-center">
        <a href="{{ route('crime.registry-dispatches.show', $dispatch->id) }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-print"></i> Print
        </button>
        <button type="button" class="btn btn-outline-primary btn-sm" id="copy-share-link-btn" data-link="{{ $shareUrl }}">
            <i class="fas fa-link"></i> Copy Share Link (High Court, no login needed)
        </button>
        <span class="text-muted small">Link expires {{ $shareUrlExpiresAt->format('d M Y') }}</span>
    </div>

    @include('oag.crime.registry_dispatches._certificate_content')
</div>

<script>
    document.getElementById('copy-share-link-btn').addEventListener('click', function () {
        const link = this.dataset.link;
        navigator.clipboard.writeText(link).then(() => {
            const original = this.innerHTML;
            this.innerHTML = '<i class="fas fa-check"></i> Link copied';
            setTimeout(() => { this.innerHTML = original; }, 2000);
        }).catch(() => {
            prompt('Copy this link:', link);
        });
    });
</script>
@endsection
