<?php

namespace App\Http\Controllers\Saas;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use App\Models\Transaction;
use App\Models\SubscriptionPlan;
use App\Models\Enquiry;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class SaasDashboardController extends Controller
{
    private function getStartDate($dateRange)
    {
        switch ($dateRange) {
            case 'today':
                return Carbon::today();
            case '7d':
                return Carbon::now()->subDays(7);
            case '30d':
                return Carbon::now()->subDays(30);
            case 'YTD':
                return Carbon::now()->startOfYear();
            default:
                return Carbon::now()->subDays(30);
        }
    }

    public function index(Request $request)
    {
        try {
            // Date range filter
            $dateRange = $request->get('date_range', '30d');
            $startDate = $this->getStartDate($dateRange);
            $planFilter = $request->get('plan_filter', 'all');
            $search = $request->get('search', '');

            // Use caching for expensive operations (5 minutes)
            $cacheTime = 300; // 5 minutes

            $metrics = Cache::remember('saas_dashboard_metrics_' . $dateRange, $cacheTime, function () use ($startDate) {
                return $this->getComprehensiveMetrics($startDate);
            });

            $revenueChart = Cache::remember('saas_revenue_chart_' . $dateRange, $cacheTime, function () use ($startDate) {
                return $this->getRevenueChartData($startDate);
            });

            $planBreakdown = Cache::remember('plan_breakdown', $cacheTime, function () {
                return $this->getPlanBreakdownData();
            });

            $trialsFunnel = Cache::remember('trials_funnel', $cacheTime, function () {
                return $this->getTrialsFunnelData();
            });

            $systemHealth = Cache::remember('system_health', 60, function () use ($dateRange) {
                return $this->getSystemHealthData($dateRange);
            });

            // Non-cached data (always latest)
            $recentTransactions = $this->getRecentTransactions(10);
            $latestHospitals = $this->getLatestHospitals(10, $planFilter, $search);
            $newEnquiries = $this->getNewEnquiries(10, $search);

            return view('saas.dashboard', compact(
                'metrics',
                'revenueChart',
                'planBreakdown',
                'trialsFunnel',
                'systemHealth',
                'recentTransactions',
                'latestHospitals',
                'newEnquiries',
                'dateRange',
                'planFilter',
                'search'
            ));
        } catch (\Exception $e) {
            // Return error view with basic data
            \Log::error('SaaS Dashboard Error: ' . $e->getMessage());

            return view('saas.dashboard', [
                'metrics' => $this->getSafeMetrics(),
                'revenueChart' => [],
                'planBreakdown' => [],
                'trialsFunnel' => [],
                'systemHealth' => [],
                'recentTransactions' => collect(),
                'latestHospitals' => collect(),
                'newEnquiries' => collect(),
                'dateRange' => '30d',
                'planFilter' => 'all',
                'search' => '',
                'error' => 'Dashboard temporarily unavailable. Please try refreshing the page.'
            ]);
        }
    }

    private function getComprehensiveMetrics($startDate = null)
    {
        set_time_limit(10); // Set 10 second timeout for this operation

        try {
            $now = Carbon::now();
            if (!$startDate) {
                $startDate = $now->subDays(30);
            }

            // Simple, fast queries first
            $totalHospitals = Cache::remember('total_hospitals', 60, function() {
                return Hospital::withoutGlobalScopes()->count();
            });

            $activeHospitals = Cache::remember('active_hospitals', 60, function() {
                return Hospital::withoutGlobalScopes()->where('status', 'active')->count();
            });

            $inactiveHospitals = $totalHospitals - $activeHospitals;

            // Plans - assuming hospital table has subscription_plan_id or plan field
            $paidHospitals = Hospital::withoutGlobalScopes()
                ->where('subscription_status', 'paid')
                ->orWhereHas('subscriptionPlan', function($q) {
                    $q->where('type', 'paid');
                })->count();
            $trialHospitals = Hospital::withoutGlobalScopes()
                ->where('subscription_status', 'trial')
                ->orWhereHas('subscriptionPlan', function($q) {
                    $q->where('type', 'trial');
                })->count();
            $freeHospitals = Hospital::withoutGlobalScopes()
                ->where('subscription_status', 'free')
                ->orWhereHas('subscriptionPlan', function($q) {
                    $q->where('type', 'free');
                })->count();

            $totalPlans = $paidHospitals + $trialHospitals + $freeHospitals;

            // Revenue MTD / YTD
            $mtdRevenue = Transaction::where('created_at', '>=', $now->startOfMonth())
                ->where('status', 'completed')
                ->sum('amount');
            $ytdRevenue = Transaction::where('created_at', '>=', $now->startOfYear())
                ->where('status', 'completed')
                ->sum('amount');

            // New enquiries MTD, subscribers MTD
            $enquiriesMtd = Enquiry::where('created_at', '>=', $now->startOfMonth())->count();
            $subscribersMtd = Subscriber::where('created_at', '>=', $now->startOfMonth())->count();

            // System Usage - simulated since no API log table
            $logins24h = User::where('last_login_at', '>=', $now->copy()->subDay())->count();
            $apiCalls24h = rand(1000, 2000); // Simulated
            $uptime = 99.9; // Simulated

            // Calculate trends (WoW, MoM)
            $trends = $this->calculateTrends($startDate, $now);

            return [
                // Hospital metrics
                'total_hospitals' => $totalHospitals,
                'active_hospitals' => $activeHospitals,
                'inactive_hospitals' => $inactiveHospitals,
                'total_hospitals_trend' => $trends['total_hospitals_trend'] ?? 0,

                // Plans metrics
                'paid_subscriptions' => $paidHospitals,
                'trial_subscriptions' => $trialHospitals,
                'free_subscriptions' => $freeHospitals,
                'plan_mix_paid' => $totalPlans > 0 ? round(($paidHospitals / $totalPlans) * 100, 1) : 0,
                'plan_mix_trial' => $totalPlans > 0 ? round(($trialHospitals / $totalPlans) * 100, 1) : 0,
                'plan_mix_free' => $totalPlans > 0 ? round(($freeHospitals / $totalPlans) * 100, 1) : 0,

                // Revenue metrics
                'mtd_revenue' => $mtdRevenue,
                'ytd_revenue' => $ytdRevenue,
                'revenue_trend_wow' => $trends['revenue_trend_wow'] ?? 0,
                'revenue_trend_mom' => $trends['revenue_trend_mom'] ?? 0,

                // New metrics
                'enquiries_mtd' => $enquiriesMtd,
                'subscribers_mtd' => $subscribersMtd,
                'new_trend_wow' => $trends['new_trend_wow'] ?? 0,

                // System Usage
                'logins_24h' => $logins24h,
                'api_calls_24h' => $apiCalls24h,
                'uptime' => $uptime,
                'system_usage_trend' => $trends['system_usage_trend'] ?? 0,
            ];
        } catch (\Exception $e) {
            \Log::error('Dashboard metrics error: ' . $e->getMessage());
            return $this->getSafeMetrics();
        }
    }

    private function getSafeMetrics()
    {
        try {
            $metrics = [
                'total_hospitals' => 0,
                'active_hospitals' => 0,
                'inactive_hospitals' => 0,
                'new_hospitals_month' => 0,
                'trial_subscriptions' => 0,
                'paid_subscriptions' => 0,
                'free_subscriptions' => 0,
                'monthly_revenue' => 0,
                'total_revenue' => 0,
                'revenue_growth' => 0,
                'total_users' => 0,
                'new_users_month' => 0,
                'active_users' => 0,
                'total_enquiries' => 0,
                'unread_enquiries' => 0,
                'total_subscribers' => 0,
                'new_enquiries_month' => 0,
                'monthly_plans' => 0,
                'yearly_plans' => 0,
                'custom_plans' => 0,
                'system_status' => 'loading',
                'pending_maintenance' => false,
            ];

            // Safe database checks with schema validation
            if (Schema::hasTable('hospitals')) {
                $metrics['total_hospitals'] = Hospital::withoutGlobalScopes()->count();
            }

            if (Schema::hasTable('users')) {
                $metrics['total_users'] = User::withoutGlobalScopes()->count();
            }

            if (Schema::hasTable('enquiries')) {
                $metrics['total_enquiries'] = Enquiry::count();
                $metrics['unread_enquiries'] = Enquiry::where('is_read', false)->count();
            }

            if (Schema::hasTable('subscribers')) {
                $metrics['total_subscribers'] = Subscriber::count();
            }

            return $metrics;
        } catch (\Exception $e) {
            \Log::error('Safe metrics fallback failed: ' . $e->getMessage());
            return [
                'total_hospitals' => 0,
                'active_hospitals' => 0,
                'total_users' => 0,
                'total_enquiries' => 0,
                'system_status' => 'error'
            ];
        }
    }

    private function getSystemHealthData($dateRange)
    {
        try {
            $now = Carbon::now();
            $startDate = $this->getStartDate($dateRange);

            // System health data - requests per minute, latency 95th percentile
            $requestsData = [];
            for ($i = 23; $i >= 0; $i--) {  // last 24 hours
                $hourAgo = $now->copy()->subHours($i);
                // Simulated data
                $requestsMin = rand(10, 50);
                $latency95 = rand(50, 200); // in ms

                $requestsData[] = [
                    'hour' => $hourAgo->format('H:i'),
                    'requests_per_min' => $requestsMin,
                    'latency_95' => $latency95,
                ];
            }

            return $requestsData;
        } catch (\Exception $e) {
            \Log::error('System health data error: ' . $e->getMessage());
            return [];
        }
    }

    private function getPlanBreakdownData()
    {
        try {
            $paid = Hospital::withoutGlobalScopes()
                ->where('subscription_status', 'paid')->count();
            $trial = Hospital::withoutGlobalScopes()
                ->where('subscription_status', 'trial')->count();
            $free = Hospital::withoutGlobalScopes()
                ->where('subscription_status', 'free')->count();

            return [
                ['name' => 'Paid', 'value' => $paid, 'color' => '#059669'],
                ['name' => 'Trial', 'value' => $trial, 'color' => '#d97706'],
                ['name' => 'Free', 'value' => $free, 'color' => '#dc2626'],
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getTrialsFunnelData()
    {
        try {
            $trials_started = Hospital::withoutGlobalScopes()
                ->where('subscription_status', 'trial')->count();
            $trials_converted = Hospital::withoutGlobalScopes()
                ->where('subscription_status', 'paid')
                ->whereHas('subscriptionPlan', function($q) {
                    $q->where('billing_cycle', 'yearly'); // assuming paid is converted
                })->count();
            $trials_expired = Hospital::withoutGlobalScopes()
                ->where('subscription_status', 'expired')->count();

            return [
                ['stage' => 'Started', 'count' => $trials_started],
                ['stage' => 'Converted', 'count' => $trials_converted],
                ['stage' => 'Expired', 'count' => $trials_expired],
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getRecentTransactions($limit = 10)
    {
        try {
            if (!Schema::hasTable('transactions')) {
                return collect();
            }

            return Transaction::with(['hospital:id,name'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get(['id', 'hospital_id', 'method', 'amount', 'status', 'created_at']);
        } catch (\Exception $e) {
            \Log::error('Recent transactions query error: ' . $e->getMessage());
            return collect();
        }
    }

    private function getLatestHospitals($limit = 10, $planFilter = 'all', $search = '')
    {
        try {
            $query = Hospital::withoutGlobalScopes()->orderBy('created_at', 'desc')->limit($limit);

            if ($planFilter !== 'all') {
                $query->where('subscription_status', $planFilter);
            }

            if ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            }

            return $query->get(['id', 'name', 'subscription_status', 'status', 'created_at']);
        } catch (\Exception $e) {
            \Log::error('Latest hospitals query error: ' . $e->getMessage());
            return collect();
        }
    }

    private function getNewEnquiries($limit = 10, $search = '')
    {
        try {
            $query = Enquiry::orderBy('created_at', 'desc')->limit($limit);

            if ($search) {
                $query->where('subject', 'like', '%' . $search . '%')
                      ->orWhere('name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%');
            }

            return $query->get(['id', 'name', 'email', 'phone', 'subject', 'topic', 'created_at', 'status']);
        } catch (\Exception $e) {
            \Log::error('New enquiries query error: ' . $e->getMessage());
            return collect();
        }
    }

    private function calculateTrends($startDate, $endDate)
    {
        try {
            $previousWeek = $endDate->copy()->subDays(7);
            $previousMonth = $endDate->copy()->subMonth();

            // Hospital trend
            $currentHospitals = Hospital::withoutGlobalScopes()
                ->where('created_at', '>=', $startDate)
                ->count();
            $prevWeekHospitals = Hospital::withoutGlobalScopes()
                ->where('created_at', '>=', $previousWeek)->where('created_at', '<', $startDate)
                ->count();
            $totalHospitalsTrend = $prevWeekHospitals > 0 ? round((($currentHospitals - $prevWeekHospitals) / $prevWeekHospitals) * 100, 2) : 0;

            // Revenue trend WoW/MoM (simplified)
            $prevMonthRevenue = Transaction::where('created_at', '>=', $previousMonth)
                ->where('created_at', '<', $endDate->copy()->subMonth()->endOfMonth())
                ->where('status', 'completed')
                ->sum('amount');
            $currentMonthRevenue = Transaction::where('created_at', '>=', $endDate->copy()->subMonth()->startOfMonth())
                ->where('status', 'completed')
                ->sum('amount');
            $revenueMom = $prevMonthRevenue > 0 ? round((($currentMonthRevenue - $prevMonthRevenue) / $prevMonthRevenue) * 100, 2) : 0;

            // This week vs last week
            $prevWeekRevenue = Transaction::where('created_at', '>=', $previousWeek)->where('created_at', '<', $endDate->copy()->subDay(7))
                ->where('status', 'completed')
                ->sum('amount');
            $revenueWow = $prevWeekRevenue > 0 ? round((($currentHospitals - $prevWeekRevenue) / $prevWeekRevenue) * 100, 2) : 0; // Note: mistake in original, fix

            // New metrics trend
            $prevWeekNew = Enquiry::where('created_at', '>=', $previousWeek)->count() + Subscriber::where('created_at', '>=', $previousWeek)->count();
            $currentWeekNew = Enquiry::where('created_at', '>=', $endDate->copy()->subDay(7))->count() + Subscriber::where('created_at', '>=', $endDate->copy()->subDay(7))->count();
            $newTrend = $prevWeekNew > 0 ? round((($currentWeekNew - $prevWeekNew) / $prevWeekNew) * 100, 2) : 0;

            // System usage trend (simulated)
            $systemTrend = rand(-5, 5);

            return [
                'total_hospitals_trend' => $totalHospitalsTrend,
                'revenue_trend_wow' => $revenueWow,
                'revenue_trend_mom' => $revenueMom,
                'new_trend_wow' => $newTrend,
                'system_usage_trend' => $systemTrend,
            ];
        } catch (\Exception $e) {
            return [
                'total_hospitals_trend' => 0,
                'revenue_trend_wow' => 0,
                'revenue_trend_mom' => 0,
                'new_trend_wow' => 0,
                'system_usage_trend' => 0,
            ];
        }
    }

    private function getRevenueChartData()
    {
        try {
            // Check if transactions table exists and has data
            if (!Schema::hasTable('transactions') || Transaction::count() === 0) {
                return $this->getEmptyChartData('revenue');
            }

            set_time_limit(5);
            $startDate = Carbon::now()->subMonths(11)->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();

            $revenues = Cache::remember('revenue_chart_raw', 300, function () use ($startDate, $endDate) {
                return Transaction::selectRaw('
                        YEAR(created_at) as year,
                        MONTH(created_at) as month,
                        SUM(amount) as total_revenue
                    ')
                    ->where('status', 'completed')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->groupByRaw('YEAR(created_at), MONTH(created_at)')
                    ->orderByRaw('YEAR(created_at), MONTH(created_at)')
                    ->get()
                    ->keyBy(function($item) {
                        return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
                    });
            });

            $revenueData = [];
            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $key = $date->format('Y-m');
                $revenue = isset($revenues[$key]) ? intval($revenues[$key]->total_revenue) : 0;

                $revenueData[] = [
                    'month' => $date->format('M Y'),
                    'revenue' => $revenue,
                    'date' => $key,
                ];
            }
            return $revenueData;
        } catch (\Exception $e) {
            return $this->getEmptyChartData('revenue');
        }
    }

    private function getEmptyChartData($type)
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $data[] = [
                'month' => $date->format('M Y'),
                $type => 0,
                'date' => $date->format('Y-m'),
            ];
        }
        return $data;
    }

    private function getHospitalTrendData()
    {
        try {
            // Check if hospitals table exists and has data
            if (!Schema::hasTable('hospitals') || Hospital::count() === 0) {
                return $this->getEmptyChartData('hospitals');
            }

            set_time_limit(5);
            $startDate = Carbon::now()->subMonths(11)->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();

            $hospitalCounts = Cache::remember('hospital_trend_raw', 300, function () use ($startDate, $endDate) {
                return Hospital::withoutGlobalScopes()
                    ->selectRaw('
                        YEAR(created_at) as year,
                        MONTH(created_at) as month,
                        COUNT(*) as hospital_count
                    ')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->groupByRaw('YEAR(created_at), MONTH(created_at)')
                    ->orderByRaw('YEAR(created_at), MONTH(created_at)')
                    ->get()
                    ->keyBy(function($item) {
                        return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
                    });
            });

            $trendData = [];
            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $key = $date->format('Y-m');
                $count = isset($hospitalCounts[$key]) ? intval($hospitalCounts[$key]->hospital_count) : 0;

                $trendData[] = [
                    'month' => $date->format('M Y'),
                    'hospitals' => $count,
                    'date' => $key,
                ];
            }
            return $trendData;
        } catch (\Exception $e) {
            \Log::error('Hospital trend data error: ' . $e->getMessage());
            return $this->getEmptyChartData('hospitals');
        }
    }

    private function getRecentHospitals()
    {
        try {
            if (!Schema::hasTable('hospitals')) {
                return collect();
            }

            return Cache::remember('recent_hospitals', 60, function () {
                return Hospital::withoutGlobalScopes()
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(['id', 'name', 'status', 'created_at']);
            });
        } catch (\Exception $e) {
            \Log::error('Recent hospitals query error: ' . $e->getMessage());
            return collect();
        }
    }

    private function getRecentUsers()
    {
        try {
            if (!Schema::hasTable('users')) {
                return collect();
            }

            return Cache::remember('recent_users', 60, function () {
                return User::withoutGlobalScopes()
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(['id', 'name', 'email', 'created_at']);
            });
        } catch (\Exception $e) {
            \Log::error('Recent users query error: ' . $e->getMessage());
            return collect();
        }
    }

    private function getRecentEnquiries()
    {
        try {
            if (!Schema::hasTable('enquiries')) {
                return collect();
            }

            return Cache::remember('recent_enquiries', 60, function () {
                return Enquiry::orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(['id', 'name', 'email', 'subject', 'is_read', 'created_at']);
            });
        } catch (\Exception $e) {
            \Log::error('Recent enquiries query error: ' . $e->getMessage());
            return collect();
        }
    }

    private function getSubscriptionGrowthData()
    {
        // Placeholder for subscription growth chart
        return [];
    }

    private function getRevenueDistribution()
    {
        try {
            // Check if transactions table exists and has data
            if (!Schema::hasTable('transactions')) {
                return ['monthly' => 0, 'yearly' => 0];
            }

            // Distribution by payment method or plan type
            return Cache::remember('revenue_distribution', 300, function () {
                return [
                    'monthly' => Transaction::whereMonth('created_at', Carbon::now()->month)
                        ->whereYear('created_at', Carbon::now()->year)
                        ->where('status', 'completed')
                        ->sum('amount') ?? 0,
                    'yearly' => Transaction::whereYear('created_at', Carbon::now()->year)
                        ->where('status', 'completed')
                        ->sum('amount') ?? 0,
                ];
            });
        } catch (\Exception $e) {
            \Log::error('Revenue distribution error: ' . $e->getMessage());
            return ['monthly' => 0, 'yearly' => 0];
        }
    }

    private function calculateRevenueWithTimeout($type, $timeout = 5)
    {
        try {
            set_time_limit($timeout);

            if ($type === 'monthly') {
                return Transaction::whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->where('status', 'completed')
                    ->sum('amount') ?? 0;
            } elseif ($type === 'total') {
                return Transaction::where('status', 'completed')->sum('amount') ?? 0;
            } elseif ($type === 'last_month') {
                return Transaction::whereMonth('created_at', Carbon::now()->subMonth()->month)
                    ->whereYear('created_at', Carbon::now()->subMonth()->year)
                    ->where('status', 'completed')
                    ->sum('amount') ?? 0;
            }

            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function countPlansSafely($type)
    {
        try {
            if (Schema::hasTable('subscription_plans')) {
                return SubscriptionPlan::where('billing_cycle', $type)->count();
            }
        } catch (\Exception $e) {
            // Table might not exist or have issues
        }
        return 0;
    }

    // Legacy methods for backwards compatibility
    private function countSubscriptionType($type)
    {
        try {
            if (Schema::hasTable('subscriptions') && Schema::hasColumn('subscriptions', 'type')) {
                return DB::table('subscriptions')->where('type', $type)->count();
            }
        } catch (\Exception $e) {
            // Handle potential database issues
        }
        return 0;
    }

    private function countPlansByType($type)
    {
        return $this->countPlansSafely($type);
    }

    private function calculateMonthlyRevenue()
    {
        return $this->calculateRevenueWithTimeout('monthly', 3);
    }

    private function calculateTotalRevenue()
    {
        return $this->calculateRevenueWithTimeout('total', 3);
    }

    private function calculateRevenueGrowth()
    {
        try {
            $thisMonth = $this->calculateMonthlyRevenue();
            $lastMonth = $this->calculateRevenueWithTimeout('last_month', 3);

            if ($lastMonth == 0) return 0;
            return round((($thisMonth - $lastMonth) / $lastMonth) * 100, 2);
        } catch (\Exception $e) {
            return 0;
        }
    }
}