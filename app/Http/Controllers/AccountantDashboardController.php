<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Role;
use Carbon\Carbon;

class AccountantDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $hospitalId = $user->hospital_id;

        // Total revenue calculations
        $revenueData = $this->getRevenueData($hospitalId);

        // Pending bills & invoices
        $pendingInvoices = $this->getPendingInvoices($hospitalId);

        // Payments received vs due
        $paymentData = $this->getPaymentData($hospitalId);

        // Payrolls processed/pending (placeholder logic)
        $payrollData = $this->getPayrollData($hospitalId);

        // Income vs expense charts data
        $chartData = $this->getChartData($hospitalId);

        $data = [
            'revenue_data' => $revenueData,
            'pending_invoices' => $pendingInvoices,
            'payment_data' => $paymentData,
            'payroll_data' => $payrollData,
            'chart_data' => $chartData,
        ];

        return view('accountant.dashboard', $data);
    }

    private function getRevenueData($hospitalId): array
    {
        $thisMonth = today()->startOfMonth();
        $thisYear = today()->startOfYear();

        // Month-to-date revenue
        $monthToDateRevenue = Payment::whereHas('invoice', function ($query) use ($hospitalId) {
                $query->where('hospital_id', $hospitalId);
            })
            ->whereBetween('payment_date', [$thisMonth, today()])
            ->sum('amount');

        // Year-to-date revenue
        $yearToDateRevenue = Payment::whereHas('invoice', function ($query) use ($hospitalId) {
                $query->where('hospital_id', $hospitalId);
            })
            ->whereBetween('payment_date', [$thisYear, today()])
            ->sum('amount');

        // Previous month revenue for comparison
        $previousMonth = today()->subMonth()->startOfMonth();
        $previousMonthEnd = $previousMonth->copy()->endOfMonth();
        $previousMonthRevenue = Payment::whereHas('invoice', function ($query) use ($hospitalId) {
                $query->where('hospital_id', $hospitalId);
            })
            ->whereBetween('payment_date', [$previousMonth, $previousMonthEnd])
            ->sum('amount');

        // Calculate percentage change
        $monthlyChange = $previousMonthRevenue > 0
            ? (($monthToDateRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100
            : 0;

        return [
            'month_to_date' => $monthToDateRevenue,
            'year_to_date' => $yearToDateRevenue,
            'monthly_change_percent' => round($monthlyChange, 2),
            'previous_month' => $previousMonthRevenue,
        ];
    }

    private function getPendingInvoices($hospitalId): array
    {
        // Get pending and overdue invoices
        $pendingInvoices = Invoice::where('hospital_id', $hospitalId)
            ->where('status', 'pending')
            ->orWhere('status', 'overdue')
            ->with('patient', 'payments')
            ->orderBy('due_date')
            ->get();

        $pendingInvoicesData = $pendingInvoices->map(function ($invoice) {
            return [
                'id' => $invoice->id,
                'patient_name' => $invoice->patient->name ?? 'Unknown',
                'amount' => $invoice->amount,
                'due_date' => $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'N/A',
                'status' => $invoice->status,
                'paid_amount' => $invoice->total_paid,
                'remaining_amount' => $invoice->remaining_amount,
            ];
        });

        // Summary totals
        $totalPendingAmount = $pendingInvoices->sum('remaining_amount');
        $overdueCount = $pendingInvoices->where('status', 'overdue')->count();
        $overdueAmount = $pendingInvoices->where('status', 'overdue')->sum('remaining_amount');

        return [
            'invoices' => $pendingInvoicesData->toArray(),
            'summary' => [
                'total_pending_amount' => $totalPendingAmount,
                'overdue_count' => $overdueCount,
                'overdue_amount' => $overdueAmount,
                'pending_count' => $pendingInvoices->count(),
            ]
        ];
    }

    private function getPaymentData($hospitalId): array
    {
        // Payments received this month
        $thisMonth = today()->startOfMonth();
        $paymentsReceived = Payment::whereHas('invoice', function ($query) use ($hospitalId) {
                $query->where('hospital_id', $hospitalId);
            })
            ->whereBetween('payment_date', [$thisMonth, today()])
            ->with(['invoice.patient'])
            ->get();

        // Total dues from all pending invoices
        $totalDues = Invoice::where('hospital_id', $hospitalId)
            ->pending()
            ->sum('remaining_amount');

        $receivedThisMonth = $paymentsReceived->sum('amount');

        // Recent payments (last 10)
        $recentPayments = $paymentsReceived->sortByDesc('payment_date')->take(10)->map(function ($payment) {
            return [
                'patient_name' => $payment->invoice->patient->name ?? 'Unknown',
                'amount' => $payment->amount,
                'payment_date' => $payment->payment_date->format('M d, Y'),
                'method' => $payment->payment_method_label,
            ];
        });

        return [
            'received_this_month' => $receivedThisMonth,
            'total_dues' => $totalDues,
            'collection_rate' => $totalDues > 0 ? round(($receivedThisMonth / $totalDues) * 100, 2) : 0,
            'recent_payments' => $recentPayments->toArray(),
        ];
    }

    private function getPayrollData($hospitalId): array
    {
        // Placeholder data for payrolls (using user count as approximation)
        $activeEmployees = User::where('hospital_id', $hospitalId)->count();

        // Simulate processed payrolls for this month
        $processedThisMonth = round($activeEmployees * 0.8);

        // Simulate pending payroll amounts
        $averageSalary = 5000; // Placeholder
        $pendingPayroll = ($activeEmployees - $processedThisMonth) * $averageSalary;
        $processedPayroll = $processedThisMonth * $averageSalary;

        return [
            'active_employees' => $activeEmployees,
            'processed_this_month' => $processedThisMonth,
            'pending_this_month' => $activeEmployees - $processedThisMonth,
            'processed_amount' => $processedPayroll,
            'pending_amount' => $pendingPayroll,
            'total_payroll_cost' => $processedPayroll + $pendingPayroll,
        ];
    }

    private function getChartData($hospitalId): array
    {
        // Generate income expense chart data for last 12 months
        $incomeData = [];
        $expenseData = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = today()->subMonths($i);
            $monthStart = $month->startOfMonth();
            $monthEnd = $month->endOfMonth();

            // Calculate monthly income (payments received)
            $monthlyIncome = Payment::whereHas('invoice', function ($query) use ($hospitalId) {
                    $query->where('hospital_id', $hospitalId);
                })
                ->whereBetween('payment_date', [$monthStart, $monthEnd])
                ->sum('amount');

            // Calculate expenses (30% of income as placeholder)
            $monthlyExpenses = $monthlyIncome * 0.3;

            $incomeData[] = [
                'month' => $month->format('M Y'),
                'amount' => $monthlyIncome,
                'category' => 'Income'
            ];

            $expenseData[] = [
                'month' => $month->format('M Y'),
                'amount' => $monthlyExpenses,
                'category' => 'Expenses'
            ];
        }

        return [
            'income_data' => $incomeData,
            'expense_data' => $expenseData,
        ];
    }
}