<style>
    header.site-header {
        background: #fff;
        border-bottom: 3px solid #f5a623;
        padding: 14px 0;
    }
    header.site-header .header-crest {
        max-height: 52px;
    }
    header.site-header .header-title {
        font-family: Georgia, 'Times New Roman', serif;
        font-size: 19px;
        font-weight: 700;
        color: #0a2463;
        line-height: 1.2;
    }
    header.site-header .header-subtitle {
        font-size: 11.5px;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: #777;
    }
    header.site-header .header-breadcrumb {
        font-size: 13.5px;
        color: #555;
    }
</style>

<header class="site-header">
    <div class="container d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div class="d-flex align-items-center gap-3">
            <img src="{{ asset('images/oag_logo.png') }}" alt="Coat of Arms of the Republic of Kiribati" class="header-crest">
            <div>
                <div class="header-title">Case Management System</div>
                <div class="header-subtitle">Office of the Attorney General</div>
            </div>
        </div>
        <div class="header-breadcrumb d-none d-md-block">
            Office of the Attorney General &middot; Criminal Justice Division &middot; South Tarawa
        </div>
    </div>
</header>
