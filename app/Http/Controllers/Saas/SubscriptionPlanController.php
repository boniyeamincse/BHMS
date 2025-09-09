<?php

namespace App\Http\Controllers\Saas;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::withCount('hospitals')->latest()->paginate(15);

        return view('saas.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('saas.plans.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:trial,paid',
            'monthly_price' => 'nullable|numeric|min:0',
            'yearly_price' => 'nullable|numeric|min:0',
            'features' => 'nullable|array',
            'limits' => 'nullable|array',
            'trial_days' => 'nullable|integer|min:0',
            'features.*' => 'string|max:255',
            'limits.user_limit' => 'nullable|integer|min:0',
            'limits.patient_limit' => 'nullable|integer|min:0',
            'limits.storage_limit' => 'nullable|integer|min:0',
        ]);

        $validatedData['status'] = 'active';

        SubscriptionPlan::create($validatedData);

        return redirect()->route('saas.plans.index')
            ->with('success', 'Subscription plan created successfully.');
    }

    public function edit(SubscriptionPlan $plan)
    {
        return view('saas.plans.edit', compact('plan'));
    }

    public function update(Request $request, SubscriptionPlan $plan)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:trial,paid',
            'monthly_price' => 'nullable|numeric|min:0',
            'yearly_price' => 'nullable|numeric|min:0',
            'features' => 'nullable|array',
            'limits' => 'nullable|array',
            'trial_days' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
            'sort_order' => 'nullable|integer|min:0',
            'features.*' => 'string|max:255',
            'limits.user_limit' => 'nullable|integer|min:0',
            'limits.patient_limit' => 'nullable|integer|min:0',
            'limits.storage_limit' => 'nullable|integer|min:0',
        ]);

        $plan->update($validatedData);

        return redirect()->route('saas.plans.index')
            ->with('success', 'Subscription plan updated successfully.');
    }

    public function destroy(SubscriptionPlan $plan)
    {
        // Check if plan is being used by any hospitals
        if ($plan->hospitals()->count() > 0) {
            return redirect()->route('saas.plans.index')
                ->with('error', 'Cannot delete subscription plan because it is being used by hospitals.');
        }

        $plan->delete();

        return redirect()->route('saas.plans.index')
            ->with('success', 'Subscription plan deleted successfully.');
    }
}