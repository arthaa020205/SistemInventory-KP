<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================
        // OWNER
        // ==========================
        $owner = User::firstOrCreate(
            [
                'email' => 'owner@khanzaplastik.id',
            ],
            [
                'name' => 'Owner',
                'password' => Hash::make('password'),
                'status' => 'Aktif',
            ]
        );

        $owner->syncRoles(['Owner']);

        // ==========================
        // PETUGAS GUDANG
        // ==========================
        $petugas = User::firstOrCreate(
            [
                'email' => 'gudang@khanzaplastik.id',
            ],
            [
                'name' => 'Petugas Gudang',
                'password' => Hash::make('password'),
                'status' => 'Aktif',
            ]
        );

        $petugas->syncRoles(['Petugas']);
    }
}