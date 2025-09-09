<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

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

        // Hospital user, scoped data
        $data = [
            'user_count' => User::where('hospital_id', $user->hospital_id)->count(),
            'notifications' => [], // Placeholder for alerts
            'charts' => [], // Placeholder for revenue/data charts
            'bills' => 0, // Placeholder
            'advanced_payments' => 0, // Placeholder
            'available_beds' => 0, // Placeholder
            'patient_count' => User::where('hospital_id', $user->hospital_id)->count(),
        ];

        return view('hospital.dashboard', $data);
    }
}
