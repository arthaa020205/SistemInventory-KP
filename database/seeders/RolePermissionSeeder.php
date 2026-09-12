<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $owner = Role::findByName('Owner');
        $petugas = Role::findByName('Petugas');

        /*
        |--------------------------------------------------------------------------
        | OWNER
        |--------------------------------------------------------------------------
        */

        $owner->syncPermissions([

            'dashboard.view',

            // User
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',

            // Master Data (Read Only)
            'kategori.view',
            'supplier.view',
            'satuan.view',
            'pelanggan.view',
            'barang.view',

            // Transaksi (Read Only)
            'barang-masuk.view',
            'barang-keluar.view',
            'stock-opname.view',

            // Monitoring
            'monitoring.view',

            // Laporan
            'laporan.view',
            'laporan.export',

            // Permintaan Pengadaan
            'permintaan.view',
            'permintaan.approve',
            'permintaan.reject',

            // Activity Log
            'log.view',

        ]);

        /*
        |--------------------------------------------------------------------------
        | PETUGAS
        |--------------------------------------------------------------------------
        */

        $petugas->syncPermissions([

            'dashboard.view',

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

            // Permintaan Pengadaan
            'permintaan.view',
            'permintaan.create',
            'permintaan.edit',
            'permintaan.delete',

        ]);
    }
}