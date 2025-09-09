<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Patient;
use App\Models\Ward;
use App\Models\Role;
use Carbon\Carbon;

class CaseHandlerDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $hospitalId = $user->hospital_id;

        // Open patient cases
        $openCases = $this->getOpenPatientCases($hospitalId);

        // Admissions & discharges data
        $admissionsDischarges = $this->getAdmissionsDischarges($hospitalId);

        // Ambulance requests assigned
        $ambulanceRequests = $this->getAmbulanceRequests($hospitalId);

        // Case timelines pending updates
        $timelineUpdates = $this->getCaseTimelineUpdates($hospitalId);

        $data = [
            'open_cases' => $openCases,
            'admissions_discharges' => $admissionsDischarges,
            'ambulance_requests' => $ambulanceRequests,
            'timeline_updates' => $timelineUpdates,
        ];

        return view('case-handler.dashboard', $data);
    }

    private function getOpenPatientCases($hospitalId): array
    {
        // Get active patients by ward for case management
        $openCases = Patient::where('hospital_id', $hospitalId)
            ->where('status', 'active')
            ->with('ward')
            ->get()
            ->groupBy(function ($patient) {
                return $patient->ward->name ?? 'Unassigned';
            })
            ->map(function ($wardPatients, $wardName) {
                return [
                    'ward_name' => $wardName,
                    'total_patients' => $wardPatients->count(),
                    'ipd_patients' => $wardPatients->where('type', 'IPD')->count(),
                    'opd_patients' => $wardPatients->where('type', 'OPD')->count(),
                    'patients' => $wardPatients->take(5)->map(function ($patient) {
                        return [
                            'id' => $patient->id,
                            'name' => $patient->name,
                            'type' => $patient->type,
                            'admission_date' => $patient->admission_date ? $patient->admission_date->format('M d, Y') : 'N/A',
                            'days_admitted' => $patient->admission_date ? $patient->admission_date->diffInDays(now()) : 0,
                            'status' => $patient->status,
                        ];
                    })->toArray(),
                ];
            });

        // Overall statistics
        $totalOpenCases = Patient::where('hospital_id', $hospitalId)
            ->where('status', 'active')
            ->count();

        $ipdCases = Patient::where('hospital_id', $hospitalId)
            ->where('status', 'active')
            ->where('type', 'IPD')
            ->count();

        $opdCases = Patient::where('hospital_id', $hospitalId)
            ->where('status', 'active')
            ->where('type', 'OPD')
            ->count();

        return [
            'total_cases' => $totalOpenCases,
            'ipd_cases' => $ipdCases,
            'opd_cases' => $opdCases,
            'cases_by_ward' => $openCases->toArray(),
        ];
    }

    private function getAdmissionsDischarges($hospitalId): array
    {
        // Recent admissions (last 7 days)
        $recentAdmissions = Patient::where('hospital_id', $hospitalId)
            ->whereNotNull('admission_date')
            ->whereBetween('admission_date', [today()->subDays(7), today()])
            ->with('ward')
            ->orderBy('admission_date', 'desc')
            ->take(10)
            ->get()
            ->map(function ($patient) {
                return [
                    'id' => $patient->id,
                    'name' => $patient->name,
                    'type' => $patient->type,
                    'admission_date' => $patient->admission_date->format('M d, Y'),
                    'ward_name' => $patient->ward->name ?? 'Unassigned',
                    'status' => $patient->status,
                ];
            });

        // Recent discharges (last 7 days)
        $recentDischarges = Patient::where('hospital_id', $hospitalId)
            ->whereNotNull('discharge_date')
            ->whereBetween('discharge_date', [today()->subDays(7), today()])
            ->with('ward')
            ->orderBy('discharge_date', 'desc')
            ->take(10)
            ->get()
            ->map(function ($patient) {
                return [
                    'id' => $patient->id,
                    'name' => $patient->name,
                    'type' => $patient->type,
                    'discharge_date' => $patient->discharge_date->format('M d, Y'),
                    'ward_name' => $patient->ward->name ?? 'Unassigned',
                    'admission_duration' => $patient->admission_date && $patient->discharge_date
                        ? $patient->admission_date->diffInDays($patient->discharge_date)
                        : 0,
                ];
            });

        // Statistics
        $thisMonthAdmissions = Patient::where('hospital_id', $hospitalId)
            ->whereNotNull('admission_date')
            ->whereBetween('admission_date', [today()->startOfMonth(), today()])
            ->count();

        $thisMonthDischarges = Patient::where('hospital_id', $hospitalId)
            ->whereNotNull('discharge_date')
            ->whereBetween('discharge_date', [today()->startOfMonth(), today()])
            ->count();

        $avgStayDuration = Patient::where('hospital_id', $hospitalId)
            ->whereNotNull('admission_date')
            ->whereNotNull('discharge_date')
            ->whereBetween('discharge_date', [today()->startOfMonth(), today()])
            ->selectRaw('AVG(DATEDIFF(discharge_date, admission_date)) as avg_duration')
            ->first()
            ->avg_duration ?? 0;

        return [
            'recent_admissions' => $recentAdmissions->toArray(),
            'recent_discharges' => $recentDischarges->toArray(),
            'this_month' => [
                'admissions' => $thisMonthAdmissions,
                'discharges' => $thisMonthDischarges,
                'avg_stay_duration' => round($avgStayDuration, 1),
            ],
        ];
    }

    private function getAmbulanceRequests($hospitalId): array
    {
        // Placeholder: Find patients who might need ambulance transport
        // Using criteria like emergency notes, urgent status, etc.
        $emergencyPatients = Patient::where('hospital_id', $hospitalId)
            ->where('status', 'active')
            ->where(function ($query) {
                $query->where('notes', 'like', '%emergency%')
                      ->orWhere('notes', 'like', '%ambulance%')
                      ->orWhere('notes', 'like', '%urgent%');
            })
            ->with('ward')
            ->get()
            ->map(function ($patient) {
                return [
                    'id' => $patient->id,
                    'name' => $patient->name,
                    'type' => $patient->type,
                    'admission_date' => $patient->admission_date ? $patient->admission_date->format('M d, Y') : 'N/A',
                    'ward_name' => $patient->ward->name ?? 'Unassigned',
                    'request_type' => 'Emergency Transport',
                    'priority' => $this->determineAmbulancePriority($patient),
                    'assigned_status' => rand(0, 1) ? 'Assigned' : 'Pending',
                    'assigned_ambulance' => rand(0, 1) ? 'AMB-' . rand(100, 999) : null,
                ];
            });

        // Statistics
        $totalRequests = $emergencyPatients->count();
        $assignedRequests = $emergencyPatients->where('assigned_status', 'Assigned')->count();
        $pendingRequests = $totalRequests - $assignedRequests;

        return [
            'total_requests' => $totalRequests,
            'assigned_requests' => $assignedRequests,
            'pending_requests' => $pendingRequests,
            'assignment_rate' => $totalRequests > 0 ? round(($assignedRequests / $totalRequests) * 100, 2) : 0,
            'ambulance_requests' => $emergencyPatients->take(8)->toArray(),
        ];
    }

    private function getCaseTimelineUpdates($hospitalId): array
    {
        // Patients requiring timeline updates
        // Based on patients admitted more than 7 days needing follow-up
        $needsTimelineUpdate = Patient::where('hospital_id', $hospitalId)
            ->where('status', 'active')
            ->where('type', 'IPD')
            ->where('admission_date', '<=', today()->subDays(7))
            ->with('ward')
            ->get()
            ->map(function ($patient) {
                $daysAdmitted = $patient->admission_date
                    ? $patient->admission_date->diffInDays(today())
                    : 0;

                return [
                    'id' => $patient->id,
                    'name' => $patient->name,
                    'admission_date' => $patient->admission_date
                        ? $patient->admission_date->format('M d, Y')
                        : 'N/A',
                    'days_admitted' => $daysAdmitted,
                    'ward_name' => $patient->ward->name ?? 'Unassigned',
                    'last_update' => $patient->updated_at->diffForHumans(),
                    'update_status' => $daysAdmitted > 10 ? 'Urgent' : 'Pending',
                    'next_milestone' => $this->calculateNextMilestone($daysAdmitted),
                ];
            })
            ->sortByDesc('days_admitted');

        $totalNeedingUpdate = $needsTimelineUpdate->count();
        $urgentUpdates = $needsTimelineUpdate->where('update_status', 'Urgent')->count();
        $pendingUpdates = $totalNeedingUpdate - $urgentUpdates;

        return [
            'total_pending_updates' => $totalNeedingUpdate,
            'urgent_updates' => $urgentUpdates,
            'pending_updates' => $pendingUpdates,
            'cases_requiring_updates' => $needsTimelineUpdate->take(10)->toArray(),
        ];
    }

    private function determineAmbulancePriority($patient): string
    {
        // Simple priority determination based on patient details
        $notes = strtolower($patient->notes ?? '');
        if (str_contains($notes, 'emergency') || str_contains($notes, 'critical')) {
            return 'Critical';
        } elseif (str_contains($notes, 'urgent')) {
            return 'High';
        } else {
            return 'Medium';
        }
    }

    private function calculateNextMilestone($daysAdmitted): string
    {
        // Calculate next treatment milestone based on admission duration
        if ($daysAdmitted < 3) {
            return 'Initial Assessment Due';
        } elseif ($daysAdmitted < 7) {
            return 'Weekly Review';
        } elseif ($daysAdmitted < 14) {
            return 'Bi-weekly Check-up';
        } else {
            return 'Monthly Consultation';
        }
    }
}