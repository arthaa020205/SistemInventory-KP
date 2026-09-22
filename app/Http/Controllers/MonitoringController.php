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

        $stokHabis = Barang::where('stok', '<=', 0)
            ->count();


        $stokMenipis = Barang::where('stok', '>', 0)
            ->whereColumn('stok', '<=', 'stok_minimum')
            ->count();


        $stokAman = Barang::where('stok', '>', 0)
            ->whereColumn('stok', '>', 'stok_minimum')
            ->count();


        /*
        |--------------------------------------------------------------------------
        | STOK BERLEBIH
        |--------------------------------------------------------------------------
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
        | BARANG MASUK PERIODE
        |--------------------------------------------------------------------------
        |
        | Menghitung jumlah TRANSAKSI barang masuk,
        | bukan jumlah kuantitas barang.
        |
        */

        $barangMasukPeriode = BarangMasuk::whereBetween(
            'tanggal_masuk',
            [$start, $end]
        )->count();


        /*
        |--------------------------------------------------------------------------
        | BARANG KELUAR PERIODE
        |--------------------------------------------------------------------------
        |
        | 1 header BarangKeluar = 1 transaksi.
        |
        | Penjualan yang memiliki banyak detail tetap dihitung
        | sebagai 1 transaksi.
        |
        */

        $keluarBiasaPeriode = BarangKeluar::whereBetween(
            'tanggal_keluar',
            [$start, $end]
        )
            ->where('jenis_keluar', '!=', 'Penjualan')
            ->count();


        $keluarPenjualanPeriode = BarangKeluar::whereBetween(
            'tanggal_keluar',
            [$start, $end]
        )
            ->where('jenis_keluar', 'Penjualan')
            ->count();


        $barangKeluarPeriode =
            $keluarBiasaPeriode +
            $keluarPenjualanPeriode;


        /*
        |--------------------------------------------------------------------------
        | AKTIVITAS HARI INI
        |--------------------------------------------------------------------------
        */

        $barangMasukHariIni = BarangMasuk::whereDate(
            'tanggal_masuk',
            today()
        )->count();


        $keluarBiasaHariIni = BarangKeluar::whereDate(
            'tanggal_keluar',
            today()
        )
            ->where('jenis_keluar', '!=', 'Penjualan')
            ->count();


        $keluarPenjualanHariIni = BarangKeluar::whereDate(
            'tanggal_keluar',
            today()
        )
            ->where('jenis_keluar', 'Penjualan')
            ->count();


        $barangKeluarHariIni =
            $keluarBiasaHariIni +
            $keluarPenjualanHariIni;


        /*
        |--------------------------------------------------------------------------
        | STOCK OPNAME
        |--------------------------------------------------------------------------
        */

        $stockOpnamePeriode = StockOpname::whereBetween(
            'tanggal_opname',
            [$start, $end]
        )->count();


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
        */

        $barangHampirKadaluarsa = BarangMasuk::whereNotNull('expired_date')
            ->whereHas('barang', function ($query) {
                $query->where('stok', '>', 0);
            })
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

        $aktivitasMasuk = collect(
            BarangMasuk::with('barang')
                ->whereBetween('tanggal_masuk', [$start, $end])
                ->latest('created_at')
                ->take(5)
                ->get()
                ->map(function ($item) {

                    return [
                        'tanggal' => $item->created_at,
                        'kode'    => $item->kode_transaksi,
                        'barang'  => optional($item->barang)->nama_barang ?? '-',
                        'jenis'   => 'Barang Masuk',
                        'badge'   => 'success',
                    ];

                })
                ->toArray()
        );


        $aktivitasKeluar = collect(
            BarangKeluar::with([
                    'barang',
                    'details.barang'
                ])
                ->whereBetween('tanggal_keluar', [$start, $end])
                ->latest('created_at')
                ->take(5)
                ->get()
                ->map(function ($item) {

                    if ($item->jenis_keluar === 'Penjualan') {

                        $namaBarang = $item->details
                            ->map(function ($detail) {
                                return optional($detail->barang)->nama_barang;
                            })
                            ->filter()
                            ->unique()
                            ->implode(', ');

                        $jenis = 'Penjualan';

                    } else {

                        $namaBarang = optional($item->barang)->nama_barang ?? '-';

                        $jenis = $item->jenis_keluar;
                    }

                    return [
                        'tanggal' => $item->created_at,
                        'kode'    => $item->kode_transaksi,
                        'barang'  => $namaBarang ?: '-',
                        'jenis'   => $jenis,
                        'badge'   => 'danger',
                    ];

                })
                ->toArray()
        );


        $aktivitasTerbaru = $aktivitasMasuk
            ->merge($aktivitasKeluar)
            ->sortByDesc('tanggal')
            ->take(8)
            ->values();


        /*
        |--------------------------------------------------------------------------
        | ANALISIS PERGERAKAN BARANG
        |--------------------------------------------------------------------------
        |
        | Bagian ini BERBEDA dengan total transaksi.
        |
        | Fast Moving / Slow Moving menghitung jumlah barang
        | yang benar-benar keluar.
        |
        | Non-Penjualan:
        |   barang_keluars.jumlah_dasar
        |
        | Penjualan:
        |   barang_keluar_details.jumlah_dasar
        |
        */


        /*
        |--------------------------------------------------------------------------
        | 1. TRANSAKSI NON PENJUALAN
        |--------------------------------------------------------------------------
        */

        $keluarBiasa = DB::table('barang_keluars')
            ->select(
                'barang_keluars.barang_id',
                DB::raw(
                    'SUM(barang_keluars.jumlah_dasar) AS total_keluar'
                )
            )
            ->whereBetween(
                'barang_keluars.tanggal_keluar',
                [$start, $end]
            )
            ->where(
                'barang_keluars.jenis_keluar',
                '!=',
                'Penjualan'
            )
            ->whereNotNull(
                'barang_keluars.barang_id'
            )
            ->groupBy(
                'barang_keluars.barang_id'
            )
            ->get();


        /*
        |--------------------------------------------------------------------------
        | 2. TRANSAKSI PENJUALAN
        |--------------------------------------------------------------------------
        */

        $keluarPenjualan = DB::table(
            'barang_keluar_details'
        )
            ->join(
                'barang_keluars',
                'barang_keluars.id',
                '=',
                'barang_keluar_details.barang_keluar_id'
            )
            ->select(
                'barang_keluar_details.barang_id',
                DB::raw(
                    'SUM(barang_keluar_details.jumlah_dasar) AS total_keluar'
                )
            )
            ->whereBetween(
                'barang_keluars.tanggal_keluar',
                [$start, $end]
            )
            ->where(
                'barang_keluars.jenis_keluar',
                'Penjualan'
            )
            ->whereNotNull(
                'barang_keluar_details.barang_id'
            )
            ->groupBy(
                'barang_keluar_details.barang_id'
            )
            ->get();


        /*
        |--------------------------------------------------------------------------
        | 3. GABUNGKAN NON PENJUALAN + PENJUALAN
        |--------------------------------------------------------------------------
        */

        $pergerakanBarang = $keluarBiasa

            ->concat($keluarPenjualan)

            ->groupBy('barang_id')

            ->map(function ($items, $barangId) {

                return (object) [

                    'barang_id' => $barangId,

                    'total_keluar' => $items->sum(
                        function ($item) {

                            return (float) $item->total_keluar;

                        }
                    ),

                ];

            })

            ->values();


        /*
        |--------------------------------------------------------------------------
        | 4. AMBIL DATA BARANG
        |--------------------------------------------------------------------------
        */

        $barangIds = $pergerakanBarang
            ->pluck('barang_id')
            ->filter()
            ->unique()
            ->values();


        $barangData = Barang::whereIn(
            'id',
            $barangIds
        )
            ->get()
            ->keyBy('id');


        /*
        |--------------------------------------------------------------------------
        | 5. PASANG NAMA BARANG
        |--------------------------------------------------------------------------
        */

        $pergerakanBarang = $pergerakanBarang

            ->map(function ($item) use ($barangData) {

                $barang = $barangData->get(
                    $item->barang_id
                );

                $item->nama_barang = $barang
                    ? $barang->nama_barang
                    : '-';

                return $item;

            });


        /*
        |--------------------------------------------------------------------------
        | 6. FAST MOVING
        |--------------------------------------------------------------------------
        */

        $fastMoving = $pergerakanBarang
            ->sortByDesc('total_keluar')
            ->take(5)
            ->values();


        /*
        |--------------------------------------------------------------------------
        | 7. SLOW MOVING
        |--------------------------------------------------------------------------
        */

        $slowMoving = $pergerakanBarang
            ->sortBy('total_keluar')
            ->take(5)
            ->values();


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
        | TOTAL TRANSAKSI BARANG KELUAR
        |--------------------------------------------------------------------------
        |
        | 1 header BarangKeluar = 1 transaksi.
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


        /*
        |--------------------------------------------------------------------------
        | FEEDBACK STOK HABIS
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | FEEDBACK STOK MENIPIS
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | FEEDBACK BARANG HAMPIR KADALUARSA
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | FEEDBACK BARANG KELUAR LEBIH BANYAK
        |--------------------------------------------------------------------------
        */

        if ($barangKeluarPeriode > $barangMasukPeriode) {

            $feedback[] = [

                'type' => 'danger',

                'icon' => 'fa-arrow-up',

                'title' => 'Pengeluaran Lebih Tinggi',

                'message' =>
                    "Jumlah transaksi barang keluar pada periode ini lebih tinggi " .
                    "dibandingkan transaksi barang masuk. Perhatikan ketersediaan stok."

            ];
        }


        /*
        |--------------------------------------------------------------------------
        | FEEDBACK BARANG MASUK LEBIH BANYAK
        |--------------------------------------------------------------------------
        */

        elseif ($barangMasukPeriode > $barangKeluarPeriode) {

            $feedback[] = [

                'type' => 'info',

                'icon' => 'fa-arrow-down',

                'title' => 'Penambahan Stok Lebih Tinggi',

                'message' =>
                    "Jumlah transaksi barang masuk pada periode ini lebih tinggi " .
                    "dibandingkan transaksi barang keluar."

            ];
        }


        /*
        |--------------------------------------------------------------------------
        | FEEDBACK OVERSTOCK
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | FEEDBACK STOCK OPNAME
        |--------------------------------------------------------------------------
        */

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

                'totalBarang',
                'totalSupplier',
                'totalStok',

                'stokMenipis',
                'stokHabis',
                'stokAman',
                'stokBerlebih',

                'barangMasukPeriode',
                'barangKeluarPeriode',

                'barangMasukHariIni',
                'barangKeluarHariIni',

                'stockOpnamePeriode',
                'stockOpnameBulanIni',

                'barangHampirHabis',
                'barangHampirKadaluarsa',
                'barangHabis',
                'barangStokBerlebih',

                'aktivitasTerbaru',

                'fastMoving',
                'slowMoving',
                'stokTerbanyak',
                'totalBarangKeluar',

                'feedback',

                'statusInventory',
                'statusInventoryClass',

                'periode'
            )
        );
    }


    /**
     * ============================================================
     * DATA GRAFIK MONITORING
     * ============================================================
     *
     * Grafik juga menggunakan JUMLAH TRANSAKSI.
     *
     * Barang masuk:
     *   1 record BarangMasuk = 1 transaksi
     *
     * Barang keluar:
     *   1 record BarangKeluar = 1 transaksi
     *
     * Penjualan dengan banyak detail tetap = 1 transaksi.
     *
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
        */

        switch ($periode) {

            case 'hari':

                for ($i = 7; $i <= 17; $i++) {

                    $labels[] =
                        sprintf(
                            '%02d:00',
                            $i
                        );


                    /*
                    | Barang Masuk = jumlah transaksi
                    */

                    $masuk[] =
                        BarangMasuk::whereDate(
                            'tanggal_masuk',
                            today()
                        )
                        ->whereRaw(
                            'HOUR(tanggal_masuk) = ?',
                            [$i]
                        )
                        ->count();


                    /*
                    | Barang Keluar = jumlah header transaksi
                    */

                    $keluar[] =
                        BarangKeluar::whereDate(
                            'tanggal_keluar',
                            today()
                        )
                        ->whereRaw(
                            'HOUR(tanggal_keluar) = ?',
                            [$i]
                        )
                        ->count();

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


                    /*
                    | Barang Masuk = jumlah transaksi
                    */

                    $masuk[] =
                        BarangMasuk::whereDate(
                            'tanggal_masuk',
                            $tanggal
                        )
                        ->count();


                    /*
                    | Barang Keluar = jumlah transaksi
                    */

                    $keluar[] =
                        BarangKeluar::whereDate(
                            'tanggal_keluar',
                            $tanggal
                        )
                        ->count();

                }

                break;


            /*
            |--------------------------------------------------------------------------
            | BULAN
            |--------------------------------------------------------------------------
            */

            case 'bulan':

                $days = now()->daysInMonth;


                for ($i = 1; $i <= $days; $i++) {

                    $tanggal =
                        Carbon::create(
                            now()->year,
                            now()->month,
                            $i
                        );


                    $labels[] =
                        $tanggal->format('d');


                    /*
                    | Barang Masuk = jumlah transaksi
                    */

                    $masuk[] =
                        BarangMasuk::whereDate(
                            'tanggal_masuk',
                            $tanggal
                        )
                        ->count();


                    /*
                    | Barang Keluar = jumlah transaksi
                    */

                    $keluar[] =
                        BarangKeluar::whereDate(
                            'tanggal_keluar',
                            $tanggal
                        )
                        ->count();

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
                        $tanggal->translatedFormat('M');


                    /*
                    | Barang Masuk = jumlah transaksi
                    */

                    $masuk[] =
                        BarangMasuk::whereYear(
                            'tanggal_masuk',
                            now()->year
                        )
                        ->whereMonth(
                            'tanggal_masuk',
                            $i
                        )
                        ->count();


                    /*
                    | Barang Keluar = jumlah transaksi
                    */

                    $keluar[] =
                        BarangKeluar::whereYear(
                            'tanggal_keluar',
                            now()->year
                        )
                        ->whereMonth(
                            'tanggal_keluar',
                            $i
                        )
                        ->count();

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


                    /*
                    | Barang Masuk = jumlah transaksi
                    */

                    $masuk[] =
                        BarangMasuk::whereDate(
                            'tanggal_masuk',
                            $tanggal
                        )
                        ->count();


                    /*
                    | Barang Keluar = jumlah transaksi
                    */

                    $keluar[] =
                        BarangKeluar::whereDate(
                            'tanggal_keluar',
                            $tanggal
                        )
                        ->count();

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