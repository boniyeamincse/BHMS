<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if(isset($sections['landing_text']) && $sections['landing_text'])
        <title>{{ $sections['landing_text']->seo_title ?? $sections['landing_text']->title }} | {{ config('app.name', 'Laravel') }}</title>
        <meta name="description" content="{{ $sections['landing_text']->seo_description ?? 'Hospital Management System' }}">
        <meta name="keywords" content="{{ $sections['landing_text']->seo_keywords ?? 'hospital management, healthcare' }}">
    @else
        <title>{{ config('app.name', 'BHMS') }} - Hospital Management System</title>
        <meta name="description" content="Professional hospital management software">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
    </style>
</head>
<body class="antialiased">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold text-gray-800">{{ config('app.name', 'BHMS') }}</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 font-medium">Login</a>
                    <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">Get Started</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="py-20 px-4">
        <div class="max-w-4xl mx-auto text-center text-white">
            @if(isset($sections['landing_text']) && $sections['landing_text'])
                <h1 class="text-4xl font-bold mb-6">{{ $sections['landing_text']->title }}</h1>
                <div class="text-xl mb-8 leading-relaxed">{!! $sections['landing_text']->content !!}</div>
            @else
                <h1 class="text-4xl font-bold mb-6">Welcome to {{ config('app.name', 'BHMS') }}</h1>
                <p class="text-xl mb-8 leading-relaxed">Professional Hospital Management System</p>
            @endif
            <div class="space-x-4">
                <a href="{{ route('register') }}" class="bg-white text-purple-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">Get Started</a>
                <a href="#about" class="border border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-purple-600 transition duration-300">Learn More</a>
            </div>
        </div>
    </section>

    <!-- About Section -->
    @if(isset($sections['about']) && $sections['about'])
    <section id="about" class="py-16 px-4 bg-white">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800">{{ $sections['about']->title }}</h2>
            </div>
            <div class="prose prose-lg mx-auto text-gray-600">
                {!! $sections['about']->content !!}
            </div>
        </div>
    </section>
    @endif

    <!-- Services Section -->
    @if(isset($sections['services']) && $sections['services'])
    <section id="services" class="py-16 px-4 bg-gray-50">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800">{{ $sections['services']->title }}</h2>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Hospital Management</h3>
                    <p class="text-gray-600">Complete hospital administration and management system</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Patient Care</h3>
                    <p class="text-gray-600">Comprehensive patient management and care tracking</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Analytics & Reports</h3>
                    <p class="text-gray-600">Detailed analytics and comprehensive reporting tools</p>
                </div>
            </div>
            <div class="prose prose-lg mx-auto text-gray-600 mt-8">
                {!! $sections['services']->content !!}
            </div>
        </div>
    </section>
    @endif

    <!-- Pricing Section -->
    @if(isset($sections['pricing']) && $sections['pricing'])
    <section id="pricing" class="py-16 px-4 bg-white">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800">{{ $sections['pricing']->title }}</h2>
            </div>
            <div class="prose prose-lg mx-auto text-gray-600">
                {!! $sections['pricing']->content !!}
            </div>
        </div>
    </section>
    @endif

    <!-- FAQ Section -->
    @if(isset($sections['faq']) && $sections['faq'])
    <section id="faq" class="py-16 px-4 bg-gray-50">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800">{{ $sections['faq']->title }}</h2>
            </div>
            <div class="prose prose-lg mx-auto text-gray-600">
                {!! $sections['faq']->content !!}
            </div>
        </div>
    </section>
    @endif

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12 px-4">
        <div class="max-w-6xl mx-auto text-center">
            <h3 class="text-xl font-bold mb-4">{{ config('app.name', 'BHMS') }}</h3>
            <p class="mb-4">Professional Hospital Management System</p>
            <p class="text-gray-400 text-sm">&copy; {{ date('Y') }} {{ config('app.name', 'BHMS') }}. All rights reserved.</p>
            <div class="mt-4">
                <p class="text-gray-400 text-sm">Powered by Laravel Framework</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</body>
</html>