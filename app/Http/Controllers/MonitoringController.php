<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Supplier;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\StockOpname;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    /**
     * ============================================================
     * DASHBOARD MONITORING
     * ============================================================
     */
    public function index(Request $request)
    {
        $periode = $request->get('periode', 'minggu');

        /*
        |--------------------------------------------------------------------------
        | PERIODE MONITORING
        |--------------------------------------------------------------------------
        */

        switch ($periode) {

            case 'hari':

                $start = now()->startOfDay();
                $end = now()->endOfDay();

                break;

            case 'bulan':

                $start = now()->startOfMonth();
                $end = now()->endOfMonth();

                break;

            case 'tahun':

                $start = now()->startOfYear();
                $end = now()->endOfYear();

                break;

            default:

                /*
                | 7 hari terakhir
                */
                $start = now()->subDays(6)->startOfDay();
                $end = now()->endOfDay();

                break;
        }


        /*
        |--------------------------------------------------------------------------
        | RINGKASAN INVENTORY
        |--------------------------------------------------------------------------
        */

        $totalBarang = Barang::count();

        $totalSupplier = Supplier::count();

        $totalStok = Barang::sum('stok');


        /*
        |--------------------------------------------------------------------------
        | KONDISI STOK
        |--------------------------------------------------------------------------
        */

        // Stok habis
        $stokHabis = Barang::where('stok', '<=', 0)
            ->count();


        // Stok menipis berdasarkan stok minimum masing-masing barang
        $stokMenipis = Barang::where('stok', '>', 0)
            ->whereColumn('stok', '<=', 'stok_minimum')
            ->count();


        // Stok aman
        $stokAman = Barang::where('stok', '>', 0)
            ->whereColumn('stok', '>', 'stok_minimum')
            ->count();


        /*
        |--------------------------------------------------------------------------
        | STOK BERLEBIH
        |--------------------------------------------------------------------------
        |
        | Overstock ditentukan berdasarkan stok yang lebih dari
        | 3x stok minimum.
        |
        */

        $stokBerlebih = Barang::where('stok_minimum', '>', 0)
            ->whereColumn(
                'stok',
                '>=',
                DB::raw('stok_minimum * 3')
            )
            ->count();


        /*
        |--------------------------------------------------------------------------
        | TRANSAKSI BERDASARKAN PERIODE
        |--------------------------------------------------------------------------
        */

        $barangMasukPeriode = BarangMasuk::whereBetween(
            'tanggal_masuk',
            [$start, $end]
        )->sum('jumlah');


        $barangKeluarPeriode = BarangKeluar::whereBetween(
            'tanggal_keluar',
            [$start, $end]
        )->sum('jumlah');


        /*
        |--------------------------------------------------------------------------
        | AKTIVITAS HARI INI
        |--------------------------------------------------------------------------
        |
        | Tetap menggunakan nama variabel lama agar Blade lama
        | juga tetap kompatibel.
        |
        */

        $barangMasukHariIni = BarangMasuk::whereDate(
            'tanggal_masuk',
            today()
        )->sum('jumlah');


        $barangKeluarHariIni = BarangKeluar::whereDate(
            'tanggal_keluar',
            today()
        )->sum('jumlah');


        /*
        |--------------------------------------------------------------------------
        | STOCK OPNAME
        |--------------------------------------------------------------------------
        */

        // Stock opname pada periode yang dipilih
        $stockOpnamePeriode = StockOpname::whereBetween(
            'tanggal_opname',
            [$start, $end]
        )->count();


        // Stock opname bulan berjalan
        $stockOpnameBulanIni = StockOpname::whereBetween(
            'tanggal_opname',
            [
                now()->startOfMonth(),
                now()->endOfMonth()
            ]
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
        | BARANG HAMPIR KADALUARSA
        |--------------------------------------------------------------------------
        |
        | Barang yang memiliki tanggal kadaluarsa mulai hari ini
        | sampai dengan 30 hari ke depan.
        |
        | Menggunakan barang_id yang unik agar satu barang dengan
        | beberapa transaksi barang masuk tidak dihitung berkali-kali.
        |
        */

        $barangHampirKadaluarsa = BarangMasuk::whereNotNull('expired_date')
            ->whereDate('expired_date', '>=', today())
            ->whereDate('expired_date', '<=', now()->addDays(30))
            ->distinct()
            ->count('barang_id');


        /*
        |--------------------------------------------------------------------------
        | BARANG HABIS
        |--------------------------------------------------------------------------
        */

        $barangHabis = Barang::where('stok', '<=', 0)
            ->orderBy('nama_barang')
            ->take(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | BARANG STOK BERLEBIH
        |--------------------------------------------------------------------------
        */

        $barangStokBerlebih = Barang::where('stok_minimum', '>', 0)
            ->whereColumn(
                'stok',
                '>=',
                DB::raw('stok_minimum * 3')
            )
            ->orderByDesc('stok')
            ->take(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | AKTIVITAS TERBARU
        |--------------------------------------------------------------------------
        */

        $aktivitasTerbaru = collect()

            ->merge(

                BarangMasuk::with('barang')
                    ->whereBetween(
                        'tanggal_masuk',
                        [$start, $end]
                    )
                    ->latest('tanggal_masuk')
                    ->take(5)
                    ->get()
                    ->map(function ($item) {

                        return [

                            'tanggal' => $item->tanggal_masuk,

                            'kode' => $item->kode_transaksi,

                            'barang' => optional(
                                $item->barang
                            )->nama_barang,

                            'jenis' => 'Masuk',

                            'badge' => 'success'

                        ];

                    })

            )

            ->merge(

                BarangKeluar::with('barang')
                    ->whereBetween(
                        'tanggal_keluar',
                        [$start, $end]
                    )
                    ->latest('tanggal_keluar')
                    ->take(5)
                    ->get()
                    ->map(function ($item) {

                        return [

                            'tanggal' => $item->tanggal_keluar,

                            'kode' => $item->kode_transaksi,

                            'barang' => optional(
                                $item->barang
                            )->nama_barang,

                            'jenis' => 'Keluar',

                            'badge' => 'danger'

                        ];

                    })

            )

            ->sortByDesc('tanggal')

            ->take(8)

            ->values();


        /*
        |--------------------------------------------------------------------------
        | FAST MOVING
        |--------------------------------------------------------------------------
        */

        $fastMoving = BarangKeluar::select(

            'barang_id',

            DB::raw(
                'SUM(jumlah) as total_keluar'
            )

        )

        ->whereBetween(
            'tanggal_keluar',
            [$start, $end]
        )

        ->with('barang')

        ->groupBy('barang_id')

        ->orderByDesc('total_keluar')

        ->take(5)

        ->get();


        /*
        |--------------------------------------------------------------------------
        | SLOW MOVING
        |--------------------------------------------------------------------------
        */

        $slowMoving = BarangKeluar::select(

            'barang_id',

            DB::raw(
                'SUM(jumlah) as total_keluar'
            )

        )

        ->whereBetween(
            'tanggal_keluar',
            [$start, $end]
        )

        ->with('barang')

        ->groupBy('barang_id')

        ->orderBy('total_keluar')

        ->take(5)

        ->get();


        /*
        |--------------------------------------------------------------------------
        | STOK TERBANYAK
        |--------------------------------------------------------------------------
        */

        $stokTerbanyak = Barang::orderByDesc('stok')
            ->take(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | BARANG PALING SERING KELUAR
        |--------------------------------------------------------------------------
        |
        | Dipisahkan dari fastMoving supaya nantinya mudah
        | dikembangkan menjadi analisis monitoring.
        |
        */

        $totalBarangKeluar = BarangKeluar::whereBetween(
            'tanggal_keluar',
            [$start, $end]
        )->count();


        /*
        |--------------------------------------------------------------------------
        | FEEDBACK MONITORING
        |--------------------------------------------------------------------------
        */

        $feedback = [];


        // Feedback stok habis
        if ($stokHabis > 0) {

            $feedback[] = [

                'type' => 'danger',

                'icon' => 'fa-times-circle',

                'title' => 'Stok Habis',

                'message' =>
                    "Terdapat {$stokHabis} barang yang tidak memiliki stok. " .
                    "Segera lakukan pengecekan dan pengadaan kembali."

            ];
        }


        // Feedback stok menipis
        if ($stokMenipis > 0) {

            $feedback[] = [

                'type' => 'warning',

                'icon' => 'fa-exclamation-triangle',

                'title' => 'Stok Menipis',

                'message' =>
                    "Terdapat {$stokMenipis} barang yang berada " .
                    "pada atau di bawah batas stok minimum. " .
                    "Disarankan melakukan pengecekan dan pengadaan kembali."

            ];
        }


        // Feedback barang hampir kadaluarsa
        if ($barangHampirKadaluarsa > 0) {

            $feedback[] = [

                'type' => 'warning',

                'icon' => 'fa-calendar-times',

                'title' => 'Barang Hampir Kadaluarsa',

                'message' =>
                    "Terdapat {$barangHampirKadaluarsa} barang yang " .
                    "akan mendekati tanggal kadaluarsa dalam 30 hari ke depan. " .
                    "Disarankan memprioritaskan penggunaan atau distribusi " .
                    "barang tersebut."

            ];
        }


        // Feedback barang keluar lebih banyak
        if ($barangKeluarPeriode > $barangMasukPeriode) {

            $feedback[] = [

                'type' => 'danger',

                'icon' => 'fa-arrow-up',

                'title' => 'Pengeluaran Lebih Tinggi',

                'message' =>
                    "Jumlah barang keluar pada periode ini lebih tinggi " .
                    "dibandingkan barang masuk. Perhatikan ketersediaan stok."

            ];
        }


        // Feedback barang masuk lebih banyak
        elseif ($barangMasukPeriode > $barangKeluarPeriode) {

            $feedback[] = [

                'type' => 'info',

                'icon' => 'fa-arrow-down',

                'title' => 'Penambahan Stok Lebih Tinggi',

                'message' =>
                    "Jumlah barang masuk pada periode ini lebih tinggi " .
                    "dibandingkan barang keluar. Terjadi penambahan persediaan."

            ];
        }


        // Feedback overstock
        if ($stokBerlebih > 0) {

            $feedback[] = [

                'type' => 'info',

                'icon' => 'fa-boxes',

                'title' => 'Stok Berlebih',

                'message' =>
                    "Terdapat {$stokBerlebih} barang dengan jumlah stok " .
                    "yang relatif tinggi dibandingkan batas minimumnya. " .
                    "Perhatikan pengadaan agar tidak terjadi penumpukan."

            ];
        }


        // Feedback stock opname
        if ($stockOpnameBulanIni == 0) {

            $feedback[] = [

                'type' => 'secondary',

                'icon' => 'fa-clipboard-check',

                'title' => 'Stock Opname',

                'message' =>
                    'Belum terdapat aktivitas stock opname pada bulan ini. ' .
                    'Disarankan melakukan pemeriksaan stok secara berkala.'

            ];
        }


        /*
        |--------------------------------------------------------------------------
        | STATUS KESEHATAN INVENTORY
        |--------------------------------------------------------------------------
        */

        if ($stokHabis > 0) {

            $statusInventory = 'Perlu Perhatian';

            $statusInventoryClass = 'danger';

        } elseif (
            $stokMenipis > 0 ||
            $barangHampirKadaluarsa > 0
        ) {

            $statusInventory = 'Perlu Dipantau';

            $statusInventoryClass = 'warning';

        } else {

            $statusInventory = 'Aman';

            $statusInventoryClass = 'success';
        }


        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'monitoring.index',
            compact(

                // Ringkasan
                'totalBarang',
                'totalSupplier',
                'totalStok',

                // Kondisi stok
                'stokMenipis',
                'stokHabis',
                'stokAman',
                'stokBerlebih',

                // Transaksi
                'barangMasukPeriode',
                'barangKeluarPeriode',

                // Aktivitas hari ini
                'barangMasukHariIni',
                'barangKeluarHariIni',

                // Stock opname
                'stockOpnamePeriode',
                'stockOpnameBulanIni',

                // Kondisi barang
                'barangHampirHabis',
                'barangHampirKadaluarsa',
                'barangHabis',
                'barangStokBerlebih',

                // Aktivitas
                'aktivitasTerbaru',

                // Analisis
                'fastMoving',
                'slowMoving',
                'stokTerbanyak',
                'totalBarangKeluar',

                // Feedback
                'feedback',

                // Status inventory
                'statusInventory',
                'statusInventoryClass',

                // Periode
                'periode'
            )
        );
    }


    /**
     * ============================================================
     * DATA GRAFIK MONITORING
     * ============================================================
     */
    public function chart(Request $request)
    {
        $periode = $request->get(
            'periode',
            'minggu'
        );


        $labels = [];

        $masuk = [];

        $keluar = [];


        /*
        |--------------------------------------------------------------------------
        | HARI
        |--------------------------------------------------------------------------
        |
        | Menampilkan aktivitas berdasarkan jam.
        |
        */

        switch ($periode) {

            case 'hari':

                for ($i = 7; $i <= 17; $i++) {

                    $labels[] =
                        sprintf(
                            '%02d:00',
                            $i
                        );


                    $masuk[] =
                        BarangMasuk::whereDate(
                            'tanggal_masuk',
                            today()
                        )

                        ->whereRaw(
                            'HOUR(tanggal_masuk) = ?',
                            [$i]
                        )

                        ->sum('jumlah');


                    $keluar[] =
                        BarangKeluar::whereDate(
                            'tanggal_keluar',
                            today()
                        )

                        ->whereRaw(
                            'HOUR(tanggal_keluar) = ?',
                            [$i]
                        )

                        ->sum('jumlah');
                }

                break;


            /*
            |--------------------------------------------------------------------------
            | 7 HARI TERAKHIR
            |--------------------------------------------------------------------------
            */

            case 'minggu':

                for ($i = 6; $i >= 0; $i--) {

                    $tanggal =
                        Carbon::today()
                            ->subDays($i);


                    $labels[] =
                        $tanggal->translatedFormat('D');


                    $masuk[] =
                        BarangMasuk::whereDate(
                            'tanggal_masuk',
                            $tanggal
                        )

                        ->sum('jumlah');


                    $keluar[] =
                        BarangKeluar::whereDate(
                            'tanggal_keluar',
                            $tanggal
                        )

                        ->sum('jumlah');
                }

                break;


            /*
            |--------------------------------------------------------------------------
            | BULAN
            |--------------------------------------------------------------------------
            */

            case 'bulan':

                $days =
                    now()->daysInMonth;


                for ($i = 1; $i <= $days; $i++) {

                    $tanggal =
                        Carbon::create(
                            now()->year,
                            now()->month,
                            $i
                        );


                    $labels[] =
                        $tanggal->format('d');


                    $masuk[] =
                        BarangMasuk::whereDate(
                            'tanggal_masuk',
                            $tanggal
                        )

                        ->sum('jumlah');


                    $keluar[] =
                        BarangKeluar::whereDate(
                            'tanggal_keluar',
                            $tanggal
                        )

                        ->sum('jumlah');
                }

                break;


            /*
            |--------------------------------------------------------------------------
            | TAHUN
            |--------------------------------------------------------------------------
            */

            case 'tahun':

                for ($i = 1; $i <= 12; $i++) {

                    $tanggal =
                        Carbon::create(
                            now()->year,
                            $i,
                            1
                        );


                    $labels[] =
                        $tanggal->translatedFormat(
                            'M'
                        );


                    $masuk[] =
                        BarangMasuk::whereYear(
                            'tanggal_masuk',
                            now()->year
                        )

                        ->whereMonth(
                            'tanggal_masuk',
                            $i
                        )

                        ->sum('jumlah');


                    $keluar[] =
                        BarangKeluar::whereYear(
                            'tanggal_keluar',
                            now()->year
                        )

                        ->whereMonth(
                            'tanggal_keluar',
                            $i
                        )

                        ->sum('jumlah');
                }

                break;


            /*
            |--------------------------------------------------------------------------
            | DEFAULT
            |--------------------------------------------------------------------------
            */

            default:

                for ($i = 6; $i >= 0; $i--) {

                    $tanggal =
                        Carbon::today()
                            ->subDays($i);


                    $labels[] =
                        $tanggal->translatedFormat('D');


                    $masuk[] =
                        BarangMasuk::whereDate(
                            'tanggal_masuk',
                            $tanggal
                        )

                        ->sum('jumlah');


                    $keluar[] =
                        BarangKeluar::whereDate(
                            'tanggal_keluar',
                            $tanggal
                        )

                        ->sum('jumlah');
                }

                break;
        }


        /*
        |--------------------------------------------------------------------------
        | RESPONSE JSON
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'labels' => $labels,

            'masuk' => $masuk,

            'keluar' => $keluar

        ]);
    }
}