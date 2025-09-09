<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Hospital;
use App\Models\Role;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test hospital
        $hospital = Hospital::firstOrCreate(
            ['email' => 'info@testhospital.com'],
            [
                'name' => 'Test Hospital',
                'address' => '123 Test Street, Dhaka, Bangladesh',
                'phone' => '+880123456789',
                'status' => 'active',
                'logo' => null,
                'settings' => [
                    'currency' => 'BDT',
                    'timezone' => 'Asia/Dhaka',
                    'modules' => [
                        'appointments' => true,
                        'pharmacy' => true,
                        'blood_bank' => true,
                        'beds' => true,
                        'billing' => true,
                        'diagnostics' => true,
                        'communication' => true,
                        'telehealth' => true,
                        'inventory' => true,
                    ],
                ],
            ]
        );

        // Create Super Admin
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'hospital_id' => null, // Super Admin not scoped to hospital
            ]
        );

        // Create Hospital Admin
        $hospitalAdmin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Hospital Admin',
                'password' => Hash::make('password'),
                'hospital_id' => $hospital->id,
            ]
        );

        // Create Doctor
        $doctor = User::updateOrCreate(
            ['email' => 'doctor@example.com'],
            [
                'name' => 'Dr. John Doe',
                'password' => Hash::make('password'),
                'hospital_id' => $hospital->id,
            ]
        );

        // Create Patient
        $patient = User::updateOrCreate(
            ['email' => 'patient@example.com'],
            [
                'name' => 'Jane Smith',
                'password' => Hash::make('password'),
                'hospital_id' => $hospital->id,
            ]
        );

        // Get roles
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        $hospitalAdminRole = Role::where('name', 'Hospital Admin')->first();
        $doctorRole = Role::where('name', 'Doctor')->first();
        $patientRole = Role::where('name', 'Patient')->first();

        // Assign roles (only if not already assigned)
        if ($superAdminRole && !$superAdmin->roles()->where('role_id', $superAdminRole->id)->exists()) {
            $superAdmin->roles()->attach($superAdminRole, ['hospital_id' => null]);
        }

        if ($hospitalAdminRole && !$hospitalAdmin->roles()->where('role_id', $hospitalAdminRole->id)->wherePivot('hospital_id', $hospital->id)->exists()) {
            $hospitalAdmin->roles()->attach($hospitalAdminRole, ['hospital_id' => $hospital->id]);
        }

        if ($doctorRole && !$doctor->roles()->where('role_id', $doctorRole->id)->wherePivot('hospital_id', $hospital->id)->exists()) {
            $doctor->roles()->attach($doctorRole, ['hospital_id' => $hospital->id]);
        }

        if ($patientRole && !$patient->roles()->where('role_id', $patientRole->id)->wherePivot('hospital_id', $hospital->id)->exists()) {
            $patient->roles()->attach($patientRole, ['hospital_id' => $hospital->id]);
        }

        $this->command->info("Test data seeded:\n- Super Admin: superadmin@example.com\n- Hospital Admin: admin@example.com\n- Doctor: doctor@example.com\n- Patient: patient@example.com\n- Password for all: password\n- Hospital: Test Hospital");
    }
}
