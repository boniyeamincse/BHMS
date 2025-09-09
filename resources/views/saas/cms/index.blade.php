<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Content Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
            @if(session('success'))
            <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4">
                {{ session('success') }}
            </div>
            @endif

            <!-- Current Locale Info -->
            <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                    </svg>
                    <div>
                        <p class="text-sm">Managing content for locale: <strong>{{ config('app.locale', 'en') }}</strong></p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- About Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">About Page</h3>
                                <p class="text-sm text-gray-600 mt-1">Information about your company</p>
                            </div>
                            <a href="{{ route('saas.cms.edit', 'about') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="-ml-0.5 mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </a>
                        </div>
                        @if($sections['about'])
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900">{{ $sections['about']->title }}</h4>
                            <p class="text-sm text-gray-600 mt-2">{{ Str::limit(strip_tags($sections['about']->content), 150) }}</p>
                            <div class="mt-3 text-xs text-gray-500">
                                Status: <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $sections['about']->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($sections['about']->status) }}
                                </span>
                                <span class="ml-2">Updated: {{ $sections['about']->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        @else
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-500 italic">No content created yet</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Services Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Services Page</h3>
                                <p class="text-sm text-gray-600 mt-1">What services you offer</p>
                            </div>
                            <a href="{{ route('saas.cms.edit', 'services') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="-ml-0.5 mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </a>
                        </div>
                        @if($sections['services'])
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900">{{ $sections['services']->title }}</h4>
                            <p class="text-sm text-gray-600 mt-2">{{ Str::limit(strip_tags($sections['services']->content), 150) }}</p>
                            <div class="mt-3 text-xs text-gray-500">
                                Status: <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $sections['services']->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($sections['services']->status) }}
                                </span>
                                <span class="ml-2">Updated: {{ $sections['services']->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        @else
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-500 italic">No content created yet</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Pricing Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Pricing Page</h3>
                                <p class="text-sm text-gray-600 mt-1">Pricing information and plans</p>
                            </div>
                            <a href="{{ route('saas.cms.edit', 'pricing') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="-ml-0.5 mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </a>
                        </div>
                        @if($sections['pricing'])
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900">{{ $sections['pricing']->title }}</h4>
                            <p class="text-sm text-gray-600 mt-2">{{ Str::limit(strip_tags($sections['pricing']->content), 150) }}</p>
                            <div class="mt-3 text-xs text-gray-500">
                                Status: <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $sections['pricing']->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($sections['pricing']->status) }}
                                </span>
                                <span class="ml-2">Updated: {{ $sections['pricing']->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        @else
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-500 italic">No content created yet</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- FAQ Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">FAQ Page</h3>
                                <p class="text-sm text-gray-600 mt-1">Frequently asked questions</p>
                            </div>
                            <a href="{{ route('saas.cms.edit', 'faq') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="-ml-0.5 mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </a>
                        </div>
                        @if($sections['faq'])
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900">{{ $sections['faq']->title }}</h4>
                            <p class="text-sm text-gray-600 mt-2">{{ Str::limit(strip_tags($sections['faq']->content), 150) }}</p>
                            <div class="mt-3 text-xs text-gray-500">
                                Status: <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $sections['faq']->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($sections['faq']->status) }}
                                </span>
                                <span class="ml-2">Updated: {{ $sections['faq']->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        @else
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-500 italic">No content created yet</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Landing Text Section -->
            <div class="mt-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Landing Text</h3>
                                <p class="text-sm text-gray-600 mt-1">Hero section text and call-to-action</p>
                            </div>
                            <a href="{{ route('saas.cms.edit', 'landing-text') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="-ml-0.5 mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </a>
                        </div>
                        @if($sections['landing_text'])
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900">{{ $sections['landing_text']->title }}</h4>
                            <p class="text-sm text-gray-600 mt-2">{{ Str::limit(strip_tags($sections['landing_text']->content), 200) }}</p>
                            <div class="mt-3 text-xs text-gray-500">
                                Status: <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $sections['landing_text']->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($sections['landing_text']->status) }}
                                </span>
                                <span class="ml-2">Updated: {{ $sections['landing_text']->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        @else
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-500 italic">No content created yet</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>