<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password - {{ config('app.name', 'BHMS') }}</title>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8V7z"/>
                    </svg>
                </div>
            </div>

            <!-- Form Title -->
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Forgot Password?</h2>
                <p class="text-gray-600 text-sm">No worries! Enter your email and we'll send you a reset link.</p>
            </div>

            <!-- Form Card -->
            <div class="bg-white shadow-xl border border-gray-200 rounded-2xl px-8 py-10">
                @if (session('status'))
                    <div class="mb-6 bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-sm text-green-700">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="font-medium">Success</span>
                        </div>
                        <p class="mt-1">{{ session('status') }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                    @csrf

                    <!-- Email Input -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 placeholder-gray-400 text-gray-900 text-sm bg-white shadow-sm @error('email') border-red-300 @enderror"
                               placeholder="Enter your email address">

                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Send Reset Button -->
                    <div>
                        <button type="submit"
                                class="w-full bg-gradient-to-r from-blue-600 to-green-600 hover:from-blue-700 hover:to-green-700 text-white font-semibold py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 text-sm shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <span class="flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Send Reset Link
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Back to Login -->
            <div class="text-center mt-8">
                <a href="{{ route('login') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-blue-600 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Sign In
                </a>
            </div>
        </div>
    </div>
</body>
</html>
