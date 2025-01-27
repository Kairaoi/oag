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
        /* Custom Styles */
        .bg-crime-pattern {
            background-image: url('https://www.transparenttextures.com/patterns/black-linen.png');
        }
        .highlight {
            background: linear-gradient(90deg, rgba(255,165,0,0.8) 0%, rgba(255,0,0,0.8) 100%);
            color: white;
            padding: 0.5rem;
            border-radius: 0.375rem;
        }
        /* Additional Custom Styles for Enhanced Layout */
        .hero-overlay {
            background: rgba(0, 0, 0, 0.5);
        }
    </style>
</head>
<body class="bg-gray-100 font-roboto">

    <!-- Header -->
    <header class="bg-crime-pattern text-white">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <!-- Logo / Title -->
            <div>
                <a href="{{ url('/') }}" class="text-2xl font-bold">CrimeStats</a>
            </div>
            <!-- Navigation Links -->
            <nav>
                @if (Route::has('login'))
                    <div class="flex space-x-4">
                        @auth
                            <a href="{{ url('/home') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">Home</a>
                        @else
                            <a href="{{ route('login') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">Log in</a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-3 py-2 rounded-md text-sm font-medium bg-red-500 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">Register</a>
                            @endif
                        @endauth
                    </div>
                @endif
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative bg-cover bg-center h-96" style="background-image: url('https://images.unsplash.com/photo-1593642532973-d31b6557fa68?ixlib=rb-4.0.3&auto=format&fit=crop&w=1950&q=80');">
        <div class="absolute inset-0 hero-overlay flex flex-col justify-center items-center">
            <h2 class="text-4xl md:text-6xl font-bold text-white mb-4">Understanding Crime Trends</h2>
            <p class="text-lg md:text-2xl text-gray-200">Stay informed with the latest crime statistics and analyses.</p>
            <a href="#statistics" class="mt-6 px-6 py-3 bg-red-500 text-white rounded-full hover:bg-red-600 transition">View Statistics</a>
        </div>
    </section>

    <!-- Main Content -->
    <main class="container mx-auto px-6 py-8">

        <!-- Crime Statistics -->
        <section id="statistics" class="mb-16">
            <h2 class="text-3xl font-semibold text-center mb-8">Crime Statistics</h2>
            <p class="text-center text-gray-700 mb-12">Explore the latest data on various crime categories to understand the current landscape.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Statistic Card -->
                <div class="bg-white shadow-lg rounded-lg overflow-hidden hover:shadow-2xl transition duration-300">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">Total Crimes</h3>
                        <p class="text-3xl text-red-500">5,678</p>
                    </div>
                    <div class="bg-red-100 h-2"></div>
                </div>
                <!-- Statistic Card -->
                <div class="bg-white shadow-lg rounded-lg overflow-hidden hover:shadow-2xl transition duration-300">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">Violent Crimes</h3>
                        <p class="text-3xl text-red-500">1,234</p>
                    </div>
                    <div class="bg-red-100 h-2"></div>
                </div>
                <!-- Statistic Card -->
                <div class="bg-white shadow-lg rounded-lg overflow-hidden hover:shadow-2xl transition duration-300">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">Property Crimes</h3>
                        <p class="text-3xl text-red-500">4,444</p>
                    </div>
                    <div class="bg-red-100 h-2"></div>
                </div>
            </div>
        </section>

        <!-- Notable Cases -->
        <section class="mb-16">
            <h2 class="text-3xl font-semibold text-center mb-8">Notable Crime Cases</h2>
            <div class="space-y-6">
                <!-- Case Card -->
                <div class="bg-white shadow-md rounded-lg p-6 flex flex-col md:flex-row items-center">
                    <div class="md:w-1/3">
                        <img src="https://via.placeholder.com/150" alt="John Doe's Burglary" class="w-full h-32 object-cover rounded-md">
                    </div>
                    <div class="md:w-2/3 mt-4 md:mt-0 md:ml-6">
                        <h3 class="text-2xl font-semibold">John Doe's Burglary</h3>
                        <p class="mt-2 text-gray-600">A high-profile burglary that shocked the community.</p>
                    </div>
                </div>
                <!-- Case Card -->
                <div class="bg-white shadow-md rounded-lg p-6 flex flex-col md:flex-row items-center">
                    <div class="md:w-1/3">
                        <img src="https://via.placeholder.com/150" alt="Jane Smith's Abduction" class="w-full h-32 object-cover rounded-md">
                    </div>
                    <div class="md:w-2/3 mt-4 md:mt-0 md:ml-6">
                        <h3 class="text-2xl font-semibold">Jane Smith's Abduction</h3>
                        <p class="mt-2 text-gray-600">A missing person case that garnered national attention.</p>
                    </div>
                </div>
                <!-- Case Card -->
                <div class="bg-white shadow-md rounded-lg p-6 flex flex-col md:flex-row items-center">
                    <div class="md:w-1/3">
                        <img src="https://via.placeholder.com/150" alt="Bank Robbery at Central Bank" class="w-full h-32 object-cover rounded-md">
                    </div>
                    <div class="md:w-2/3 mt-4 md:mt-0 md:ml-6">
                        <h3 class="text-2xl font-semibold">Bank Robbery at Central Bank</h3>
                        <p class="mt-2 text-gray-600">A daring robbery involving multiple suspects.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action -->
        <section class="bg-red-500 text-white rounded-lg p-8 text-center">
            <h2 class="text-2xl md:text-4xl font-bold mb-4">Stay Informed</h2>
            <p class="mb-6">Subscribe to our newsletter to receive the latest crime statistics and updates directly to your inbox.</p>
            <form class="flex flex-col sm:flex-row justify-center items-center">
                <input type="email" placeholder="Enter your email" class="w-full sm:w-1/3 px-4 py-2 rounded-md text-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white" required>
                <button type="submit" class="mt-4 sm:mt-0 sm:ml-4 px-6 py-2 bg-white text-red-500 font-semibold rounded-md hover:bg-gray-100 transition">Subscribe</button>
            </form>
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-6">
        <div class="container mx-auto text-center">
            <p>&copy; 2024 CrimeStats. All rights reserved.</p>
            <div class="mt-4 flex justify-center space-x-4">
                <a href="#" class="hover:text-red-500">Privacy Policy</a>
                <a href="#" class="hover:text-red-500">Terms of Service</a>
                <a href="#" class="hover:text-red-500">Contact Us</a>
            </div>
        </div>
    </footer>

</body>
</html>
