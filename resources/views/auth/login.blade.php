<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In - {{ config('app.name', 'BHMS') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <!-- Medical background pattern -->
        <div class="absolute inset-0 opacity-[0.02]">
            <div class="h-full w-full bg-[radial-gradient(circle_at_1px_1px,rgba(59,130,246,0.3)_1px,transparent_0)] bg-[size:30px_30px]"></div>
        </div>

        <div class="max-w-md w-full space-y-8 relative z-10">
            <!-- Medical Logo/Icon -->
            <div class="text-center">
                <div class="mx-auto w-16 h-16 bg-gradient-to-r from-blue-600 to-green-600 rounded-full flex items-center justify-center mb-8 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>

            <!-- Form Title -->
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Welcome Back</h2>
                <p class="text-gray-600 text-sm">Sign in to access your BHMS dashboard</p>
            </div>

            <!-- Form Card -->
            <div class="bg-white shadow-xl border border-gray-200 rounded-2xl px-8 py-10">
                @if($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-700">
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="font-medium">Authentication Error</span>
                        </div>
                        <ul class="list-disc list-inside text-red-600 text-xs">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('resent'))
                    <div class="mb-6 bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-sm text-green-700">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="font-medium">Success</span>
                        </div>
                        <p class="mt-1 text-xs">Verification email sent successfully.</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email Input -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 placeholder-gray-400 text-gray-900 text-sm bg-white shadow-sm"
                               placeholder="Enter your email">
                    </div>

                    <!-- Password Input -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-sm font-semibold text-gray-700">Password</label>
                            <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-700 transition-colors duration-200 font-medium">Forgot password?</a>
                        </div>
                        <input type="password" id="password" name="password" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 placeholder-gray-400 text-gray-900 text-sm bg-white shadow-sm"
                               placeholder="Enter your password">
                    </div>

                    <!-- Remember Me Checkbox -->
                    <div class="flex items-center">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Keep me signed in</span>
                        </label>
                    </div>

                    <!-- Login Button -->
                    <div>
                        <button type="submit"
                                class="w-full bg-gradient-to-r from-blue-600 to-green-600 hover:from-blue-700 hover:to-green-700 text-white font-semibold py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 text-sm shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <span class="flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                                Sign In to BHMS
                            </span>
                        </button>
                    </div>
                </form>

                <!-- Sign Up Link -->
                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-600">
                        New to BHMS?
                        <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-700 font-semibold ml-1 transition-colors duration-200">Create your account</a>
                    </p>
                </div>
            </div>

            <!-- Back to Home -->
            <div class="text-center mt-8">
                <a href="{{ url('/') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-blue-600 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to BHMS Home
                </a>
            </div>
        </div>
    </div>
</body>
</html>