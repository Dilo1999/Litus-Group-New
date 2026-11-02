<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Seed the super admin user for the CRM (Filament).
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'dev@litusmaldives.com'],
            [
                'name' => 'Super Admin',
                'password' => 'admingroup99@',
                'role' => User::ROLE_SUPERADMIN,
            ]
        );
    }
}
