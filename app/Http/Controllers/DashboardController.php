<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Supplier;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\StockOpname;
use App\Models\PermintaanPengadaan;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | MASTER DATA
        |--------------------------------------------------------------------------
        */

        $totalBarang = Barang::count();
        $totalKategori = Kategori::count();
        $totalSupplier = Supplier::count();
        $totalUser = User::count();


        /*
        |--------------------------------------------------------------------------
        | KONDISI STOK
        |--------------------------------------------------------------------------
        */

        $totalStok = Barang::sum('stok');

        $stokHabis = Barang::where('stok', '<=', 0)
            ->count();

        $stokMenipis = Barang::where('stok', '>', 0)
            ->whereColumn('stok', '<=', 'stok_minimum')
            ->count();

        $stokAman = Barang::whereColumn('stok', '>', 'stok_minimum')
            ->count();

        $barangMendekatiEd = BarangMasuk::with('barang')
            ->whereNotNull('expired_date')
            ->whereHas('barang', function ($query) {
                $query->where('stok', '>', 0);
            })
            ->whereDate('expired_date', '>=', today())
            ->whereDate('expired_date', '<=', now()->addDays(30))
            ->orderBy('expired_date')
            ->take(5)
            ->get();
        /*
        |--------------------------------------------------------------------------
        | AKTIVITAS INVENTORY
        |--------------------------------------------------------------------------
        */

        $barangMasuk = BarangMasuk::count();

        $barangKeluar = BarangKeluar::count();

        $totalStockOpname = StockOpname::count();

        $permintaanPending = PermintaanPengadaan::where(
            'status',
            'Menunggu'
        )->count();


        /*
        |--------------------------------------------------------------------------
        | BARANG HAMPIR HABIS
        |--------------------------------------------------------------------------
        */

        $barangHampirHabis = Barang::where('stok', '>', 0)
            ->whereColumn('stok', '<=', 'stok_minimum')
            ->orderBy('stok')
            ->take(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | BARANG STOK TERBANYAK
        |--------------------------------------------------------------------------
        */

        $barangStokTerbanyak = Barang::orderByDesc('stok')
            ->take(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | PERMINTAAN PENGADAAN TERBARU
        |--------------------------------------------------------------------------
        */

        $permintaanTerbaru = PermintaanPengadaan::with([
            'barang',
            'user'
        ])
            ->latest('tanggal_permintaan')
            ->take(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | BARANG MASUK TERBARU
        |--------------------------------------------------------------------------
        */

        $barangMasukTerbaru = BarangMasuk::with([
            'barang',
            'supplier'
        ])
            ->latest('tanggal_masuk')
            ->take(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | BARANG KELUAR TERBARU
        |--------------------------------------------------------------------------
        */

        $barangKeluarTerbaru = BarangKeluar::with([
            'barang',
            'user'
        ])
            ->latest('tanggal_keluar')
            ->take(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | STOCK OPNAME TERBARU
        |--------------------------------------------------------------------------
        */

        $stockOpnameTerbaru = StockOpname::with([
            'barang',
            'user'
        ])
            ->latest('tanggal_opname')
            ->take(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | GRAFIK BARANG MASUK VS BARANG KELUAR
        |--------------------------------------------------------------------------
        */

        $grafikMasuk = [];
        $grafikKeluar = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {

            $grafikMasuk[] = BarangMasuk::whereYear(
                'tanggal_masuk',
                now()->year
            )
                ->whereMonth(
                    'tanggal_masuk',
                    $bulan
                )
                ->count();


            $grafikKeluar[] = BarangKeluar::whereYear(
                'tanggal_keluar',
                now()->year
            )
                ->whereMonth(
                    'tanggal_keluar',
                    $bulan
                )
                ->count();
        }


        /*
        |--------------------------------------------------------------------------
        | RETURN DASHBOARD
        |--------------------------------------------------------------------------
        */

        return view('dashboard', compact(

            // Master
            'totalBarang',
            'totalKategori',
            'totalSupplier',
            'totalUser',

            // Kondisi stok
            'totalStok',
            'stokHabis',
            'stokMenipis',
            'stokAman',

            // Aktivitas inventory
            'barangMasuk',
            'barangKeluar',
            'totalStockOpname',
            'permintaanPending',

            // Monitoring
            'barangHampirHabis',
            'barangStokTerbanyak',

            // Permintaan
            'permintaanTerbaru',

            // Aktivitas terbaru
            'barangMasukTerbaru',
            'barangKeluarTerbaru',
            'stockOpnameTerbaru',

            // Grafik
            'grafikMasuk',
            'grafikKeluar',

            'barangMendekatiEd'
        ));
    }
}