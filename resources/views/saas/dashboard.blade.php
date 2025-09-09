<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Super Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Hospitals -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div class="ml-5">
                                <div class="text-2xl font-bold text-gray-900">{{ number_format($metrics['total_hospitals']) }}</div>
                                <div class="text-gray-500 text-sm">Total Hospitals</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paid Subscriptions -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-5">
                                <div class="text-2xl font-bold text-gray-900">{{ number_format($metrics['paid_subscriptions']) }}</div>
                                <div class="text-gray-500 text-sm">Paid Subscriptions</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Revenue -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <div class="ml-5">
                                <div class="text-2xl font-bold text-gray-900">${{ number_format($metrics['monthly_revenue'], 2) }}</div>
                                <div class="text-gray-500 text-sm">Monthly Revenue</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Users -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <div class="ml-5">
                                <div class="text-2xl font-bold text-gray-900">{{ number_format($metrics['total_users']) }}</div>
                                <div class="text-gray-500 text-sm">Total Users</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Metrics Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <div class="text-lg font-semibold text-gray-700">Active Hospitals</div>
                    <div class="text-3xl font-bold text-green-600">{{ $metrics['active_hospitals'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <div class="text-lg font-semibold text-gray-700">Trial Subscriptions</div>
                    <div class="text-3xl font-bold text-yellow-600">{{ $metrics['trial_subscriptions'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <div class="text-lg font-semibold text-gray-700">Total Enquiries</div>
                    <div class="text-3xl font-bold text-blue-600">{{ $metrics['total_enquiries'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <div class="text-lg font-semibold text-gray-700">Newsletter Subscribers</div>
                    <div class="text-3xl font-bold text-indigo-600">{{ $metrics['total_subscribers'] }}</div>
                </div>
            </div>

            <!-- Navigation Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <a href="{{ route('saas.hospitals.index') }}" class="bg-white hover:bg-gray-50 p-6 rounded-lg shadow-sm transition-colors">
                    <div class="flex items-center">
                        <svg class="h-8 w-8 text-green-500 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Hospitals</h3>
                            <p class="text-gray-600 text-sm">Manage registered hospitals and their details</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('saas.plans.index') }}" class="bg-white hover:bg-gray-50 p-6 rounded-lg shadow-sm transition-colors">
                    <div class="flex items-center">
                        <svg class="h-8 w-8 text-blue-500 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Subscription Plans</h3>
                            <p class="text-gray-600 text-sm">Manage pricing plans and features</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('saas.transactions.index') }}" class="bg-white hover:bg-gray-50 p-6 rounded-lg shadow-sm transition-colors">
                    <div class="flex items-center">
                        <svg class="h-8 w-8 text-purple-500 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Transactions</h3>
                            <p class="text-gray-600 text-sm">View payment logs and transactions</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('saas.hospital-types.index') }}" class="bg-white hover:bg-gray-50 p-6 rounded-lg shadow-sm transition-colors">
                    <div class="flex items-center">
                        <svg class="h-8 w-8 text-yellow-500 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Hospital Types</h3>
                            <p class="text-gray-600 text-sm">Manage hospital categories</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('saas.enquiries.index') }}" class="bg-white hover:bg-gray-50 p-6 rounded-lg shadow-sm transition-colors">
                    <div class="flex items-center">
                        <svg class="h-8 w-8 text-red-500 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Enquiries</h3>
                            <p class="text-gray-600 text-sm">Manage customer enquiries</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('saas.subscribers.index') }}" class="bg-white hover:bg-gray-50 p-6 rounded-lg shadow-sm transition-colors">
                    <div class="flex items-center">
                        <svg class="h-8 w-8 text-indigo-500 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 3.26a2 2 0 001.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Newsletter Subscribers</h3>
                            <p class="text-gray-600 text-sm">Manage email subscribers</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- CMS and Settings Links -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <a href="{{ route('saas.cms.index') }}" class="bg-white hover:bg-gray-50 p-6 rounded-lg shadow-sm transition-colors">
                    <div class="flex items-center">
                        <svg class="h-8 w-8 text-green-500 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Content Management</h3>
                            <p class="text-gray-600 text-sm">Manage About, Services, Pricing, FAQ, and landing text</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('saas.settings.index') }}" class="bg-white hover:bg-gray-50 p-6 rounded-lg shadow-sm transition-colors">
                    <div class="flex items-center">
                        <svg class="h-8 w-8 text-gray-500 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Settings</h3>
                            <p class="text-gray-600 text-sm">Configure branding, SMTP, SMS, payment, favicon, locales</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Recent Activities -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Recent Hospitals -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Hospitals</h3>
                        <div class="space-y-3">
                            @forelse($recentHospitals as $hospital)
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $hospital->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $hospital->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            @empty
                            <p class="text-sm text-gray-500">No recent hospitals</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Recent Users -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Users</h3>
                        <div class="space-y-3">
                            @forelse($recentUsers as $user)
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-xs font-medium text-blue-600">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                    <p class="text-xs text-gray-400">{{ $user->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            @empty
                            <p class="text-sm text-gray-500">No recent users</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Recent Enquiries -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Enquiries</h3>
                        <div class="space-y-3">
                            @forelse($recentEnquiries as $enquiry)
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $enquiry->subject }}</p>
                                    <p class="text-sm text-gray-500">{{ $enquiry->name }} - {{ $enquiry->email }}</p>
                                    <p class="text-xs text-gray-400">{{ $enquiry->created_at->diffForHumans() }}</p>
                                    @if($enquiry->status === 'unread')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Unread</span>
                                    @endif
                                </div>
                            </div>
                            @empty
                            <p class="text-sm text-gray-500">No recent enquiries</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>