<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Office of the Attorney General</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; }
        .font-serif-heading { font-family: Georgia, 'Times New Roman', serif; }
        .bg-navy { background-color: #0a2463; }
        .text-navy { color: #0a2463; }
        .border-navy { border-color: #0a2463; }
        .border-gold { border-color: #f5a623; }
        .text-accent { color: #4fd6e6; }
        .bg-accent { background-color: #17a2b8; }
        .bg-accent:hover { background-color: #128a9d; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    <!-- Utility Bar -->
    <div class="bg-navy text-blue-100 text-xs uppercase tracking-wide">
        <div class="container mx-auto px-6 py-1 flex justify-between">
            <span>Government of Kiribati &mdash; Office of the Attorney General</span>
            <span class="hidden md:inline text-accent font-semibold">Official Government Portal</span>
        </div>
    </div>

    <!-- Header -->
    <header class="bg-white border-b-4 border-gold">
        <div class="container mx-auto px-6 py-4 flex flex-wrap justify-between items-center gap-3">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/oag_logo.png') }}" alt="Coat of Arms of the Republic of Kiribati" class="h-12">
                <div>
                    <div class="font-serif-heading font-bold text-navy text-lg leading-tight">Case Management System</div>
                    <div class="text-xs uppercase tracking-wider text-gray-500">Office of the Attorney General</div>
                </div>
            </div>
            <!-- Navigation Links -->
            <nav>
                @if (Route::has('login'))
                    <div class="flex space-x-3">
                        @auth
                            <a href="{{ url('/home') }}" class="px-4 py-2 rounded text-sm font-semibold text-navy border border-navy hover:bg-navy hover:text-white transition">Go to Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="px-4 py-2 rounded text-sm font-semibold text-navy border border-navy hover:bg-navy hover:text-white transition">Log in</a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-4 py-2 rounded text-sm font-semibold text-white bg-accent hover:opacity-90 transition">Register</a>
                            @endif
                        @endauth
                    </div>
                @endif
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="bg-navy text-white">
        <div class="container mx-auto px-6 py-20 max-w-3xl">
            <span class="inline-block text-xs font-bold uppercase tracking-widest text-accent border border-blue-300 border-opacity-40 rounded px-3 py-1 mb-4">Official Case Management System</span>
            <h1 class="font-serif-heading text-4xl md:text-5xl font-bold mb-4">Criminal Case <span class="text-accent">Management Portal</span></h1>
            <p class="text-lg text-blue-100 mb-6">Register, review and track criminal cases through every stage &mdash; from initial complaint to court hearing and appeal &mdash; in one system used across the Office of the Attorney General.</p>
            <p class="text-xs uppercase tracking-widest text-blue-300 mb-8">Te Mauri &middot; Te Raoi &middot; Ao Te Tabomoa</p>
            @auth
                <a href="{{ url('/home') }}" class="inline-block px-6 py-3 bg-accent text-white rounded font-semibold hover:opacity-90 transition">Go to Dashboard &rarr;</a>
            @else
                <a href="{{ route('login') }}" class="inline-block px-6 py-3 bg-accent text-white rounded font-semibold hover:opacity-90 transition">Staff Login &rarr;</a>
            @endauth
        </div>
    </section>

    <!-- Main Content -->
    <main class="container mx-auto px-6 py-16 max-w-3xl">
        <h2 class="font-serif-heading text-2xl font-bold text-navy mb-4 text-center">About This System</h2>
        <p class="text-gray-600 text-center">
            This is the internal case management system for the Office of the Attorney General, Republic of Kiribati.
            It supports the registration, review and tracking of criminal, civil and appeal cases, court hearings and
            victim records, and is restricted to authorised staff.
        </p>
    </main>

    <!-- Footer -->
    <footer class="bg-navy text-blue-100 py-6">
        <div class="container mx-auto px-6 text-center text-sm">
            <p class="mb-1 font-semibold text-white">Office of the Attorney General</p>
            <p class="mb-3">Republic of Kiribati &middot; Bairiki, Tarawa</p>
            <p class="text-xs text-blue-300">&copy; {{ date('Y') }} Government of Kiribati &middot; Office of the Attorney General. Internal system for authorised personnel only.</p>
        </div>
    </footer>

</body>
</html>
