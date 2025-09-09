<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Patient;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Announcement;
use Carbon\Carbon;

class PatientDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // For demonstration, we'll simulate a patient's data
        // In a real system, this would be based on the authenticated user's patient profile
        $patientData = $this->getPatientData();

        // Upcoming appointments
        $upcomingAppointments = $this->getUpcomingAppointments($patientData);

        // Bills/payments
        $billPayments = $this->getBillPayments($patientData);

        // Prescriptions & reports
        $prescriptionsReports = $this->getPrescriptionsReports($patientData);

        // IPD admission history
        $ipdHistory = $this->getIpdAdmissionHistory($patientData);

        // Notifications/messages
        $notifications = $this->getNotifications();

        // Tele-consultation links
        $teleConsultations = $this->getTeleConsultationLinks($patientData);

        $data = [
            'patient_data' => $patientData,
            'upcoming_appointments' => $upcomingAppointments,
            'bill_payments' => $billPayments,
            'prescriptions_reports' => $prescriptionsReports,
            'ipd_history' => $ipdHistory,
            'notifications' => $notifications,
            'tele_consultations' => $teleConsultations,
        ];

        return view('patient.dashboard', $data);
    }

    private function getPatientData(): array
    {
        // Simulate current patient information
        // In a real system, this would come from authenticated patient profile
        return [
            'id' => 1,
            'name' => 'John Doe',
            'phone' => '+1-234-567-8900',
            'email' => 'john.doe@email.com',
            'date_of_birth' => '1985-03-15',
            'age' => 39,
            'gender' => 'Male',
            'blood_group' => 'O+',
            'emergency_contact' => 'Jane Doe (+1-234-567-8901)',
            'current_status' => 'active',
            'patient_since' => '2023-01-15',
        ];
    }

    private function getUpcomingAppointments($patientData): array
    {
        // Simulate upcoming appointments
        $appointments = [
            [
                'id' => 1,
                'date' => Carbon::now()->addDays(3)->format('Y-m-d'),
                'time' => '09:00',
                'type' => 'General Consultation',
                'doctor' => 'Dr. Sarah Johnson',
                'department' => 'General Medicine',
                'location' => 'OPD Block A',
                'status' => 'confirmed',
                'notes' => 'Annual health check-up',
            ],
            [
                'id' => 2,
                'date' => Carbon::now()->addDays(7)->format('Y-m-d'),
                'time' => '14:30',
                'type' => 'Cardiology Check',
                'doctor' => 'Dr. Michael Chen',
                'department' => 'Cardiology',
                'location' => 'OPD Block B',
                'status' => 'confirmed',
                'notes' => 'Blood pressure monitoring and ECG',
            ],
            [
                'id' => 3,
                'date' => Carbon::now()->addDays(14)->format('Y-m-d'),
                'time' => '10:15',
                'type' => 'Blood Test Follow-up',
                'doctor' => 'Dr. Emily Davis',
                'department' => 'Laboratory',
                'location' => 'Lab Section 2',
                'status' => 'pending',
                'notes' => 'Complete Blood Count results review',
            ],
        ];

        return [
            'appointments' => $appointments,
            'next_appointment' => count($appointments) > 0 ? $appointments[0] : null,
            'total_appointments' => count($appointments),
        ];
    }

    private function getBillPayments($patientData): array
    {
        // Simulate billing and payment history
        $dueBills = [
            [
                'id' => 1,
                'description' => 'OPD Consultation - General Medicine',
                'amount' => 150.00,
                'due_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
                'days_overdue' => 0,
                'status' => 'unpaid',
            ],
            [
                'id' => 2,
                'description' => 'Laboratory Tests - Complete Blood Count',
                'amount' => 75.00,
                'due_date' => Carbon::now()->subDays(2)->format('Y-m-d'),
                'days_overdue' => 2,
                'status' => 'overdue',
            ],
        ];

        $recentPayments = [
            [
                'id' => 1,
                'description' => 'IPD Stay - Room Charges',
                'amount' => 500.00,
                'payment_date' => Carbon::now()->subDays(10)->format('Y-m-d'),
                'payment_method' => 'Credit Card',
                'status' => 'paid',
            ],
            [
                'id' => 2,
                'description' => 'OPD Consultation Fee',
                'amount' => 125.00,
                'payment_date' => Carbon::now()->subDays(20)->format('Y-m-d'),
                'payment_method' => 'Cash',
                'status' => 'paid',
            ],
            [
                'id' => 3,
                'description' => 'X-Ray (Chest)',
                'amount' => 200.00,
                'payment_date' => Carbon::now()->subDays(35)->format('Y-m-d'),
                'payment_method' => 'Online Payment',
                'status' => 'paid',
            ],
        ];

        $totalDue = array_sum(array_column($dueBills, 'amount'));
        $totalPaidThisYear = array_sum(array_column($recentPayments, 'amount'));

        return [
            'due_bills' => $dueBills,
            'paid_history' => $recentPayments,
            'total_due' => $totalDue,
            'total_paid_this_year' => $totalPaidThisYear,
            'overdue_count' => count(array_filter($dueBills, fn($bill) => $bill['days_overdue'] > 0)),
        ];
    }

    private function getPrescriptionsReports($patientData): array
    {
        // Simulate available prescriptions
        $prescriptions = [
            [
                'id' => 1,
                'doctor' => 'Dr. Sarah Johnson',
                'date' => Carbon::now()->subDays(3)->format('Y-m-d'),
                'medications' => [
                    ['name' => 'Lisinopril 10mg', 'dosage' => '1 tablet daily', 'days_supply' => 30, 'refills' => 3],
                    ['name' => 'Hydrochlorothiazide 25mg', 'dosage' => '1 tablet daily', 'days_supply' => 30, 'refills' => 3],
                ],
                'instructions' => 'Take medications with food. Monitor blood pressure regularly.',
                'is_collectible' => true,
            ],
            [
                'id' => 2,
                'doctor' => 'Dr. Michael Chen',
                'date' => Carbon::now()->subDays(15)->format('Y-m-d'),
                'medications' => [
                    ['name' => 'Omeprazole 20mg', 'dosage' => '1 capsule daily', 'days_supply' => 30, 'refills' => 2],
                ],
                'instructions' => 'Take on empty stomach, 30 minutes before breakfast.',
                'is_collectible' => false,
            ],
        ];

        // Simulate available reports
        $reports = [
            [
                'id' => 1,
                'type' => 'Laboratory Report',
                'test_name' => 'Complete Blood Count',
                'date' => Carbon::now()->subDays(5)->format('Y-m-d'),
                'doctor' => 'Dr. Emily Davis',
                'status' => 'available',
                'result_summary' => 'All parameters within normal range',
            ],
            [
                'id' => 2,
                'type' => 'Radiology Report',
                'test_name' => 'Chest X-Ray',
                'date' => Carbon::now()->subDays(12)->format('Y-m-d'),
                'doctor' => 'Dr. Robert Wilson',
                'status' => 'available',
                'result_summary' => 'Normal cardiac silhouette and lung fields',
            ],
            [
                'id' => 3,
                'type' => 'Consultation Report',
                'test_name' => 'Cardiology Consultation',
                'date' => Carbon::now()->subDays(20)->format('Y-m-d'),
                'doctor' => 'Dr. Michael Chen',
                'status' => 'pending_interpretation',
                'result_summary' => 'Further examination recommended',
            ],
        ];

        $collectibleCount = count(array_filter($prescriptions, fn($rx) => $rx['is_collectible']));
        $availableReportsCount = count(array_filter($reports, fn($report) => $report['status'] === 'available'));

        return [
            'prescriptions' => $prescriptions,
            'reports' => $reports,
            'collectible_prescriptions' => $collectibleCount,
            'available_reports' => $availableReportsCount,
        ];
    }

    private function getIpdAdmissionHistory($patientData): array
    {
        // Simulate IPD admission history
        $ipdHistory = [
            [
                'id' => 1,
                'admission_date' => Carbon::now()->subMonths(6)->format('Y-m-d'),
                'discharge_date' => Carbon::now()->subMonths(6)->addDays(5)->format('Y-m-d'),
                'department' => 'General Medicine',
                'doctor' => 'Dr. Sarah Johnson',
                'primary_diagnosis' => 'Pneumonia',
                'treatment' => 'Antibiotics, Oxygen therapy',
                'ward' => 'Ward A - Room 201',
                'length_of_stay' => 5,
                'outcome' => 'Recovered',
                'total_cost' => 2500.00,
                'insurance_coverage' => 1800.00,
                'patient_payment' => 700.00,
            ],
            [
                'id' => 2,
                'admission_date' => Carbon::now()->subMonths(3)->format('Y-m-d'),
                'discharge_date' => Carbon::now()->subMonths(3)->addDays(2)->format('Y-m-d'),
                'department' => 'Cardiology',
                'doctor' => 'Dr. Michael Chen',
                'primary_diagnosis' => 'Chest Pain Evaluation',
                'treatment' => 'ECG, Cardiac enzymes, Stress test',
                'ward' => 'Ward B - Room 105',
                'length_of_stay' => 2,
                'outcome' => 'Discharged with stable condition',
                'total_cost' => 1200.00,
                'insurance_coverage' => 800.00,
                'patient_payment' => 400.00,
            ],
            [
                'id' => 3,
                'admission_date' => Carbon::now()->subDays(45)->format('Y-m-d'),
                'discharge_date' => Carbon::now()->subDays(38)->format('Y-m-d'),
                'department' => 'Emergency Surgery',
                'doctor' => 'Dr. David Miller',
                'primary_diagnosis' => 'Appendicitis',
                'treatment' => 'Laparoscopic appendectomy',
                'ward' => 'Surgical Ward - Room 312',
                'length_of_stay' => 7,
                'outcome' => 'Surgical recovery',
                'total_cost' => 3800.00,
                'insurance_coverage' => 2800.00,
                'patient_payment' => 1000.00,
            ],
        ];

        $totalAdmissions = count($ipdHistory);
        $averageLengthOfStay = floor(array_sum(array_column($ipdHistory, 'length_of_stay')) / $totalAdmissions);

        return [
            'admissions' => $ipdHistory,
            'total_admissions' => $totalAdmissions,
            'total_cost' => array_sum(array_column($ipdHistory, 'total_cost')),
            'average_length_of_stay' => $averageLengthOfStay,
            'last_admission' => $totalAdmissions > 0 ? $ipdHistory[0] : null,
        ];
    }

    private function getNotifications(): array
    {
        // Simulate notifications and messages from hospital
        $notifications = [
            [
                'id' => 1,
                'type' => 'appointment_reminder',
                'title' => 'Upcoming Appointment Reminder',
                'message' => 'You have an appointment with Dr. Sarah Johnson on ' . Carbon::now()->addDays(3)->format('M d, Y') . ' at 9:00 AM.',
                'date' => Carbon::now()->subHours(2)->format('Y-m-d H:i'),
                'is_read' => false,
                'priority' => 'normal',
            ],
            [
                'id' => 2,
                'type' => 'prescription_ready',
                'title' => 'Prescription Ready for Collection',
                'message' => 'Your prescription from Dr. Michael Chen is ready for collection at the pharmacy.',
                'date' => Carbon::now()->subHours(5)->format('Y-m-d H:i'),
                'is_read' => false,
                'priority' => 'normal',
            ],
            [
                'id' => 3,
                'type' => 'test_results',
                'title' => 'Laboratory Results Available',
                'message' => 'Your Complete Blood Count results are now available in your dashboard.',
                'date' => Carbon::now()->subHours(8)->format('Y-m-d H:i'),
                'is_read' => true,
                'priority' => 'normal',
            ],
            [
                'id' => 4,
                'type' => 'payment_due',
                'title' => 'Payment Due Reminder',
                'message' => 'Please settle your outstanding bill of $225 within 5 days to avoid service disruption.',
                'date' => Carbon::now()->subDays(1)->format('Y-m-d H:i'),
                'is_read' => false,
                'priority' => 'high',
            ],
            [
                'id' => 5,
                'type' => 'tele_consultation',
                'title' => 'Tele-consultation Scheduled',
                'message' => 'Your tele-consultation with Dr. Emily Davis is scheduled for tomorrow at 2:30 PM.',
                'date' => Carbon::now()->subDays(1)->format('Y-m-d H:i'),
                'is_read' => true,
                'priority' => 'normal',
            ],
        ];

        $unreadCount = count(array_filter($notifications, fn($n) => $n['is_read'] === false));
        $urgentCount = count(array_filter($notifications, fn($n) => $n['priority'] === 'high' && $n['is_read'] === false));

        return [
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
            'urgent_count' => $urgentCount,
            'total_notifications' => count($notifications),
        ];
    }

    private function getTeleConsultationLinks($patientData): array
    {
        // Simulate tele-consultation links
        $consultations = [
            [
                'id' => 1,
                'session_title' => 'Cardiology Follow-up',
                'doctor' => 'Dr. Michael Chen',
                'scheduled_date' => Carbon::now()->addDays(2)->format('Y-m-d'),
                'scheduled_time' => '15:30',
                'duration' => 30,
                'link' => '#tele-consult-1', // In real system, this would be a secure meeting link
                'passcode' => 'TL-123456',
                'status' => 'scheduled',
                'special_notes' => 'Please have ECG reports ready for discussion.',
            ],
            [
                'id' => 2,
                'session_title' => 'General Health Check',
                'doctor' => 'Dr. Sarah Johnson',
                'scheduled_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
                'scheduled_time' => '11:00',
                'duration' => 20,
                'link' => '#tele-consult-2',
                'passcode' => 'TL-789012',
                'status' => 'scheduled',
                'special_notes' => 'Annual health assessment discussion.',
            ],
        ];

        $completedSessions = [
            [
                'id' => 3,
                'session_title' => 'Blood Pressure Review',
                'doctor' => 'Dr. Emily Davis',
                'completed_date' => Carbon::now()->subDays(7)->format('Y-m-d'),
                'completed_time' => '14:00',
                'duration' => 25,
                'status' => 'completed',
                'summary' => 'Discussed medication adjustments and home monitoring recommendations.',
            ],
        ];

        return [
            'upcoming_consultations' => $consultations,
            'completed_sessions' => $completedSessions,
            'next_consultation' => count($consultations) > 0 ? $consultations[0] : null,
            'total_sessions_this_month' => count($consultations) + count($completedSessions),
        ];
    }
}