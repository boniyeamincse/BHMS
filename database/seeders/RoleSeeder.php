<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Super Admin', 'description' => 'SaaS owner is his master'],
            ['name' => 'Hospital Admin', 'description' => 'Manages one hospital'],
            ['name' => 'Doctor', 'description' => 'Manages appointments, prescriptions, cases'],
            ['name' => 'Accountant', 'description' => 'Handles billing, payments, payroll'],
            ['name' => 'Case Handler', 'description' => 'Patient cases and admissions, ambulance'],
            ['name' => 'Receptionist', 'description' => 'Appointments, patient registration, front office'],
            ['name' => 'Pharmacist', 'description' => 'Medicines, pharmacy bills'],
            ['name' => 'Nurse', 'description' => 'Bed assignments, patient care, blood bank'],
            ['name' => 'Lab Technician', 'description' => 'Pathology, radiology, diagnosis tests'],
            ['name' => 'Patient', 'description' => 'Books appointments, views bills, prescriptions'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}
