<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
        body {
            display: flex;
            flex-direction: column;
        }
        #app {
            flex: 1;
        }
        .navbar-utility {
            background: #0a2463 !important;
            font-size: 12.5px;
            padding-top: 6px;
            padding-bottom: 6px;
        }
        .navbar-utility .navbar-brand {
            font-size: 12.5px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #b9c4e0 !important;
        }
        .navbar-utility .badge-official {
            color: #7fe3ee;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-size: 11.5px;
        }
        .public-footer-note {
            background-color: #f8f9fa;
            padding: 16px;
            text-align: center;
            border-top: 1px solid #e0e0e0;
            font-size: 12.5px;
            color: #6c757d;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-dark navbar-utility">
            <div class="container">
                <span class="navbar-brand mb-0">
                    Government of Kiribati &mdash; Office of the Attorney General
                </span>
                <span class="badge-official d-none d-sm-inline">Official Government Portal</span>
            </div>
        </nav>

        <main class="py-4 flex-fill">
            @yield('content')
        </main>
    </div>

    <div class="public-footer-note no-print">
        This is a shared view of a single official document. It does not require, and does not grant, access to the Office of the Attorney General's case management system.
    </div>

    @stack('scripts')
</body>
</html>
