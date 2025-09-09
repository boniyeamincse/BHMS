<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Patient;
use App\Models\Ward;
use App\Models\Bed;
use App\Models\BloodInventory;
use App\Models\Role;
use Carbon\Carbon;

class NurseDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $hospitalId = $user->hospital_id;

        // Bed occupancy data
        $bedOccupancy = $this->getBedOccupancy($hospitalId);

        // Assigned patients and medication schedule
        $assignedPatients = $this->getAssignedPatients($hospitalId, $user->id);

        // Pending vitals entry
        $pendingVitals = $this->getPendingVitals($hospitalId);

        // Blood bank alerts
        $bloodAlerts = $this->getBloodAlerts($hospitalId);

        $data = [
            'bed_occupancy' => $bedOccupancy,
            'assigned_patients' => $assignedPatients,
            'pending_vitals' => $pendingVitals,
            'blood_alerts' => $bloodAlerts,
        ];

        return view('nurse.dashboard', $data);
    }

    private function getBedOccupancy($hospitalId): array
    {
        // Get all wards with bed information
        $wards = Ward::where('hospital_id', $hospitalId)
            ->with('beds')
            ->get();

        $wardOccupancyData = $wards->map(function ($ward) {
            $totalBeds = $ward->beds->count();
            $occupiedBeds = $ward->beds->where('status', 'occupied')->count();
            $availableBeds = $ward->beds->where('status', 'available')->count();
            $maintenanceBeds = $ward->beds->where('status', 'maintenance')->count();

            $occupancyRate = $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100, 1) : 0;

            return [
                'ward_name' => $ward->name,
                'total_beds' => $totalBeds,
                'occupied_beds' => $occupiedBeds,
                'available_beds' => $availableBeds,
                'maintenance_beds' => $maintenanceBeds,
                'occupancy_rate' => $occupancyRate,
                'status' => $this->getOccupancyStatus($occupancyRate),
            ];
        })->sortBy('ward_name');

        // Overall hospital statistics
        $totalHospitalBeds = $wards->sum(function ($ward) {
            return $ward->beds->count();
        });

        $occupiedHospitalBeds = $wards->sum(function ($ward) {
            return $ward->beds->where('status', 'occupied')->count();
        });

        $availableHospitalBeds = $wards->sum(function ($ward) {
            return $ward->beds->where('status', 'available')->count();
        });

        $overallOccupancyRate = $totalHospitalBeds > 0
            ? round(($occupiedHospitalBeds / $totalHospitalBeds) * 100, 1)
            : 0;

        return [
            'wards' => $wardOccupancyData->values()->all(),
            'hospital_summary' => [
                'total_beds' => $totalHospitalBeds,
                'occupied_beds' => $occupiedHospitalBeds,
                'available_beds' => $availableHospitalBeds,
                'overall_occupancy_rate' => $overallOccupancyRate,
            ],
        ];
    }

    private function getAssignedPatients($hospitalId, $nurseId): array
    {
        // Get patients assigned to this nurse (simplified - using all active IPD patients)
        // In a real system, there would be a nurse-patient assignment table
        $assignedPatients = Patient::where('hospital_id', $hospitalId)
            ->where('type', 'IPD')
            ->where('status', 'active')
            ->whereNotNull('ward_id')
            ->with('ward')
            ->get()
            ->map(function ($patient) {
                return [
                    'id' => $patient->id,
                    'name' => $patient->name,
                    'ward_name' => $patient->ward->name ?? 'Unassigned',
                    'admission_date' => $patient->admission_date ? $patient->admission_date->format('M d, Y') : 'N/A',
                    'days_admitted' => $patient->admission_date ? $patient->admission_date->diffInDays(now()) : 0,
                    'medication_schedule' => $this->generateMedicationSchedule($patient),
                    'next_checking_time' => $this->generateNextCheckingTime(),
                    'vitals_pending' => rand(0, 1), // Simulate some have pending vitals
                ];
            })->take(8); // Limit to manageable number

        // Upcoming medication schedule for today
        $medicationSchedule = $this->generateTodaysMedicationSchedule($assignedPatients);

        return [
            'patients' => $assignedPatients->toArray(),
            'total_assigned' => $assignedPatients->count(),
            'medication_schedule' => $medicationSchedule,
        ];
    }

    private function getPendingVitals($hospitalId): array
    {
        // Find patients who require vitals entry
        // Using patients admitted today or recently as needing vitals
        $recentlyAdmitted = Patient::where('hospital_id', $hospitalId)
            ->where('type', 'IPD')
            ->where('status', 'active')
            ->where('admission_date', '>=', today()->subDays(2))
            ->with('ward')
            ->get()
            ->merge(
                // Also include some random patients for demonstration
                Patient::where('hospital_id', $hospitalId)
                    ->where('type', 'IPD')
                    ->where('status', 'active')
                    ->inRandomOrder()
                    ->limit(3)
                    ->get()
            )
            ->unique('id')
            ->take(12);

        $pendingVitalsEntries = $recentlyAdmitted->map(function ($patient) {
            $lastVitals = now()->subHours(rand(6, 24)); // Random last vitals time
            $isOverdue = now()->diffInHours($lastVitals) > 6;

            return [
                'id' => $patient->id,
                'name' => $patient->name,
                'ward_name' => $patient->ward->name ?? 'Unassigned',
                'patient_type' => $patient->type,
                'admission_date' => $patient->admission_date ? $patient->admission_date->format('M d, Y') : 'N/A',
                'last_vitals' => $lastVitals->format('M d H:i'),
                'hours_since_last' => now()->diffInHours($lastVitals),
                'is_overdue' => $isOverdue,
                'due_at' => $lastVitals->addHours(6)->format('M d H:i'), // Next vitals due after 6 hours
                'priority' => $isOverdue ? 'High' : 'Normal',
            ];
        })->sortByDesc('is_overdue');

        $overdueCount = $pendingVitalsEntries->where('is_overdue', true)->count();
        $totalPending = $pendingVitalsEntries->count();

        return [
            'entries' => $pendingVitalsEntries->toArray(),
            'total_pending' => $totalPending,
            'overdue_count' => $overdueCount,
            'completion_rate' => $totalPending > 0 ? round(($totalPending - $overdueCount) / $totalPending * 100, 1) : 100,
        ];
    }

    private function getBloodAlerts($hospitalId): array
    {
        // Get low stock blood inventory
        $criticalStock = BloodInventory::where('hospital_id', $hospitalId)
            ->available()
            ->where('units', '<', 10)
            ->orderBy('units')
            ->get();

        $expringSoon = BloodInventory::where('hospital_id', $hospitalId)
            ->expiringSoon(7)
            ->orderBy('expiry_date')
            ->get()
            ->map(function ($item) {
                return [
                    'blood_type' => $item->blood_type,
                    'units' => $item->units,
                    'expiry_date' => $item->expiry_date->format('M d, Y'),
                    'days_until_expiry' => $item->days_until_expiry,
                    'alert_type' => 'expiring_soon',
                ];
            });

        $lowStockAlerts = $criticalStock->map(function ($item) {
            return [
                'blood_type' => $item->blood_type,
                'units' => $item->units,
                'warning_level' => $item->units < 5 ? 'Critical' : 'Low',
                'alert_type' => 'low_stock',
            ];
        });

        // Combine all alerts
        $allAlerts = collect($lowStockAlerts)->merge($expringSoon)->sortBy(function ($alert) {
            return match ($alert['alert_type']) {
                'low_stock' => $alert['warning_level'] === 'Critical' ? 1 : 2,
                'expiring_soon' => 3,
                default => 4,
            };
        });

        // Summary statistics
        $totalAlerts = $allAlerts->count();
        $criticalAlerts = $allAlerts->where('warning_level', 'Critical')->count() +
                         $allAlerts->where('alert_type', 'expiring_soon')
                                  ->where('days_until_expiry', '<=', 1)->count();
        $expiringAlerts = $allAlerts->where('alert_type', 'expiring_soon')->count();

        return [
            'alerts' => $allAlerts->values()->all(),
            'summary' => [
                'total_alerts' => $totalAlerts,
                'critical_alerts' => $criticalAlerts,
                'expiring_soon' => $expiringAlerts,
                'low_stock' => $totalAlerts - $expiringAlerts,
            ],
        ];
    }

    private function getOccupancyStatus($occupancyRate): string
    {
        if ($occupancyRate >= 95) return 'critical';
        if ($occupancyRate >= 80) return 'high';
        if ($occupancyRate >= 60) return 'moderate';
        return 'low';
    }

    private function generateMedicationSchedule($patient): array
    {
        // Generate a sample medication schedule for the patient
        $medications = [
            [
                'name' => 'Paracetamol 500mg',
                'dose' => '500mg',
                'frequency' => '3 times/day',
                'time' => '08:00, 14:00, 20:00',
            ],
            [
                'name' => 'Amoxicillin 250mg',
                'dose' => '250mg',
                'frequency' => '2 times/day',
                'time' => '10:00, 18:00',
            ],
        ];

        return $medications;
    }

    private function generateNextCheckingTime(): string
    {
        $nextCheckHour = rand(1, 6);
        return now()->addHours($nextCheckHour)->format('H:i');
    }

    private function generateTodaysMedicationSchedule($patients): array
    {
        $schedule = [];
        $currentHour = now()->hour;

        // Generate upcoming medication times for patients
        foreach ($patients as $patient) {
            foreach ($patient['medication_schedule'] as $med) {
                $times = explode(', ', $med['time']);
                foreach ($times as $time) {
                    $timeValue = (int)explode(':', $time)[0];
                    if ($timeValue > $currentHour) {
                        $schedule[] = [
                            'patient_name' => $patient['name'],
                            'medication' => $med['name'],
                            'dose' => $med['dose'],
                            'scheduled_time' => $time,
                            'due_in_hours' => $timeValue - $currentHour,
                        ];
                    }
                }
            }
        }

        return collect($schedule)
            ->sortBy('due_in_hours')
            ->take(10)
            ->values()
            ->all();
    }
}