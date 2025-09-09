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

class SaasDashboardController extends Controller
{
    public function index()
    {
        // KPI Metrics for Super Admin Dashboard
        $metrics = [
            'total_hospitals' => Hospital::count(),
            'active_hospitals' => Hospital::where('status', 'active')->count(),
            'inactive_hospitals' => Hospital::where('status', 'inactive')->count(),

            // Subscription metrics using payment_status
            'trial_subscriptions' => Hospital::where('payment_status', 'trial')->count(),
            'paid_subscriptions' => Hospital::where('payment_status', 'active')->count(),
            'free_subscriptions' => Hospital::where('payment_status', 'free')->count(),

            // Revenue metrics from transactions
            'monthly_revenue' => Transaction::where('status', 'completed')
                ->whereMonth('payment_date', now()->month)
                ->whereYear('payment_date', now()->year)
                ->sum('amount'),

            'total_revenue' => Transaction::where('status', 'completed')->sum('amount'),

            // User engagement metrics
            'total_users' => User::whereNotNull('hospital_id')->count(),
            'new_users' => User::whereNotNull('hospital_id')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),

            // Enquiry and subscriber metrics
            'total_enquiries' => Enquiry::count(),
            'unread_enquiries' => Enquiry::where('status', 'unread')->count(),
            'total_subscribers' => Subscriber::where('status', 'active')->count(),

            // Plan distribution using billing_cycle
            'monthly_plans' => Hospital::where('billing_cycle', 'monthly')->count(),
            'yearly_plans' => Hospital::where('billing_cycle', 'yearly')->count(),
        ];

        // Revenue chart data for the past 12 months
        $revenueChart = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $revenue = Transaction::where('status', 'completed')
                ->whereMonth('payment_date', $month->month)
                ->whereYear('payment_date', $month->year)
                ->sum('amount');

            $revenueChart[] = [
                'month' => $month->format('M Y'),
                'revenue' => $revenue
            ];
        }

        // Hospital registration trends (last 30 days)
        $hospitalTrend = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $hospitalTrend[] = [
                'date' => $date->format('M d'),
                'count' => Hospital::whereDate('created_at', $date->format('Y-m-d'))->count()
            ];
        }

        // Recent activities
        $recentHospitals = Hospital::latest()->take(5)->get(['id', 'name', 'created_at']);
        $recentUsers = DB::table('users')
            ->whereNotNull('hospital_id')
            ->latest('created_at')
            ->take(5)
            ->get(['id', 'name', 'email', 'hospital_id', 'created_at']);

        $recentEnquiries = Enquiry::latest()->take(5)->get(['id', 'name', 'email', 'subject', 'status', 'created_at']);

        return view('saas.dashboard', compact(
            'metrics',
            'revenueChart',
            'hospitalTrend',
            'recentHospitals',
            'recentUsers',
            'recentEnquiries'
        ));
    }
}