<?php

namespace App\Http\Controllers\Saas;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class HospitalController extends Controller
{
    public function index(Request $request)
    {
        $query = Hospital::with(['hospitalType', 'subscriptionPlan'])->latest();

        // Apply filters
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('payment_status') && $request->payment_status !== '') {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $hospitals = $query->paginate(15);

        return view('saas.hospitals.index', compact('hospitals'));
    }

    public function edit(Hospital $hospital)
    {
        return view('saas.hospitals.edit', compact('hospital'));
    }

    public function update(Request $request, Hospital $hospital)
    {
        $validatedData = $request->validate([
            'status' => 'required|in:active,inactive',
            'payment_status' => 'required|in:trial,active,expired,free',
        ]);

        $hospital->update($validatedData);

        return redirect()->route('saas.hospitals.index')
            ->with('success', 'Hospital updated successfully.');
    }

    public function impersonate(Hospital $hospital)
    {
        // Store current super admin context
        Session::put('original_super_admin_id', Auth::id());

        // Get first user of the hospital to impersonate
        $userToImpersonate = $hospital->users()->first();

        if (!$userToImpersonate) {
            return redirect()->back()->with('error', 'Hospital has no users to impersonate.');
        }

        // Log the super admin out
        Auth::logout();

        // Log in as the hospital user
        Auth::login($userToImpersonate);

        // Store the impersonation flag and redirect to hospital dashboard
        Session::put('impersonating_hospital_id', $hospital->id);

        return redirect()->route('hospital.dashboard')
            ->with('success', 'Now impersonating a user from ' . $hospital->name . '. Click the button below to stop impersonation.');
    }

    public static function stopImpersonation()
    {
        if (Session::has('original_super_admin_id')) {
            $originalAdminId = Session::get('original_super_admin_id');

            // Log out current user
            Auth::logout();

            // Log back in as original super admin
            $originalAdmin = \App\Models\User::find($originalAdminId);
            if ($originalAdmin) {
                Auth::login($originalAdmin);
            }

            // Clear impersonation session data
            Session::forget(['original_super_admin_id', 'impersonating_hospital_id']);

            return redirect()->route('saas.dashboard')
                ->with('success', 'Impersonation stopped. Welcome back!');
        }

        return redirect()->route('hospital.dashboard');
    }
}