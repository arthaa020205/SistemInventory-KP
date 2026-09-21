<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\PermintaanController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LogAktivitasController;
use App\Http\Controllers\KonversiSatuanController;

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});


/*
|--------------------------------------------------------------------------
| AUTHENTICATED
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:dashboard.view')
        ->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | PROFILE
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');


    /*
    |--------------------------------------------------------------------------
    | MASTER USER
    | Owner = CRUD
    |--------------------------------------------------------------------------
    */

    Route::resource('users', UserController::class)
        ->middlewareFor(
            ['index', 'show', 'create', 'store', 'edit', 'update', 'destroy'],
            'permission:user.view'
        );


    /*
    |--------------------------------------------------------------------------
    | MASTER DATA
    |
    | Owner   = Read Only
    | Petugas = CRUD
    |--------------------------------------------------------------------------
    */

    // Kategori
    Route::resource('kategori', KategoriController::class)
        ->middlewareFor(
            ['index', 'show'],
            'permission:kategori.view'
        )
        ->middlewareFor(
            ['create', 'store'],
            'permission:kategori.create'
        )
        ->middlewareFor(
            ['edit', 'update'],
            'permission:kategori.edit'
        )
        ->middlewareFor(
            ['destroy'],
            'permission:kategori.delete'
        );


    // Supplier
    Route::resource('supplier', SupplierController::class)
        ->middlewareFor(
            ['index', 'show'],
            'permission:supplier.view'
        )
        ->middlewareFor(
            ['create', 'store'],
            'permission:supplier.create'
        )
        ->middlewareFor(
            ['edit', 'update'],
            'permission:supplier.edit'
        )
        ->middlewareFor(
            ['destroy'],
            'permission:supplier.delete'
        );


    // Satuan
    Route::resource('satuan', SatuanController::class)
        ->middlewareFor(
            ['index', 'show'],
            'permission:satuan.view'
        )
        ->middlewareFor(
            ['create', 'store'],
            'permission:satuan.create'
        )
        ->middlewareFor(
            ['edit', 'update'],
            'permission:satuan.edit'
        )
        ->middlewareFor(
            ['destroy'],
            'permission:satuan.delete'
        );


    // Barang
    Route::resource('barang', BarangController::class)
        ->middlewareFor(
            ['index', 'show'],
            'permission:barang.view'
        )
        ->middlewareFor(
            ['create', 'store'],
            'permission:barang.create'
        )
        ->middlewareFor(
            ['edit', 'update'],
            'permission:barang.edit'
        )
        ->middlewareFor(
            ['destroy'],
            'permission:barang.delete'
        );

            Route::post(
                '/barang/{barang}/konversi',
                [KonversiSatuanController::class, 'store']
            )->name('barang.konversi.store');

            Route::delete(
                '/barang/{barang}/konversi/{konversiSatuan}',
                [KonversiSatuanController::class, 'destroy']
            )->name('barang.konversi.destroy');

    // Pelanggan
    Route::resource('pelanggan', PelangganController::class)
        ->middlewareFor(
            ['index', 'show'],
            'permission:pelanggan.view'
        )
        ->middlewareFor(
            ['create', 'store'],
            'permission:pelanggan.create'
        )
        ->middlewareFor(
            ['edit', 'update'],
            'permission:pelanggan.edit'
        )
        ->middlewareFor(
            ['destroy'],
            'permission:pelanggan.delete'
        );


    /*
    |--------------------------------------------------------------------------
    | TRANSAKSI
    |
    | Owner   = Read Only
    | Petugas = CRUD
    |--------------------------------------------------------------------------
    */

    // Barang Masuk
    Route::resource('barang-masuk', BarangMasukController::class)
        ->middlewareFor(
            ['index', 'show'],
            'permission:barang-masuk.view'
        )
        ->middlewareFor(
            ['create', 'store'],
            'permission:barang-masuk.create'
        )
        ->middlewareFor(
            ['edit', 'update'],
            'permission:barang-masuk.edit'
        )
        ->middlewareFor(
            ['destroy'],
            'permission:barang-masuk.delete'
        );


    // Barang Keluar
    Route::resource('barang-keluar', BarangKeluarController::class)
        ->middlewareFor(
            ['index', 'show'],
            'permission:barang-keluar.view'
        )
        ->middlewareFor(
            ['create', 'store'],
            'permission:barang-keluar.create'
        )
        ->middlewareFor(
            ['edit', 'update'],
            'permission:barang-keluar.edit'
        )
        ->middlewareFor(
            ['destroy'],
            'permission:barang-keluar.delete'
        );


    // Invoice Penjualan
    Route::get('/barang-keluar/{barangKeluar}/invoice', [
        \App\Http\Controllers\BarangKeluarController::class,
        'invoice'
    ])->name('barang-keluar.invoice');

    Route::get('/barang-keluar/{barangKeluar}/surat-jalan', [
        \App\Http\Controllers\BarangKeluarController::class,
        'suratJalan'
    ])->name('barang-keluar.surat-jalan');


    // Stock Opname
    Route::resource('stock-opname', StockOpnameController::class)
        ->middlewareFor(
            ['index', 'show'],
            'permission:stock-opname.view'
        )
        ->middlewareFor(
            ['create', 'store'],
            'permission:stock-opname.create'
        )
        ->middlewareFor(
            ['edit', 'update'],
            'permission:stock-opname.edit'
        )
        ->middlewareFor(
            ['destroy'],
            'permission:stock-opname.delete'
        );


    /*
    |--------------------------------------------------------------------------
    | PERMINTAAN PENGADAAN
    |
    | Owner   = View + Approve + Reject
    | Petugas = View + Create
    |--------------------------------------------------------------------------
    */

    Route::resource('permintaan', PermintaanController::class)
        ->middlewareFor(
            ['index', 'show'],
            'permission:permintaan.view'
        )
        ->middlewareFor(
            ['create', 'store'],
            'permission:permintaan.create'
        );

    Route::get(
        '/permintaan/{permintaan}/approval',
        [PermintaanController::class, 'approval']
    )
        ->middleware('permission:permintaan.approve')
        ->name('permintaan.approval');

    Route::put(
        '/permintaan/{permintaan}/setujui',
        [PermintaanController::class, 'setujui']
    )
        ->middleware('permission:permintaan.approve')
        ->name('permintaan.setujui');

    Route::put(
        '/permintaan/{permintaan}/tolak',
        [PermintaanController::class, 'tolak']
    )
        ->middleware('permission:permintaan.reject')
        ->name('permintaan.tolak');


    /*
    |--------------------------------------------------------------------------
    | MONITORING
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/monitoring',
        [MonitoringController::class, 'index']
    )
        ->middleware('permission:monitoring.view')
        ->name('monitoring.index');

    Route::get(
        '/monitoring/chart',
        [MonitoringController::class, 'chart']
    )
        ->middleware('permission:monitoring.view')
        ->name('monitoring.chart');


    /*
    |--------------------------------------------------------------------------
    | LAPORAN
    |--------------------------------------------------------------------------
    */

    Route::prefix('laporan')
        ->middleware('permission:laporan.view')
        ->group(function () {

            Route::get(
                '/',
                [LaporanController::class, 'index']
            )->name('laporan.index');

            
            // Stok
            Route::get(
                '/stok',
                [LaporanController::class, 'stok']
            )->name('laporan.stok');

            Route::get(
                '/stok/pdf',
                [LaporanController::class, 'stokPdf']
            )->name('laporan.stok.pdf');

            Route::get(
                '/stok/excel',
                [LaporanController::class, 'stokExcel']
            )->name('laporan.stok.excel');

            // Barang Masuk
            Route::get(
                '/barang-masuk',
                [LaporanController::class, 'barangMasuk']
            )->name('laporan.barang-masuk');

            Route::get(
                '/barang-masuk/pdf',
                [LaporanController::class, 'barangMasukPdf']
            )->name('laporan.barang-masuk.pdf');

            Route::get(
                '/barang-masuk/excel',
                [LaporanController::class, 'barangMasukExcel']
            )->name('laporan.barang-masuk.excel');


            // Barang Keluar
            Route::get(
                '/barang-keluar',
                [LaporanController::class, 'barangKeluar']
            )->name('laporan.barang-keluar');

            Route::get(
                '/barang-keluar/pdf',
                [LaporanController::class, 'barangKeluarPdf']
            )->name('laporan.barang-keluar.pdf');

            Route::get(
                '/barang-keluar/excel',
                [LaporanController::class, 'barangKeluarExcel']
            )->name('laporan.barang-keluar.excel');


            // Stock Opname
            Route::get(
                '/stock-opname',
                [LaporanController::class, 'stockOpname']
            )->name('laporan.stock-opname');

            Route::get(
                '/stock-opname/pdf',
                [LaporanController::class, 'stockOpnamePdf']
            )->name('laporan.stock-opname.pdf');

            Route::get(
                '/stock-opname/excel',
                [LaporanController::class, 'stockOpnameExcel']
            )->name('laporan.stock-opname.excel');

        });


    /*
    |--------------------------------------------------------------------------
    | ACTIVITY LOG
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/log',
        [LogAktivitasController::class, 'index']
    )
        ->middleware('permission:log.view')
        ->name('log.index');

});

Route::get('/barang/cari-qr/{kode}', [
    \App\Http\Controllers\BarangController::class,
    'cariByQr'
])->name('barang.cari-qr');


/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';