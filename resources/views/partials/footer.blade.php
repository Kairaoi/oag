<!-- resources/views/partials/footer.blade.php -->
<style>
    .site-footer {
        background: #0a2463;
        color: #c3ccdf;
    }
    .site-footer h5 {
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-bottom: 0.9rem;
    }
    .site-footer a {
        color: #c3ccdf;
        text-decoration: none;
    }
    .site-footer a:hover {
        color: #7fe3ee;
    }
    .site-footer .footer-bottom {
        border-top: 1px solid rgba(255, 255, 255, 0.12);
        color: #8b98b8;
        font-size: 12.5px;
    }
</style>

<footer class="site-footer py-4">
    <div class="container">
        <div class="row gy-4">
            <div class="col-md-4">
                <h5>Office of the Attorney General</h5>
                <p class="mb-1">Republic of Kiribati</p>
                <p class="mb-0">Bairiki, Tarawa</p>
            </div>

            <div class="col-md-4">
                <h5>Case Management</h5>
                <p class="mb-1"><a href="{{ route('crime.criminalCase.index') }}">Criminal Case List</a></p>
                <p class="mb-1"><a href="{{ route('crime.CaseReview.index') }}">Case Reviews</a></p>
                <p class="mb-1"><a href="{{ route('crime.court-hearings.index') }}">Court Hearings</a></p>
                <p class="mb-1"><a href="{{ route('crime.ag-reviews.index') }}">AG Reviews</a></p>
                <p class="mb-0"><a href="{{ route('crime.registry-dispatches.index') }}">Registry Dispatches</a></p>
            </div>

            <div class="col-md-4">
                <h5>Other Divisions</h5>
                <p class="mb-1"><a href="{{ route('civil.boards.index') }}">Civil Case</a></p>
                <p class="mb-1"><a href="{{ route('legal.boards.index') }}">Legal Advice</a></p>
                <p class="mb-0"><a href="{{ route('draft.boards.index') }}">Drafting</a></p>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col text-center footer-bottom pt-3">
                <p class="mb-0">&copy; {{ date('Y') }} Government of Kiribati &middot; Office of the Attorney General. Internal system for authorised personnel only.</p>
            </div>
        </div>
    </div>
</footer>
