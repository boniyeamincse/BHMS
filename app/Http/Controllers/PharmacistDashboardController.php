<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Patient;
use App\Models\Role;
use Carbon\Carbon;

class PharmacistDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $hospitalId = $user->hospital_id;

        // Medicine stock data
        $medicineStock = $this->getMedicineStock($hospitalId);

        // Prescriptions pending fulfillment
        $pendingPrescriptions = $this->getPendingPrescriptions($hospitalId);

        // Sales/bills generated today
        $todaySales = $this->getTodaySales($hospitalId);

        // Supplier/restock alerts
        $supplierAlerts = $this->getSupplierAlerts($hospitalId);

        $data = [
            'medicine_stock' => $medicineStock,
            'pending_prescriptions' => $pendingPrescriptions,
            'today_sales' => $todaySales,
            'supplier_alerts' => $supplierAlerts,
        ];

        return view('pharmacist.dashboard', $data);
    }

    private function getMedicineStock($hospitalId): array
    {
        // Simulate medicine stock data (similar to blood inventory but for medicines)
        $medicines = [
            ['name' => 'Paracetamol 500mg', 'stock' => 15, 'min_stock' => 20, 'expiry_date' => Carbon::now()->addDays(15)->format('Y-m-d')],
            ['name' => 'Amoxicillin 250mg', 'stock' => 8, 'min_stock' => 15, 'expiry_date' => Carbon::now()->addDays(45)->format('Y-m-d')],
            ['name' => 'Ibuprofen 200mg', 'stock' => 3, 'min_stock' => 10, 'expiry_date' => Carbon::now()->addDays(120)->format('Y-m-d')],
            ['name' => 'Metformin 500mg', 'stock' => 28, 'min_stock' => 25, 'expiry_date' => Carbon::now()->addDays(180)->format('Y-m-d')],
            ['name' => 'Omeprazole 20mg', 'stock' => 2, 'min_stock' => 15, 'expiry_date' => Carbon::now()->addDays(30)->format('Y-m-d')],
            ['name' => 'Simvastatin 10mg', 'stock' => 12, 'min_stock' => 20, 'expiry_date' => Carbon::now()->addDays(90)->format('Y-m-d')],
            ['name' => 'Lisinopril 5mg', 'stock' => 5, 'min_stock' => 15, 'expiry_date' => Carbon::now()->addDays(60)->format('Y-m-d')],
            ['name' => 'Amlodipine 5mg', 'stock' => 19, 'min_stock' => 18, 'expiry_date' => Carbon::now()->addDays(200)->format('Y-m-d')],
        ];

        // Process stock alerts
        $lowStockAlerts = [];
        $expiringSoonAlerts = [];

        foreach ($medicines as $medicine) {
            // Low stock check
            if ($medicine['stock'] <= $medicine['min_stock']) {
                $lowStockAlerts[] = [
                    'medicine_name' => $medicine['name'],
                    'current_stock' => $medicine['stock'],
                    'min_stock' => $medicine['min_stock'],
                    'alert_level' => $medicine['stock'] <= ($medicine['min_stock'] * 0.5) ? 'Critical' : 'Low',
                    'alert_type' => 'low_stock',
                ];
            }

            // Expiring soon check (within 60 days)
            $daysToExpiry = Carbon::now()->diffInDays(Carbon::parse($medicine['expiry_date']));
            if ($daysToExpiry <= 60) {
                $expiringSoonAlerts[] = [
                    'medicine_name' => $medicine['name'],
                    'days_left' => $daysToExpiry,
                    'expiry_date' => $medicine['expiry_date'],
                    'current_stock' => $medicine['stock'],
                    'alert_type' => 'expiring_soon',
                ];
            }
        }

        // Combine and sort alerts by priority
        $allAlerts = collect(array_merge($lowStockAlerts, $expiringSoonAlerts))
            ->sortBy(function ($alert) {
                return match ($alert['alert_type']) {
                    'low_stock' => $alert['alert_level'] === 'Critical' ? 1 : 2,
                    'expiring_soon' => $alert['days_left'] <= 30 ? 3 : 4,
                    default => 5,
                };
            })
            ->values()
            ->toArray();

        return [
            'medicines' => $medicines,
            'alerts' => $allAlerts,
            'summary' => [
                'total_medicines' => count($medicines),
                'low_stock_count' => count($lowStockAlerts),
                'expiring_soon_count' => count($expiringSoonAlerts),
                'critical_count' => collect($allAlerts)->where('alert_level', 'Critical')->count(),
            ],
        ];
    }

    private function getPendingPrescriptions($hospitalId): array
    {
        // Use invoices as prescriptions (approximation)
        // Select unpaid invoices as "pending prescriptions"
        $pendingInvoices = Invoice::where('hospital_id', $hospitalId)
            ->where('status', 'pending')
            ->with('patient')
            ->orderBy('due_date')
            ->get()
            ->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'patient_name' => $invoice->patient->name ?? 'Unknown Patient',
                    'prescription_date' => $invoice->created_at->format('M d, Y'),
                    'prescription_type' => $invoice->description ?? 'General Prescription',
                    'amount' => $invoice->amount,
                    'remaining_amount' => $invoice->remaining_amount,
                    'due_date' => $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'N/A',
                    'days_overdue' => $invoice->due_date ? max(0, Carbon::now()->diffInDays(Carbon::parse($invoice->due_date), false)) : 0,
                    'urgency' => $this->calculatePrescriptionUrgency($invoice),
                ];
            })
            ->take(15); // Limit to prevent overload

        $urgentCount = $pendingInvoices->where('urgency', 'Urgent')->count();
        $overdueCount = $pendingInvoices->where('days_overdue', '>', 0)->count();
        $totalPending = $pendingInvoices->count();

        return [
            'prescriptions' => $pendingInvoices->toArray(),
            'summary' => [
                'total_pending' => $totalPending,
                'urgent_count' => $urgentCount,
                'overdue_count' => $overdueCount,
                'total_amount_pending' => $pendingInvoices->sum('remaining_amount'),
                'completion_rate' => $totalPending > 0 ? round(($totalPending - $overdueCount) / $totalPending * 100, 1) : 100,
            ],
        ];
    }

    private function getTodaySales($hospitalId): array
    {
        // Use payments as sales revenue
        $todayPayments = Payment::whereHas('invoice', function ($query) use ($hospitalId) {
                $query->where('hospital_id', $hospitalId);
            })
            ->whereDate('payment_date', today())
            ->with(['invoice.patient'])
            ->get();

        // Today's sales metrics
        $totalSales = $todayPayments->sum('amount');
        $cashPayments = $todayPayments->where('method', 'cash')->sum('amount');
        $cardPayments = $todayPayments->where('method', 'card')->sum('amount');
        $onlinePayments = $todayPayments->where('method', 'online')->sum('amount');

        // Previous day for comparison
        $yesterdaySales = Payment::whereHas('invoice', function ($query) use ($hospitalId) {
                $query->where('hospital_id', $hospitalId);
            })
            ->whereDate('payment_date', today()->subDay())
            ->sum('amount');

        $salesGrowth = $yesterdaySales > 0
            ? round((($totalSales - $yesterdaySales) / $yesterdaySales) * 100, 1)
            : 0;

        // Recent sales transactions (last 10)
        $recentSales = $todayPayments->sortByDesc('payment_date')->take(10)->map(function ($payment) {
            return [
                'patient_name' => $payment->invoice->patient->name ?? 'Unknown',
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method_label,
                'time' => $payment->payment_date->format('H:i'),
                'invoice_id' => $payment->invoice->id,
            ];
        })->toArray();

        return [
            'summary' => [
                'total_sales' => $totalSales,
                'cash_payments' => $cashPayments,
                'card_payments' => $cardPayments,
                'online_payments' => $onlinePayments,
                'sales_growth' => $salesGrowth,
                'transaction_count' => $todayPayments->count(),
            ],
            'recent_sales' => $recentSales,
        ];
    }

    private function getSupplierAlerts($hospitalId): array
    {
        // Simulate supplier/restock alerts
        $suppliers = [
            ['name' => 'Medlife Pharmaceuticals', 'status' => 'active', 'last_order' => today()->subDays(rand(1, 7))],
            ['name' => 'Global Medical Supplies', 'status' => 'active', 'last_order' => today()->subDays(rand(3, 14))],
            ['name' => 'Healthcare Distributors', 'status' => 'inactive', 'last_order' => today()->subDays(rand(30, 60))],
            ['name' => 'MediCorp Ltd', 'status' => 'active', 'last_order' => today()->subDays(rand(2, 10))],
            ['name' => 'Wellness Supply Chain', 'status' => 'pending', 'last_order' => today()->subDays(rand(7, 21))],
        ];

        $restockAlerts = [];
        $supplierAlerts = [];

        foreach ($suppliers as $supplier) {
            $daysSinceLastOrder = Carbon::now()->diffInDays(Carbon::parse($supplier['last_order']));

            if ($daysSinceLastOrder > 14 && $supplier['status'] !== 'inactive') {
                $restockAlerts[] = [
                    'supplier_name' => $supplier['name'],
                    'days_since_order' => $daysSinceLastOrder,
                    'last_order_date' => $supplier['last_order'],
                    'status' => $supplier['status'],
                    'alert_type' => 'restock_needed',
                ];
            }

            if ($supplier['status'] === 'inactive' || $supplier['status'] === 'pending') {
                $supplierAlerts[] = [
                    'supplier_name' => $supplier['name'],
                    'status' => $supplier['status'],
                    'last_order_date' => $supplier['last_order'],
                    'alert_type' => 'supplier_status',
                ];
            }
        }

        // Combine alerts
        $allAlerts = collect(array_merge($restockAlerts, $supplierAlerts))
            ->sortBy(function ($alert) {
                return match ($alert['alert_type']) {
                    'supplier_status' => 1,
                    'restock_needed' => 2,
                    default => 3,
                };
            })
            ->values()
            ->toArray();

        return [
            'alerts' => $allAlerts,
            'summary' => [
                'total_suppliers' => count($suppliers),
                'active_suppliers' => collect($suppliers)->where('status', 'active')->count(),
                'inactive_suppliers' => collect($suppliers)->where('status', 'inactive')->count(),
                'pending_suppliers' => collect($suppliers)->where('status', 'pending')->count(),
                'restock_alerts_count' => count($restockAlerts),
            ],
        ];
    }

    private function calculatePrescriptionUrgency($invoice): string
    {
        $daysOverdue = $invoice->due_date ? max(0, Carbon::now()->diffInDays(Carbon::parse($invoice->due_date), false)) : 0;

        if ($daysOverdue > 7) {
            return 'Urgent';
        } elseif ($daysOverdue > 3) {
            return 'High';
        } elseif ($daysOverdue > 0) {
            return 'Medium';
        } else {
            return 'Normal';
        }
    }
}