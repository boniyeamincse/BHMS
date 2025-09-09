<?php

namespace App\Http\Controllers\Saas;

use App\Http\Controllers\Controller;
use App\Models\HospitalType;
use Illuminate\Http\Request;

class HospitalTypeController extends Controller
{
    public function index()
    {
        $hospitalTypes = HospitalType::withCount('hospitals')->latest()->paginate(15);

        return view('saas.hospital-types.index', compact('hospitalTypes'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        HospitalType::create($validatedData);

        return redirect()->route('saas.hospital-types.index')
            ->with('success', 'Hospital type created successfully.');
    }

    public function edit(HospitalType $hospitalType)
    {
        return view('saas.hospital-types.edit', compact('hospitalType'));
    }

    public function update(Request $request, HospitalType $hospitalType)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $hospitalType->update($validatedData);

        return redirect()->route('saas.hospital-types.index')
            ->with('success', 'Hospital type updated successfully.');
    }

    public function destroy(HospitalType $hospitalType)
    {
        // Check if hospital type is being used by any hospitals
        if ($hospitalType->hospitals()->count() > 0) {
            return redirect()->route('saas.hospital-types.index')
                ->with('error', 'Cannot delete hospital type because it is being used by hospitals.');
        }

        $hospitalType->delete();

        return redirect()->route('saas.hospital-types.index')
            ->with('success', 'Hospital type deleted successfully.');
    }
}