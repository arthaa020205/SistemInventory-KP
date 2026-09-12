<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate([
            'name' => 'Owner',
            'guard_name' => 'web',
        ]);

        Role::firstOrCreate([
            'name' => 'Petugas',
            'guard_name' => 'web',
        ]);
    }
}