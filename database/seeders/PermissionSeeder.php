<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [

            // Dashboard
            'dashboard.view',

            // User
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',

            // Kategori
            'kategori.view',
            'kategori.create',
            'kategori.edit',
            'kategori.delete',

            // Supplier
            'supplier.view',
            'supplier.create',
            'supplier.edit',
            'supplier.delete',

            // Satuan
            'satuan.view',
            'satuan.create',
            'satuan.edit',
            'satuan.delete',

            // Pelanggan
            'pelanggan.view',
            'pelanggan.create',
            'pelanggan.edit',
            'pelanggan.delete',

            // Barang
            'barang.view',
            'barang.create',
            'barang.edit',
            'barang.delete',

            // Barang Masuk
            'barang-masuk.view',
            'barang-masuk.create',
            'barang-masuk.edit',
            'barang-masuk.delete',

            // Barang Keluar
            'barang-keluar.view',
            'barang-keluar.create',
            'barang-keluar.edit',
            'barang-keluar.delete',

            // Stock Opname
            'stock-opname.view',
            'stock-opname.create',
            'stock-opname.edit',
            'stock-opname.delete',

            // Monitoring
            'monitoring.view',

            // Laporan
            'laporan.view',
            'laporan.export',

            // Permintaan Pengadaan
            'permintaan.view',
            'permintaan.create',
            'permintaan.edit',
            'permintaan.delete',
            'permintaan.approve',
            'permintaan.reject',

            // Activity Log
            'log.view',

        ];

        foreach ($permissions as $permission) {

            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);

        }
    }
}