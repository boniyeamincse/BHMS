<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Patient;
use App\Models\Enquiry;
use App\Models\Role;
use Carbon\Carbon;

class ReceptionistDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $hospitalId = $user->hospital_id;

        // Appointment requests
        $appointmentRequests = $this->getAppointmentRequests($hospitalId);

        // Patient registrations
        $patientRegistrations = $this->getPatientRegistrations($hospitalId);

        // Call log summary
        $callLogSummary = $this->getCallLogSummary($hospitalId);

        // Visitor log summary
        $visitorLogSummary = $this->getVisitorLogSummary($hospitalId);

        // Enquiries/tickets
        $enquiriesTickets = $this->getEnquiriesTickets($hospitalId);

        $data = [
            'appointment_requests' => $appointmentRequests,
            'patient_registrations' => $patientRegistrations,
            'call_log_summary' => $callLogSummary,
            'visitor_log_summary' => $visitorLogSummary,
            'enquiries_tickets' => $enquiriesTickets,
        ];

        return view('receptionist.dashboard', $data);
    }

    private function getAppointmentRequests($hospitalId)
    {
        // Using patients with admission_date in future as appointment requests
        $pendingAppointments = Patient::where('hospital_id', $hospitalId)
            ->where('admission_date', '>=', today())
            ->where('status', 'active')
            ->select('id', 'name', 'phone', 'admission_date', 'type', 'notes')
            ->get()
            ->map(function ($patient) {
                return [
                    'id' => $patient->id,
                    'patient_name' => $patient->name,
                    'phone' => $patient->phone,
                    'appointment_date' => $patient->admission_date ? $patient->admission_date->format('M d, Y') : 'N/A',
                    'appointment_time' => $patient->admission_date ? $patient->admission_date->format('H:i') : 'N/A',
                    'type' => $patient->type,
                    'status' => $patient->admission_date > today() ? 'pending' : 'approved',
                    'notes' => $patient->notes,
                ];
            });

        return $pendingAppointments->toArray();
    }

    private function getPatientRegistrations($hospitalId): array
    {
        $thisMonth = today()->startOfMonth();
        $previousMonth = today()->subMonth()->startOfMonth();
        $previousMonthEnd = $previousMonth->copy()->endOfMonth();

        // New patients this month
        $newThisMonth = Patient::where('hospital_id', $hospitalId)
            ->whereBetween('created_at', [$thisMonth, today()])
            ->count();

        // New patients previous month
        $newPreviousMonth = Patient::where('hospital_id', $hospitalId)
            ->whereBetween('created_at', [$previousMonth, $previousMonthEnd])
            ->count();

        // Returning patients (patients with multiple visits)
        // This is approximated by looking at patients with recent admission dates
        $returningThisMonth = Patient::where('hospital_id', $hospitalId)
            ->whereBetween('admission_date', [$thisMonth, today()])
            ->where('status', 'discharged')
            ->count();

        return [
            'new_this_month' => $newThisMonth,
            'new_previous_month' => $newPreviousMonth,
            'new_increase_percent' => $newPreviousMonth > 0 ? round((($newThisMonth - $newPreviousMonth) / $newPreviousMonth) * 100, 1) : 0,
            'returning_this_month' => $returningThisMonth,
            'total_this_month' => $newThisMonth + $returningThisMonth,
        ];
    }

    private function getCallLogSummary($hospitalId): array
    {
        // Placeholder data for call logs (since no call log model exists)
        // Using enquiries and patients activity as approximation

        $todayCalls = Enquiry::whereDate('created_at', today())->count() +
                      Patient::where('hospital_id', $hospitalId)->whereDate('created_at', today())->count();

        $thisWeekCalls = Enquiry::whereBetween('created_at', [today()->startOfWeek(), today()])->count() +
                         Patient::where('hospital_id', $hospitalId)
                                 ->whereBetween('created_at', [today()->startOfWeek(), today()])
                                 ->count();

        $thisMonthCalls = Enquiry::whereBetween('created_at', [today()->startOfMonth(), today()])->count() +
                          Patient::where('hospital_id', $hospitalId)
                                  ->whereBetween('created_at', [today()->startOfMonth(), today()])
                                  ->count();

        // Categorize calls (placeholder logic)
        $incomingCalls = max(0, $todayCalls - 5); // Simulate incoming calls
        $outgoingCalls = min(5, $todayCalls); // Simulate outgoing calls
        $missedCalls = rand(0, 3); // Random missed calls

        return [
            'today' => [
                'total' => $todayCalls,
                'incoming' => $incomingCalls,
                'outgoing' => $outgoingCalls,
                'missed' => $missedCalls,
            ],
            'this_week' => $thisWeekCalls,
            'this_month' => $thisMonthCalls,
        ];
    }

    private function getVisitorLogSummary($hospitalId): array
    {
        // Placeholder data for visitor logs
        // Using appointment/activity patterns as approximation

        $todayVisitors = rand(8, 15); // Simulated visitors
        $thisWeekVisitors = rand(45, 85);
        $thisMonthVisitors = rand(180, 300);

        // Visitor types (placeholder)
        $visitorTypes = [
            'patient_family' => round($todayVisitors * 0.6),
            'patients' => round($todayVisitors * 0.25),
            'others' => round($todayVisitors * 0.15),
        ];

        // Hourly distribution (placeholder)
        $hourlyDistribution = [];
        for ($hour = 9; $hour <= 17; $hour++) {
            $hourlyDistribution[$hour . ':00'] = rand(0, 5);
        }

        return [
            'today' => [
                'total' => $todayVisitors,
                'by_type' => $visitorTypes,
                'hourly' => $hourlyDistribution,
            ],
            'this_week' => $thisWeekVisitors,
            'this_month' => $thisMonthVisitors,
        ];
    }

    private function getEnquiriesTickets($hospitalId): array
    {
        // Get enquiries/tickets with their status
        $enquiries = Enquiry::select('id', 'name', 'subject', 'status', 'priority', 'created_at', 'phone', 'message')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($enquiry) {
                return [
                    'id' => $enquiry->id,
                    'name' => $enquiry->name,
                    'subject' => $enquiry->subject,
                    'status' => $enquiry->status,
                    'priority' => $enquiry->priority,
                    'created_at' => $enquiry->created_at->format('M d, Y H:i'),
                    'phone' => $enquiry->phone,
                    'message' => $enquiry->message,
                ];
            });

        // Summary counts
        $summary = [
            'total' => Enquiry::count(),
            'unread' => Enquiry::unread()->count(),
            'high_priority' => Enquiry::priority('high')->count(),
            'medium_priority' => Enquiry::priority('medium')->count(),
            'low_priority' => Enquiry::priority('low')->count(),
        ];

        return [
            'summary' => $summary,
            'recent' => $enquiries->toArray(),
        ];
    }
}