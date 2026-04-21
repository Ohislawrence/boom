<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles (idempotent)
        Role::firstOrCreate(['name' => 'admin',    'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'tipster',  'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'bettor',   'guard_name' => 'web']);

        // Promote the first registered user to admin
        $firstUser = \App\Models\User::first();
        if ($firstUser && ! $firstUser->hasRole('admin')) {
            $firstUser->assignRole('admin');
            $this->command->info("Promoted {$firstUser->email} to admin.");
        }

        $this->command->info('Roles created: admin, tipster, bettor');
    }
}
