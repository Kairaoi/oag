{{-- resources/views/layouts/civil.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

    </style>

    @stack('styles')
</head>
<body class="bg-gray-100 font-roboto">

    <!-- Header -->
    <header class="bg-crime-pattern text-white">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div>
                <a href="{{ url('/') }}" class="text-2xl font-bold">CrimeStats</a>
            </div>
            <nav>
                @if (Route::has('login'))
                    <div class="flex space-x-4">
                        @auth
                            <a href="{{ url('/home') }}" class="hover:text-red-500">Home</a>
                        @else
                            <a href="{{ route('login') }}" class="hover:text-red-500">Login</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="bg-red-500 px-3 py-2 rounded-md hover:bg-red-600">Register</a>
                            @endif
                        @endauth
                    </div>
                @endif
            </nav>
        </div>
    </header>

    <!-- Hero Banner -->
    <section class="relative bg-cover bg-center h-72" style="background-image: url('https://images.unsplash.com/photo-1593642532973-d31b6557fa68?ixlib=rb-4.0.3&auto=format&fit=crop&w=1950&q=80');">
        <div class="absolute inset-0 hero-overlay flex flex-col justify-center items-center text-white">
            <h1 class="text-4xl font-bold">Office of the Attorney General</h1>
            <p class="text-lg mt-2">Civil Litigation Case Management</p>
        </div>
    </section>

    <!-- Main Content -->
    <main class="container mx-auto px-6 py-10">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-6 mt-12">
        <div class="container mx-auto text-center">
            <p>&copy; 2025 CrimeStats. All rights reserved.</p>
            <div class="mt-4 flex justify-center space-x-4">
                <a href="#" class="hover:text-red-500">Privacy</a>
                <a href="#" class="hover:text-red-500">Terms</a>
                <a href="#" class="hover:text-red-500">Contact</a>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
