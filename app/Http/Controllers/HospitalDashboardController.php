<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Patient;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Ward;
use App\Models\Bed;
use App\Models\BloodInventory;
use App\Models\Announcement;
use App\Models\Role;
use Carbon\Carbon;

class HospitalDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Check if user is Super Admin
        $isSuperAdmin = $user->roles()->where('name', 'Super Admin')->exists();

        if ($isSuperAdmin) {
            // Super Admin, redirect to SaaS dashboard
            return redirect()->route('saas.dashboard');
        }

        $hospitalId = $user->hospital_id;

        // Staff counts by role
        $staffCounts = $this->getStaffCounts($hospitalId);

        // Patient stats
        $patientStats = $this->getPatientStats($hospitalId);

        // Finance data
        $financeData = $this->getFinanceData($hospitalId);

        // Bed availability
        $bedData = $this->getBedAvailability($hospitalId);

        // Blood stock levels
        $bloodData = $this->getBloodStockLevels($hospitalId);

        // Top income/expense categories (chart data)
        $chartData = $this->getChartData($hospitalId);

        // Recent announcements
        $announcements = Announcement::active()
            ->where('scheduled_date', '<=', today())
            ->orderBy('scheduled_date', 'desc')
            ->limit(5)
            ->get();

        $data = [
            // Existing
            'user_count' => User::where('hospital_id', $hospitalId)->count(),

            // New data
            'staff_counts' => $staffCounts,
            'patient_stats' => $patientStats,
            'finance_data' => $financeData,
            'bed_data' => $bedData,
            'blood_data' => $bloodData,
            'chart_data' => $chartData,
            'announcements' => $announcements,
        ];

        return view('hospital.dashboard', $data);
    }

    private function getStaffCounts($hospitalId): array
    {
        $roles = ['Doctor', 'Nurse', 'Receptionist', 'Pharmacist'];
        $counts = [];

        foreach ($roles as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $counts[$roleName] = User::where('hospital_id', $hospitalId)
                    ->whereHas('roles', function ($query) use ($role) {
                        $query->where('role_id', $role->id);
                    })
                    ->count();
            } else {
                $counts[$roleName] = 0;
            }
        }

        return $counts;
    }

    private function getPatientStats($hospitalId): array
    {
        return [
            'total_patients' => Patient::where('hospital_id', $hospitalId)->count(),
            'admitted_patients' => Patient::admitted()->count(),
            'discharged_patients' => Patient::discharged()->where('discharge_date', '>=', today()->startOfMonth())->count(),
            'opd_visits' => Patient::opdVisits()->whereHas('admission_date', '>=', today()->startOfMonth())->count(),
        ];
    }

    private function getFinanceData($hospitalId): array
    {
        $invoices = Invoice::where('hospital_id', $hospitalId);
        $payments = Payment::whereHas('invoice', function ($query) use ($hospitalId) {
            $query->where('hospital_id', $hospitalId);
        });

        $totalInvoices = $invoices->sum('amount');
        $totalPayments = $payments->sum('amount');
        $dueAmount = $totalInvoices - $totalPayments;

        // This month data
        $thisMonthStart = today()->startOfMonth();
        $monthlyInvoices = $invoices->where('created_at', '>=', $thisMonthStart)->sum('amount');
        $monthlyPayments = $payments->where('payment_date', '>=', $thisMonthStart)->sum('amount');

        return [
            'total_invoices' => $totalInvoices,
            'total_payments' => $totalPayments,
            'due_amount' => $dueAmount,
            'monthly_invoices' => $monthlyInvoices,
            'monthly_payments' => $monthlyPayments,
            'monthly_due' => $monthlyInvoices - $monthlyPayments,
        ];
    }

    private function getBedAvailability($hospitalId): array
    {
        $wards = Ward::with('beds')->where('hospital_id', $hospitalId)->get();
        $bedData = [];

        foreach ($wards as $ward) {
            $bedData[] = [
                'ward_name' => $ward->name,
                'total_beds' => $ward->beds->count(),
                'occupied_beds' => $ward->beds->where('status', 'occupied')->count(),
                'available_beds' => $ward->beds->where('status', 'available')->count(),
                'maintenance_beds' => $ward->beds->where('status', 'maintenance')->count(),
            ];
        }

        return $bedData;
    }

    private function getBloodStockLevels($hospitalId): array
    {
        return BloodInventory::available()
            ->where('hospital_id', $hospitalId)
            ->selectRaw('blood_type, SUM(units) as total_units')
            ->groupBy('blood_type')
            ->get()
            ->pluck('total_units', 'blood_type')
            ->toArray();
    }

    private function getChartData($hospitalId): array
    {
        // Example: Monthly income categories (last 12 months)
        $incomeData = [];
        $expenseData = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = today()->subMonths($i);
            $monthStart = $month->startOfMonth();
            $monthEnd = $month->endOfMonth();

            $monthlyPayments = Payment::whereHas('invoice', function ($query) use ($hospitalId) {
                    $query->where('hospital_id', $hospitalId);
                })
                ->whereBetween('payment_date', [$monthStart, $monthEnd])
                ->sum('amount');

            $incomeData[] = [
                'month' => $month->format('M Y'),
                'amount' => $monthlyPayments,
                'category' => 'Income'
            ];

            // Expenses could be from purchases, salaries, etc. (placeholder for now)
            $expenseData[] = [
                'month' => $month->format('M Y'),
                'amount' => $monthlyPayments * 0.3, // Placeholder: 30% of income as expenses
                'category' => 'Expenses'
            ];
        }

        return [
            'income_data' => $incomeData,
            'expense_data' => $expenseData,
        ];
    }
}
