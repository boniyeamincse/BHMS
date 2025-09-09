<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-semibold text-slate-900 leading-tight">
                Super Admin Dashboard
            </h2>
            <div class="flex items-center space-x-4">
                <!-- Date Range Filter -->
                <form method="GET" class="flex items-center space-x-2">
                    <label for="date_range" class="text-sm font-medium text-slate-600">Date Range:</label>
                    <select id="date_range" name="date_range" onchange="this.form.submit()"
                            class="rounded-md border-slate-200 shadow-sm focus:border-slate-500 focus:ring-slate-500 text-sm">
                        <option value="today" {{ $dateRange == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="7d" {{ $dateRange == '7d' ? 'selected' : '' }}>7d</option>
                        <option value="30d" {{ $dateRange == '30d' ? 'selected' : '' }}>30d</option>
                        <option value="YTD" {{ $dateRange == 'YTD' ? 'selected' : '' }}>YTD</option>
                    </select>
                </form>
                <!-- Plan Filter -->
                <form method="GET" class="flex items-center space-x-2">
                    <input type="hidden" name="date_range" value="{{ $dateRange }}">
                    <label for="plan_filter" class="text-sm font-medium text-slate-600">Plan:</label>
                    <select id="plan_filter" name="plan_filter" onchange="this.form.submit()"
                            class="rounded-md border-slate-200 shadow-sm focus:border-slate-500 focus:ring-slate-500 text-sm">
                        <option value="all" {{ $planFilter == 'all' ? 'selected' : '' }}>All</option>
                        <option value="paid" {{ $planFilter == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="trial" {{ $planFilter == 'trial' ? 'selected' : '' }}>Trial</option>
                        <option value="free" {{ $planFilter == 'free' ? 'selected' : '' }}>Free</option>
                    </select>
                </form>
                <!-- Search -->
                <form method="GET" class="flex items-center space-x-2">
                    <input type="hidden" name="date_range" value="{{ $dateRange }}">
                    <input type="hidden" name="plan_filter" value="{{ $planFilter }}">
                    <input id="search" name="search" type="text" placeholder="Search hospitals/transactions" value="{{ $search }}"
                           class="rounded-md border-slate-200 shadow-sm focus:border-slate-500 focus:ring-slate-500 text-sm">
                </form>
                <!-- Quick Actions -->
                <div class="flex items-center space-x-2">
                    <a href="#" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-2 rounded-lg text-sm font-medium">
                        + New Hospital
                    </a>
                    <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm font-medium">
                        Send Announcement
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Row 1: 4 KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Hospitals KPI -->
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-slate-900">{{ number_format($metrics['total_hospitals']) }}</div>
                            <div class="text-sm text-slate-600">Hospitals</div>
                            <div class="text-xs text-slate-500 mt-1">
                                <span>{{ $metrics['active_hospitals'] }} Active • {{ $metrics['inactive_hospitals'] }} Inactive</span>
                            </div>
                            <div class="flex items-center text-xs mt-1">
                                @if($metrics['total_hospitals_trend'] > 0)
                                    <span class="text-emerald-600 font-semibold">▲ +{{ number_format($metrics['total_hospitals_trend'], 1) }}% WoW</span>
                                @elseif($metrics['total_hospitals_trend'] < 0)
                                    <span class="text-rose-600 font-semibold">▼ {{ number_format(abs($metrics['total_hospitals_trend']), 1) }}% WoW</span>
                                @else
                                    <span class="text-slate-500">± 0.0% WoW</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <span class="sr-only">{{ number_format($metrics['total_hospitals']) }} total hospitals, {{ $metrics['active_hospitals'] }} active, {{ $metrics['inactive_hospitals'] }} inactive, trend {{ $metrics['total_hospitals_trend'] > 0 ? 'up' : ($metrics['total_hospitals_trend'] < 0 ? 'down' : 'no change') }} by {{ abs($metrics['total_hospitals_trend']) }} percent week over week</span>
                </div>

                <!-- Plans KPI -->
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-slate-900">{{ $metrics['paid_subscriptions'] }} • {{ $metrics['trial_subscriptions'] }} • {{ $metrics['free_subscriptions'] }}</div>
                            <div class="text-sm text-slate-600">Plans: Paid • Trial • Free</div>
                            <div class="text-xs text-slate-500 mt-1">
                                <span>{{ number_format($metrics['plan_mix_paid'], 1) }}% • {{ number_format($metrics['plan_mix_trial'], 1) }}% • {{ number_format($metrics['plan_mix_free'], 1) }}% mix</span>
                            </div>
                        </div>
                    </div>
                    <span class="sr-only">{{ $metrics['paid_subscriptions'] }} paid subscriptions, {{ $metrics['trial_subscriptions'] }} trial subscriptions, {{ $metrics['free_subscriptions'] }} free subscriptions, mix {{ number_format($metrics['plan_mix_paid'], 1) }} percent paid, {{ number_format($metrics['plan_mix_trial'], 1) }} percent trial, {{ number_format($metrics['plan_mix_free'], 1) }} percent free</span>
                </div>

                <!-- MRR / Revenue KPI -->
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-slate-900">${{ number_format($metrics['mtd_revenue'], 0) }} • ${{ number_format($metrics['ytd_revenue'], 0) }}</div>
                            <div class="text-sm text-slate-600">MRR / Revenue: MTD • YTD</div>
                            <div class="flex items-center text-xs mt-1">
                                @if($metrics['revenue_trend_wow'] > 0)
                                    <span class="text-emerald-600 font-semibold">▲ +{{ number_format($metrics['revenue_trend_wow'], 1) }}% WoW</span>
                                @elseif($metrics['revenue_trend_wow'] < 0)
                                    <span class="text-rose-600 font-semibold">▼ {{ number_format(abs($metrics['revenue_trend_wow']), 1) }}% WoW</span>
                                @else
                                    <span class="text-slate-500">± 0.0% WoW</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <span class="sr-only">Revenue ${number_format($metrics['mtd_revenue']) }} month to date, ${ number_format($metrics['ytd_revenue']) }} year to date, trend {{ $metrics['revenue_trend_wow'] > 0 ? 'up' : ($metrics['revenue_trend_wow'] < 0 ? 'down' : 'no change') }} by {{ abs($metrics['revenue_trend_wow']) }} percent week over week</span>
                </div>

                <!-- New KPI -->
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-slate-900">{{ $metrics['enquiries_mtd'] }} • {{ $metrics['subscribers_mtd'] }}</div>
                            <div class="text-sm text-slate-600">New: Enquiries • Subscribers</div>
                            <div class="flex items-center text-xs mt-1">
                                @if($metrics['new_trend_wow'] > 0)
                                    <span class="text-emerald-600 font-semibold">▲ +{{ number_format($metrics['new_trend_wow'], 1) }}% WoW</span>
                                @elseif($metrics['new_trend_wow'] < 0)
                                    <span class="text-rose-600 font-semibold">▼ {{ number_format(abs($metrics['new_trend_wow']), 1) }}% WoW</span>
                                @else
                                    <span class="text-slate-500">± 0.0% WoW</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <span class="sr-only">{{ $metrics['enquiries_mtd'] }} enquiries month to date, {{ $metrics['subscribers_mtd'] }} subscribers month to date, trend {{ $metrics['new_trend_wow'] > 0 ? 'up' : ($metrics['new_trend_wow'] < 0 ? 'down' : 'no change') }} by {{ abs($metrics['new_trend_wow']) }} percent week over week</span>
                </div>
            </div>

            <!-- System Usage Row -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-medium text-slate-600">Logins (24h)</div>
                        <div class="text-xs text-slate-500">{{ $metrics['system_usage_trend'] }}% trend</div>
                    </div>
                    <div class="text-3xl font-bold text-slate-900 mt-2">{{ $metrics['logins_24h'] }}</div>
                    <div class="h-8 mt-2">
                        <canvas id="loginsSparkline"></canvas>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-medium text-slate-600">API Calls (24h)</div>
                        <div class="text-xs text-slate-500">{{ $metrics['system_usage_trend'] }}% trend</div>
                    </div>
                    <div class="text-3xl font-bold text-slate-900 mt-2">{{ number_format($metrics['api_calls_24h']) }}</div>
                    <div class="h-8 mt-2">
                        <canvas id="apiSparkline"></canvas>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100 hover:shadow-md transition-shadow">
                    <div class="text-sm font-medium text-slate-600">Uptime</div>
                    <div class="text-3xl font-bold text-emerald-600 mt-2">{{ $metrics['uptime'] }}%</div>
                    <div class="text-xs text-slate-500 mt-1">Current uptime status</div>
                </div>
            </div>

            <!-- Row 2: 2 Main Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Revenue by Month Chart -->
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100">
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">Revenue by Month (Stripe & PayPal)</h3>
                    <div class="h-64">
                        <canvas id="revenueChart"></canvas>
                    </div>
                    <div class="sr-only" aria-live="polite" aria-atomic="true">Revenue line area chart showing monthly values for Stripe and PayPal</div>
                </div>

                <!-- Plan Breakdown Chart -->
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100">
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">Plan Breakdown</h3>
                    <div class="h-64">
                        <canvas id="planChart"></canvas>
                    </div>
                    <div class="sr-only" aria-live="polite" aria-atomic="true">Doughnut chart showing plan distribution by paid trial and free</div>
                </div>
            </div>

            <!-- Row 3: Additional Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Trials Funnel Chart -->
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100">
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">Trials Funnel</h3>
                    <div class="h-64">
                        <canvas id="trialsChart"></canvas>
                    </div>
                    <div class="sr-only" aria-live="polite" aria-atomic="true">Bar chart showing trials funnel from started to converted and expired</div>
                </div>

                <!-- System Health Chart -->
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100">
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">System Health</h3>
                    <div class="h-32">
                        <canvas id="systemHealthChart"></canvas>
                    </div>
                    <div class="mt-4 space-y-1">
                        <div class="flex items-center text-xs">
                            <span class="w-16">Requests/</span>
                            <div class="h-1 bg-blue-200 rounded flex-1">
                                <div class="h-1 bg-blue-600 rounded" style="width: 70%"></div>
                            </div>
                        </div>
                        <div class="flex items-center text-xs">
                            <span class="w-16">95th Lat.</span>
                            <div class="h-1 bg-green-200 rounded flex-1">
                                <div class="h-1 bg-green-600 rounded" style="width: 50%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="sr-only" aria-live="polite" aria-atomic="true">System health chart showing requests per minute and 95th latency</div>
                </div>
            </div>

            <!-- Row 3: 3 Tables -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Recent Transactions Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100">
                    <div class="p-6 border-b border-slate-100">
                        <h3 class="text-lg font-semibold text-slate-900">Recent Transactions</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-slate-600" role="table" aria-label="Recent transactions">
                            <thead class="text-xs text-slate-400 uppercase border-b border-slate-100">
                                <tr>
                                    <th class="px-4 py-2 text-left">Hospital</th>
                                    <th class="px-4 py-2 text-left">Method</th>
                                    <th class="px-4 py-2 text-left">Amount</th>
                                    <th class="px-4 py-2 text-left">Status</th>
                                    <th class="px-4 py-2 text-left">Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions as $transaction)
                                <tr class="border-b border-slate-50 hover:bg-slate-25 transition-colors">
                                    <td class="px-4 py-3">{{ $transaction->hospital->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3">{{ ucfirst($transaction->method) }}</td>
                                    <td class="px-4 py-3 font-semibold">${{ number_format($transaction->amount, 2) }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $transaction->status == 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                            {{ ucfirst($transaction->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">{{ $transaction->created_at->diffForHumans() }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                                        No recent transactions
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Latest Hospitals Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100">
                    <div class="p-6 border-b border-slate-100">
                        <h3 class="text-lg font-semibold text-slate-900">Latest Hospitals</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-slate-600" role="table" aria-label="Latest hospitals">
                            <thead class="text-xs text-slate-400 uppercase border-b border-slate-100">
                                <tr>
                                    <th class="px-4 py-2 text-left">Name</th>
                                    <th class="px-4 py-2 text-left">Plan</th>
                                    <th class="px-4 py-2 text-left">Status</th>
                                    <th class="px-4 py-2 text-left">Created</th>
                                    <th class="px-4 py-2 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestHospitals as $hospital)
                                <tr class="border-b border-slate-50 hover:bg-slate-25 transition-colors">
                                    <td class="px-4 py-3 font-semibold">{{ $hospital->name }}</td>
                                    <td class="px-4 py-3">{{ ucfirst($hospital->subscription_status) }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $hospital->status == 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-800' }}">
                                            {{ ucfirst($hospital->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">{{ $hospital->created_at->format('M d, Y') }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center space-x-2">
                                            <button class="text-emerald-600 hover:text-emerald-800 text-sm font-medium">
                                                Impersonate
                                            </button>
                                            @if($hospital->status == 'active')
                                            <button class="text-rose-600 hover:text-rose-800 text-sm font-medium">
                                                Suspend
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                                        No hospitals found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- New Enquiries Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100">
                    <div class="p-6 border-b border-slate-100">
                        <h3 class="text-lg font-semibold text-slate-900">New Enquiries</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-slate-600" role="table" aria-label="New enquiries">
                            <thead class="text-xs text-slate-400 uppercase border-b border-slate-100">
                                <tr>
                                    <th class="px-4 py-2 text-left">Hospital</th>
                                    <th class="px-4 py-2 text-left">Contact</th>
                                    <th class="px-4 py-2 text-left">Topic</th>
                                    <th class="px-4 py-2 text-left">Age</th>
                                    <th class="px-4 py-2 text-left">Assign</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($newEnquiries as $enquiry)
                                <tr class="border-b border-slate-50 hover:bg-slate-25 transition-colors">
                                    <td class="px-4 py-3">{{ $enquiry->subject }}</td>
                                    <td class="px-4 py-3">{{ $enquiry->name }}<br><span class="text-slate-400">{{ $enquiry->email }}</span></td>
                                    <td class="px-4 py-3">{{ $enquiry->topic }}</td>
                                    <td class="px-4 py-3">{{ $enquiry->created_at->diffForHumans() }}</td>
                                    <td class="px-4 py-3">
                                        @if($enquiry->status == 'unassigned')
                                        <button class="bg-emerald-600 hover:bg-emerald-700 text-white px-2 py-1 rounded text-xs font-medium">
                                            Assign to Me
                                        </button>
                                        @else
                                        <span class="text-slate-400">Assigned</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                                        No new enquiries
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Script -->
    <script defer src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer>
        document.addEventListener('DOMContentLoaded', function() {
            // Revenue Chart
            var ctxRevenue = document.getElementById('revenueChart');
            if (ctxRevenue) {
                var revenueChart = new Chart(ctxRevenue, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode(collect($revenueChart)->pluck('month')) !!},
                        datasets: [{
                            label: 'Revenue',
                            data: {!! json_encode(collect($revenueChart)->pluck('revenue')) !!},
                            borderColor: '#059669',
                            backgroundColor: 'rgba(5, 150, 105, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { callback: function(value) { return '$' + value.toLocaleString(); } }
                            }
                        },
                        elements: { point: { radius: 0 } }
                    }
                });
            }

            // Plan Breakdown Chart
            var ctxPlan = document.getElementById('planChart');
            if (ctxPlan) {
                var planChart = new Chart(ctxPlan, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode(collect($planBreakdown)->pluck('name')) !!},
                        datasets: [{
                            data: {!! json_encode(collect($planBreakdown)->pluck('value')) !!},
                            backgroundColor: {!! json_encode(collect($planBreakdown)->pluck('color')) !!},
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { usePointStyle: true, padding: 20 }
                            }
                        }
                    }
                });
            }

            // Trials Funnel Bar Chart
            var ctxTrials = document.getElementById('trialsChart');
            if (ctxTrials) {
                var trialsChart = new Chart(ctxTrials, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode(collect($trialsFunnel)->pluck('stage')) !!},
                        datasets: [{
                            label: 'Count',
                            data: {!! json_encode(collect($trialsFunnel)->pluck('count')) !!},
                            backgroundColor: ['#059669', '#d97706', '#dc2626'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            }

            // System Health Chart (sparkline)
            var ctxSystem = document.getElementById('systemHealthChart');
            if (ctxSystem) {
                var systemData = {!! json_encode($systemHealth) !!};
                var requestsData = systemData.map(d => d.requests_per_min);
                var latencyData = systemData.map(d => d.latency_95);
                var labels = systemData.map(d => d.hour);

                var systemHealthChart = new Chart(ctxSystem, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Requests/min',
                                data: requestsData,
                                borderColor: '#059669',
                                borderWidth: 1,
                                fill: false,
                                pointRadius: 0
                            },
                            {
                                label: '95th Latency (ms)',
                                data: latencyData,
                                borderColor: '#3b82f6',
                                borderWidth: 1,
                                fill: false,
                                pointRadius: 0
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' }
                        },
                        scales: {
                            y: { beginAtZero: true, display: false },
                            x: { display: false }
                        },
                        elements: { line: { tension: 0 } }
                    }
                });
            }

            // Sparklines for System Usage
            var createSparkline = function(canvasId, data) {
                var ctx = document.getElementById(canvasId);
                if (!ctx) return;
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: Array.from({length: data.length}, (_, i) => i),
                        datasets: [{
                            data: data,
                            borderColor: '#059669',
                            borderWidth: 1,
                            fill: false,
                            pointRadius: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: { legend: { display: false } },
                        scales: { x: { display: false }, y: { display: false } },
                        elements: { line: { tension: 0 } }
                    }
                });
            };

            // Generate simulated sparklines
            createSparkline('loginsSparkline', Array.from({length: 24}, () => Math.floor(Math.random() * 100) + 10));
            createSparkline('apiSparkline', Array.from({length: 24}, () => Math.floor(Math.random() * 500) + 50));
        });
    </script>
</x-app-layout>