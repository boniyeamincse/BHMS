<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hospital;

class HospitalSettingsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $hospital = $user->hospital;

        $modules = $this->getModules();
        $settings = $hospital->settings ?? [];

        return view('hospital.settings', compact('modules', 'settings', 'hospital'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'modules' => 'nullable|array',
            'currency' => 'nullable|string',
            'timezone' => 'nullable|string',
        ]);

        $user = auth()->user();
        $hospital = $user->hospital;

        $settings = $request->only(['modules', 'currency', 'timezone']);
        $hospital->update(['settings' => $settings]);

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    private function getModules()
    {
        return [
            'appointments' => 'Appointments',
            'pharmacy' => 'Pharmacy',
            'blood_bank' => 'Blood Bank',
            'beds' => 'Beds Management',
            'billing' => 'Billing',
            'diagnostics' => 'Diagnostics',
            'communication' => 'Communication',
            'telehealth' => 'Telehealth',
            'inventory' => 'Inventory',
        ];
    }
}
