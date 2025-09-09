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
use Carbon\Carbon;

class SaasDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Comprehensive metrics for Super Admin dashboard
        $metrics = $this->getComprehensiveMetrics();

        // Chart data for revenue and hospital trends
        $revenueChart = $this->getRevenueChartData();
        $hospitalTrend = $this->getHospitalTrendData();

        // Recent activity data
        $recentHospitals = $this->getRecentHospitals();
        $recentUsers = $this->getRecentUsers();
        $recentEnquiries = $this->getRecentEnquiries();

        // Additional dashboard components
        $subscriptionGrowth = $this->getSubscriptionGrowthData();
        $revenueDistribution = $this->getRevenueDistribution();

        return view('saas.dashboard', compact(
            'metrics',
            'revenueChart',
            'hospitalTrend',
            'recentHospitals',
            'recentUsers',
            'recentEnquiries',
            'subscriptionGrowth',
            'revenueDistribution'
        ));
    }

    private function getComprehensiveMetrics()
    {
        try {
            $currentMonth = Carbon::now()->format('Y-m');
            $lastMonth = Carbon::now()->subMonth()->format('Y-m');

            return [
                // Hospital metrics
                'total_hospitals' => Hospital::withoutGlobalScopes()->count(),
                'active_hospitals' => Hospital::withoutGlobalScopes()->where('status', 'active')->count(),
                'inactive_hospitals' => Hospital::withoutGlobalScopes()->where('status', 'inactive')->count(),
                'new_hospitals_month' => Hospital::withoutGlobalScopes()
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->count(),

                // Subscription metrics (if subscription tables exist)
                'trial_subscriptions' => $this->countSubscriptionType('trial'),
                'paid_subscriptions' => $this->countSubscriptionType('paid'),
                'free_subscriptions' => $this->countSubscriptionType('free'),

                // Revenue metrics
                'monthly_revenue' => $this->calculateMonthlyRevenue(),
                'total_revenue' => $this->calculateTotalRevenue(),
                'revenue_growth' => $this->calculateRevenueGrowth(),

                // User metrics
                'total_users' => User::withoutGlobalScopes()->count(),
                'new_users_month' => User::withoutGlobalScopes()
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->count(),
                'active_users' => User::withoutGlobalScopes()
                    ->where('last_login_at', '>', Carbon::now()->subMonth())
                    ->count(),

                // Communication metrics
                'total_enquiries' => Enquiry::count(),
                'unread_enquiries' => Enquiry::where('is_read', false)->count(),
                'total_subscribers' => Subscriber::count(),
                'new_enquiries_month' => Enquiry::whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->count(),

                // Plan metrics (if table exists)
                'monthly_plans' => $this->countPlansByType('monthly'),
                'yearly_plans' => $this->countPlansByType('yearly'),
                'custom_plans' => $this->countPlansByType('custom'),

                // System health
                'system_status' => 'healthy',
                'pending_maintenance' => false,
            ];
        } catch (\Exception $e) {
            // Return safe defaults with error information
            return [
                'total_hospitals' => 'Error loading data',
                'active_hospitals' => 0,
                'inactive_hospitals' => 0,
                'trial_subscriptions' => 0,
                'paid_subscriptions' => 0,
                'free_subscriptions' => 0,
                'monthly_revenue' => 0,
                'total_revenue' => 0,
                'total_users' => 0,
                'new_users' => 0,
                'total_enquiries' => 0,
                'unread_enquiries' => 0,
                'total_subscribers' => 0,
                'monthly_plans' => 0,
                'yearly_plans' => 0,
                'error_message' => $e->getMessage(),
            ];
        }
    }

    private function getRevenueChartData()
    {
        try {
            // Get last 12 months revenue data for chart
            $revenueData = [];
            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $monthStart = $date->startOfMonth();
                $monthEnd = $date->endOfMonth();

                $revenue = Transaction::whereBetween('created_at', [$monthStart, $monthEnd])
                    ->where('status', 'completed')
                    ->sum('amount') ?? 0;

                $revenueData[] = [
                    'month' => $date->format('M Y'),
                    'revenue' => $revenue,
                    'date' => $date->format('Y-m'),
                ];
            }
            return $revenueData;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getHospitalTrendData()
    {
        try {
            // Get hospital growth trend for last 12 months
            $trendData = [];
            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $monthStart = $date->startOfMonth();
                $monthEnd = $date->endOfMonth();

                $count = Hospital::withoutGlobalScopes()
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->count();

                $trendData[] = [
                    'month' => $date->format('M Y'),
                    'hospitals' => $count,
                ];
            }
            return $trendData;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getRecentHospitals()
    {
        try {
            return Hospital::withoutGlobalScopes()
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(['id', 'name', 'status', 'created_at']);
        } catch (\Exception $e) {
            return collect();
        }
    }

    private function getRecentUsers()
    {
        try {
            return User::withoutGlobalScopes()
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(['id', 'name', 'email', 'created_at']);
        } catch (\Exception $e) {
            return collect();
        }
    }

    private function getRecentEnquiries()
    {
        try {
            return Enquiry::orderBy('created_at', 'desc')
                ->limit(5)
                ->get(['id', 'name', 'email', 'subject', 'is_read', 'created_at']);
        } catch (\Exception $e) {
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
            // Distribution by payment method or plan type
            return [
                'monthly' => Transaction::whereMonth('created_at', Carbon::now()->month)->sum('amount') ?? 0,
                'yearly' => Transaction::whereYear('created_at', Carbon::now()->year)->sum('amount') ?? 0,
            ];
        } catch (\Exception $e) {
            return ['monthly' => 0, 'yearly' => 0];
        }
    }

    private function countSubscriptionType($type)
    {
        try {
            // Assuming subscription table exists with 'type' field
            return DB::table('subscriptions')->where('type', $type)->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function countPlansByType($type)
    {
        try {
            return SubscriptionPlan::where('billing_cycle', $type)->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function calculateMonthlyRevenue()
    {
        try {
            return Transaction::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->where('status', 'completed')
                ->sum('amount') ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function calculateTotalRevenue()
    {
        try {
            return Transaction::where('status', 'completed')->sum('amount') ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function calculateRevenueGrowth()
    {
        try {
            $thisMonth = $this->calculateMonthlyRevenue();
            $lastMonth = Transaction::whereMonth('created_at', Carbon::now()->subMonth()->month)
                ->whereYear('created_at', Carbon::now()->subMonth()->year)
                ->where('status', 'completed')
                ->sum('amount') ?? 0;

            if ($lastMonth == 0) return 0;
            return round((($thisMonth - $lastMonth) / $lastMonth) * 100, 2);
        } catch (\Exception $e) {
            return 0;
        }
    }
}