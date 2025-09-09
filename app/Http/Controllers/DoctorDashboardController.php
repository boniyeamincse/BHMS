<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Patient;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Ward;
use App\Models\Bed;
use App\Models\Announcement;
use App\Models\Role;
use Carbon\Carbon;

class DoctorDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $hospitalId = $user->hospital_id;

        // Today's appointments
        $todayAppointments = $this->getTodayAppointments($user->id, $hospitalId);

        // Patient cases assigned
        $assignedPatients = $this->getAssignedPatients($user->id, $hospitalId);

        // Upcoming schedules/availability (placeholder for now)
        $upcomingSchedules = $this->getUpcomingSchedules($user->id, $hospitalId);

        // Pending prescriptions
        $pendingPrescriptions = $this->getPendingPrescriptions($user->id, $hospitalId);

        // OPD/IPD patients under care
        $opdIpdPatients = $this->getOpdIpdPatients($user->id, $hospitalId);

        // Tele-consultations scheduled
        $teleConsultations = $this->getTeleConsultations($user->id, $hospitalId);

        $data = [
            'today_appointments' => $todayAppointments,
            'assigned_patients' => $assignedPatients,
            'upcoming_schedules' => $upcomingSchedules,
            'pending_prescriptions' => $pendingPrescriptions,
            'opd_ipd_patients' => $opdIpdPatients,
            'tele_consultations' => $teleConsultations,
        ];

        return view('doctor.dashboard', $data);
    }

    private function getTodayAppointments($doctorId, $hospitalId): array
    {
        // Since no appointment model, using patients with admission_date today as appointments
        $appointments = Patient::where('hospital_id', $hospitalId)
            ->whereDate('admission_date', today())
            ->where('status', 'active')
            ->select('id', 'name', 'type', 'admission_date', 'notes')
            ->get()
            ->map(function ($patient) {
                return [
                    'patient_name' => $patient->name,
                    'type' => $patient->type,
                    'time' => $patient->admission_date ? $patient->admission_date->format('H:i') : 'N/A',
                    'notes' => $patient->notes,
                ];
            });

        return $appointments->toArray();
    }

    private function getAssignedPatients($doctorId, $hospitalId): array
    {
        // Patients assigned to this doctor - assuming via notes or we can add a field later
        $patients = Patient::where('hospital_id', $hospitalId)
            ->where('status', 'active')
            ->select('id', 'name', 'type', 'admission_date', 'status')
            ->limit(10)
            ->get()
            ->map(function ($patient) {
                return [
                    'id' => $patient->id,
                    'name' => $patient->name,
                    'type' => $patient->type,
                    'admission_date' => $patient->admission_date ? $patient->admission_date->format('M d, Y') : 'N/A',
                    'status' => $patient->status,
                ];
            });

        return $patients->toArray();
    }

    private function getUpcomingSchedules($doctorId, $hospitalId): array
    {
        // Placeholder - upcoming schedules
        // We can use future admission dates as schedules
        $schedules = Patient::where('hospital_id', $hospitalId)
            ->whereDate('admission_date', '>', today())
            ->where('status', 'active')
            ->select('id', 'name', 'admission_date', 'type')
            ->orderBy('admission_date')
            ->limit(5)
            ->get()
            ->map(function ($patient) {
                return [
                    'patient_name' => $patient->name,
                    'date' => $patient->admission_date->format('M d, Y'),
                    'type' => $patient->type,
                ];
            });

        return $schedules->toArray();
    }

    private function getPendingPrescriptions($doctorId, $hospitalId): array
    {
        // Using invoices as prescriptions (approximation)
        $prescriptions = Invoice::where('hospital_id', $hospitalId)
            ->whereHas('patient', function ($query) {
                $query->where('type', 'OPD'); // Assuming OPD has prescriptions
            })
            ->whereDoesntHave('payments', function ($query) {
                $query->where('payment_date', '<=', now());
            })
            ->select('id', 'patient_id', 'amount', 'created_at')
            ->with('patient:id,name')
            ->limit(10)
            ->get()
            ->map(function ($invoice) {
                return [
                    'patient_name' => $invoice->patient->name,
                    'amount' => $invoice->amount,
                    'created_date' => $invoice->created_at->format('M d, Y'),
                ];
            });

        return $prescriptions->toArray();
    }

    private function getOpdIpdPatients($doctorId, $hospitalId): array
    {
        $opd = Patient::where('hospital_id', $hospitalId)
            ->where('type', 'OPD')
            ->where('status', 'active')
            ->count();

        $ipd = Patient::where('hospital_id', $hospitalId)
            ->where('type', 'IPD')
            ->where('status', 'active')
            ->count();

        return [
            'opd' => $opd,
            'ipd' => $ipd,
        ];
    }

    private function getTeleConsultations($doctorId, $hospitalId): array
    {
        // Placeholder - using patients with notes containing 'tele' or something
        $tele = Patient::where('hospital_id', $hospitalId)
            ->whereDate('admission_date', '>=', today())
            ->where('notes', 'like', '%tele%')
            ->select('id', 'name', 'admission_date', 'notes')
            ->get()
            ->map(function ($patient) {
                return [
                    'patient_name' => $patient->name,
                    'date' => $patient->admission_date ? $patient->admission_date->format('M d, Y') : 'N/A',
                    'notes' => $patient->notes,
                ];
            });

        return $tele->toArray();
    }
}