<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Patient;
use App\Models\Invoice;
use App\Models\Role;
use Carbon\Carbon;

class LabTechnicianDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $hospitalId = $user->hospital_id;

        // Pending test requests
        $pendingRequests = $this->getPendingTestRequests($hospitalId);

        // Tests in progress vs completed
        $testProgress = $this->getTestProgress($hospitalId);

        // Reports pending review/sign-off
        $pendingReports = $this->getPendingReports($hospitalId);

        // Equipment usage logs
        $equipmentLogs = $this->getEquipmentUsageLogs($hospitalId);

        $data = [
            'pending_requests' => $pendingRequests,
            'test_progress' => $testProgress,
            'pending_reports' => $pendingReports,
            'equipment_logs' => $equipmentLogs,
        ];

        return view('lab-technician.dashboard', $data);
    }

    private function getPendingTestRequests($hospitalId): array
    {
        // Simulate lab test requests based on active patients
        // In a real system, this would be a LabTestRequests model
        $activePatients = Patient::where('hospital_id', $hospitalId)
            ->where('status', 'active')
            ->where('type', 'IPD')
            ->get()
            ->take(12);

        $testRequests = [];
        $pathologyTests = ['Blood Test', 'Urine Test', 'Stool Test', 'Culture', 'Biopsy'];
        $radiologyTests = ['X-Ray', 'CT Scan', 'MRI', 'Ultrasound', 'ECG'];
        $diagnosisTests = ['Blood Sugar', 'Cholesterol', 'Liver Function', 'Kidney Function', 'Thyroid'];

        foreach ($activePatients as $patient) {
            $daysAdmitted = max(1, Carbon::now()->diffInDays($patient->admission_date));
            $numTests = rand(1, min($daysAdmitted, 3)); // More tests for longer admissions

            for ($i = 0; $i < $numTests; $i++) {
                $testCategories = ['pathology', 'radiology', 'diagnosis'];
                $category = $testCategories[array_rand($testCategories)];

                $testName = match ($category) {
                    'pathology' => $pathologyTests[array_rand($pathologyTests)],
                    'radiology' => $radiologyTests[array_rand($radiologyTests)],
                    'diagnosis' => $diagnosisTests[array_rand($diagnosisTests)],
                };

                $testRequests[] = [
                    'id' => rand(1000, 9999),
                    'patient_name' => $patient->name,
                    'test_name' => $testName,
                    'test_type' => $category,
                    'request_date' => Carbon::now()->subHours(rand(1, 24))->format('M d, Y H:i'),
                    'priority' => rand(0, 2) ? 'Routine' : 'Urgent', // Mostly routine
                    'requested_by' => 'Dr. ' . ['Smith', 'Johnson', 'Davis', 'Wilson', 'Brown'][rand(0, 4)],
                    'estimated_time' => rand(30, 120) . ' min',
                    'status' => 'pending',
                ];
            }
        }

        // Sort by priority and date
        $sortedRequests = collect($testRequests)->sortBy(function ($request) {
            return match ($request['priority']) {
                'Urgent' => 1,
                'High' => 2,
                default => 3,
            };
        })->values()->all();

        $urgentCount = collect($sortedRequests)->where('priority', 'Urgent')->count();
        $pathologyCount = collect($sortedRequests)->where('test_type', 'pathology')->count();
        $radiologyCount = collect($sortedRequests)->where('test_type', 'radiology')->count();
        $diagnosisCount = collect($sortedRequests)->where('test_type', 'diagnosis')->count();

        return [
            'requests' => array_slice($sortedRequests, 0, 20),
            'summary' => [
                'total_pending' => count($sortedRequests),
                'urgent_count' => $urgentCount,
                'pathology_count' => $pathologyCount,
                'radiology_count' => $radiologyCount,
                'diagnosis_count' => $diagnosisCount,
            ],
        ];
    }

    private function getTestProgress($hospitalId): array
    {
        // Generate simulated test progress data
        $totalTests = rand(45, 75);
        $completedTests = rand(35, $totalTests);
        $inProgressTests = $totalTests - $completedTests;
        $delayedTests = rand(0, max(1, $inProgressTests - 5));

        $testsInProgress = [];
        $completedTestsList = [];

        // Tests in progress
        for ($i = 0; $i < $inProgressTests; $i++) {
            $testNames = [
                'Blood Glucose', 'Hemoglobin', 'Lipid Profile', 'Kidney Function',
                'Chest X-Ray', 'CT Abdomen', 'MRI Brain', 'ECG',
                'Urine Analysis', 'Culture Test', 'Biopsy', 'Blood Test'
            ];

            $testsInProgress[] = [
                'id' => rand(1000, 9999),
                'test_name' => $testNames[array_rand($testNames)],
                'patient_name' => 'Patient ' . rand(100, 999),
                'start_time' => Carbon::now()->subMinutes(rand(10, 180))->format('H:i'),
                'progress_percentage' => rand(15, 85),
                'estimated_completion' => Carbon::now()->addMinutes(rand(15, 60))->format('H:i'),
                'technician' => 'Tech ' . rand(1, 5),
            ];
        }

        // Recently completed tests
        for ($i = 0; $i < min(10, $completedTests); $i++) {
            $testNames = [
                'Blood Glucose', 'Hemoglobin', 'Lipid Profile', 'Kidney Function',
                'Chest X-Ray', 'CT Abdomen', 'MRI Brain', 'ECG',
                'Urine Analysis', 'Culture Test', 'Biopsy', 'Blood Test'
            ];

            $completedTestsList[] = [
                'id' => rand(1000, 9999),
                'test_name' => $testNames[array_rand($testNames)],
                'patient_name' => 'Patient ' . rand(100, 999),
                'completed_time' => Carbon::now()->subMinutes(rand(5, 120))->format('H:i'),
                'duration' => rand(15, 90) . ' min',
                'technician' => 'Tech ' . rand(1, 5),
                'result_status' => rand(0, 9) > 8 ? 'Abnormal' : 'Normal',
            ];
        }

        return [
            'total_tests_today' => $totalTests,
            'completed_tests' => $completedTests,
            'in_progress_tests' => $inProgressTests,
            'delayed_tests' => $delayedTests,
            'completion_rate' => round(($completedTests / $totalTests) * 100, 1),
            'tests_in_progress' => array_slice($testsInProgress, 0, 8),
            'recently_completed' => $completedTestsList,
        ];
    }

    private function getPendingReports($hospitalId): array
    {
        // Simulate pending reports for review/sign-off
        $pendingReportCount = rand(12, 25);
        $urgentReportCount = rand(3, min(6, $pendingReportCount));

        $pendingReports = [];

        for ($i = 0; $i < $pendingReportCount; $i++) {
            $testTypes = [
                'Blood Test', 'Urine Analysis', 'X-Ray', 'CT Scan', 'MRI',
                'Echocardiogram', 'Liver Function', 'Kidney Function', 'Culture Test'
            ];

            $urgent = $i < $urgentReportCount;

            $pendingReports[] = [
                'id' => rand(1000, 9999),
                'test_name' => $testTypes[array_rand($testTypes)],
                'patient_name' => 'Patient ' . rand(100, 999),
                'completed_time' => Carbon::now()->subHours(rand(2, 8))->format('M d, Y H:i'),
                'technician' => 'Tech ' . rand(1, 5),
                'priority' => $urgent ? 'Urgent' : 'Normal',
                'result_summary' => $urgent ? 'Abnormal findings requiring immediate attention' : 'Normal results',
                'pending_reason' => rand(0, 1) ? 'Awaiting senior review' : 'Pending sign-off',
                'waiting_time' => rand(30, 240) . ' min',
                'is_overdue' => $urgent,
            ];
        }

        // Sort by priority
        $sortedReports = collect($pendingReports)->sortBy(function ($report) {
            return match ($report['priority']) {
                'Urgent' => 1,
                default => 2,
            };
        })->values()->all();

        return [
            'reports' => array_slice($sortedReports, 0, 15),
            'summary' => [
                'total_pending' => $pendingReportCount,
                'urgent_count' => $urgentReportCount,
                'normal_count' => $pendingReportCount - $urgentReportCount,
                'average_wait_time' => rand(60, 120) . ' min',
            ],
        ];
    }

    private function getEquipmentUsageLogs($hospitalId): array
    {
        // Simulate equipment usage logs
        $equipment = [
            ['name' => 'CNC Analyzer', 'total_usage_time' => rand(8, 12) * 60],
            ['name' => 'Radiology X-Ray Machine', 'total_usage_time' => rand(6, 10) * 60],
            ['name' => 'CT Scanner', 'total_usage_time' => rand(4, 8) * 60],
            ['name' => 'MRI Scanner', 'total_usage_time' => rand(3, 6) * 60],
            ['name' => 'Ultrasound Machine', 'total_usage_time' => rand(7, 11) * 60],
            ['name' => 'ECG Machine', 'total_usage_time' => rand(9, 14) * 60],
            ['name' => 'Lab Microscope', 'total_usage_time' => rand(10, 15) * 60],
        ];

        $usageLogs = [];
        $maintenanceAlerts = [];

        foreach ($equipment as $item) {
            $usageHours = round($item['total_usage_time'] / 60, 1);
            $dailyUsage = rand(2, 8);

            // Create usage logs
            for ($i = 0; $i < rand(5, 10); $i++) {
                $usageLogs[] = [
                    'equipment_name' => $item['name'],
                    'patient' => 'Patient ' . rand(100, 999),
                    'test_type' => ['Test', 'Scan', 'Analysis'][rand(0, 2)],
                    'start_time' => Carbon::now()->subHours(rand(1, 24))->format('M d H:i'),
                    'duration_min' => rand(15, 90),
                    'technician' => 'Tech ' . rand(1, 5),
                ];
            }

            // Maintenance alerts based on usage
            if ($usageHours > 10 && rand(0, 2) === 0) { // 1/3 chance if usage > 10 hours
                $maintenanceAlerts[] = [
                    'equipment_name' => $item['name'],
                    'alert_type' => 'maintenance_overdue',
                    'last_maintenance' => Carbon::now()->subDays(rand(10, 25))->format('M d, Y'),
                    'next_maintenance' => Carbon::now()->addDays(rand(5, 15))->format('M d, Y'),
                    'severity' => 'High',
                ];
            }
        }

        // Sort logs by latest
        $sortedLogs = collect($usageLogs)
            ->sortByDesc(function ($log) {
                return strtotime($log['start_time']);
            })
            ->take(15)
            ->values()
            ->all();

        return [
            'equipment_status' => $equipment,
            'usage_logs' => $sortedLogs,
            'maintenance_alerts' => $maintenanceAlerts,
            'summary' => [
                'total_equipment' => count($equipment),
                'maintenance_required' => count($maintenanceAlerts),
                'fully_operational' => count($equipment) - count($maintenanceAlerts),
                'average_daily_usage' => round(collect($equipment)->avg('total_usage_time') / 60, 1) . ' hours',
            ],
        ];
    }
}