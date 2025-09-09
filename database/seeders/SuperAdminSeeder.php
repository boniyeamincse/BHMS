<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if Super Admin role exists, create if not
        $superAdminRole = Role::where('name', 'Super Admin')->first();

        if (!$superAdminRole) {
            $superAdminRole = Role::create([
                'name' => 'Super Admin',
                'description' => 'Full system access and administration',
            ]);
        }

        // Check if super admin user exists
        $superAdmin = User::where('email', 'superadmin@bhms.com')->first();

        if (!$superAdmin) {
            $superAdmin = User::create([
                'name' => 'BHMS Super Admin',
                'email' => 'superadmin@bhms.com',
                'password' => Hash::make('BHMS@2025!super#'),
                'email_verified_at' => now(),
            ]);

            // Attach Super Admin role
            $superAdmin->roles()->attach($superAdminRole);
        }

        $this->command->info('Super Admin user created/updated:');
        $this->command->info('Email: superadmin@bhms.com');
        $this->command->info('Password: BHMS@2025!super#');
        $this->command->warn('WARNING: Change this password immediately after first login!');
    }
}
