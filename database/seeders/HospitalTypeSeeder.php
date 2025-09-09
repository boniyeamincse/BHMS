<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HospitalTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hospitalTypes = [
            [
                'name' => 'General Hospital',
                'description' => 'Comprehensive healthcare services for all medical needs',
                'features' => [
                    'Emergency Services',
                    'Surgery Department',
                    'Intensive Care Unit',
                    'Maternity Ward',
                    'Pediatric Care'
                ],
                'status' => 'active',
            ]
        ];

        foreach ($hospitalTypes as $type) {
            \App\Models\HospitalType::create($type);
        }
    }
}
