<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\Supplier;
use App\Models\Satuan;
use App\Models\PermintaanPengadaan;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\KonversiSatuan;
use Illuminate\Validation\ValidationException;

class BarangMasukController extends Controller
{
    /**
     * Menampilkan data barang masuk.
     * Owner dan Petugas bisa melihat.
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $barang = $request->barang;
        $dari = $request->dari;
        $sampai = $request->sampai;

        $data = BarangMasuk::with([
            'barang',
            'supplier',
            'satuan',
            'user'
        ])
        ->when($search, function ($query) use ($search) {

            $query->where(function ($q) use ($search) {

                $q->where(
                    'kode_transaksi',
                    'like',
                    "%{$search}%"
                )

                ->orWhereHas('barang', function ($q2) use ($search) {

                    $q2->where(
                        'nama_barang',
                        'like',
                        "%{$search}%"
                    );

                });

            });

        })
        ->when($barang, function ($query) use ($barang) {

            $query->where('barang_id', $barang);

        })
        ->when($dari, function ($query) use ($dari) {

            $query->whereDate(
                'tanggal_masuk',
                '>=',
                $dari
            );

        })
        ->when($sampai, function ($query) use ($sampai) {

            $query->whereDate(
                'tanggal_masuk',
                '<=',
                $sampai
            );

        })
        ->latest()
        ->paginate(10)
        ->withQueryString();

        $barangs = Barang::where('status', 'Aktif')
            ->orderBy('nama_barang')
            ->get();

        return view(
            'barang-masuk.index',
            compact(
                'data',
                'search',
                'barang',
                'barangs',
                'dari',
                'sampai'
            )
        );
    }


    /**
     * Form tambah barang masuk.
     * Hanya Petugas yang bisa mengakses
     * karena route dilindungi permission.
     */
    public function create()
    {
        /*
        * Ambil barang aktif beserta supplier,
        * satuan dasar, dan konversi satuannya.
        */
        $barang = Barang::with([
            'satuan',
            'supplier',
            'konversiSatuan.satuan'
        ])
        ->where('status', 'Aktif')
        ->whereHas('supplier', function ($query) {
            $query->where('status', 'Aktif');
        })
        ->orderBy('nama_barang')
        ->get();

        /*
        * Semua satuan
        */
        $satuan = Satuan::orderBy('nama_satuan')
            ->get();

        /*
        * Generate kode transaksi
        */
        $kode = $this->generateKode();

        /*
        * Permintaan pengadaan yang sudah disetujui
        * dan belum memiliki barang masuk.
        */
        $permintaan = PermintaanPengadaan::with('barang')
            ->where('status', 'Disetujui')
            ->whereNull('barang_masuk_id')
            ->orderBy('tanggal_permintaan')
            ->get();

        return view(
            'barang-masuk.create',
            compact(
                'barang',
                'satuan',
                'kode',
                'permintaan'
            )
        );
    }


    /**
     * Menyimpan barang masuk.
     */
    public function store(Request $request)
    {
        $request->validate([

            'barang_id' =>
                'required|exists:barang,id',

            'tanggal_masuk' =>
                'required|date',

            'jumlah' =>
                'required|integer|min:1',

            'satuan_id' =>
                'required|exists:satuan,id',

            'harga_beli' =>
                'required|numeric|min:0',

            'expired_date' =>
                'nullable|date',

            'nomor_faktur' =>
                'nullable|string|max:100',

            'keterangan' =>
                'nullable|string',

            'permintaan_id' =>
                'nullable|exists:permintaan_pengadaans,id',

        ]);

        $barangValid = Barang::with('supplier')
            ->where('id', $request->barang_id)
            ->where('status', 'Aktif')
            ->first();

        if (!$barangValid) {
            throw ValidationException::withMessages([
                'barang_id' => 'Barang tidak aktif atau tidak dapat digunakan.'
            ]);
        }

        if (
            !$barangValid->supplier ||
            $barangValid->supplier->status !== 'Aktif'
        ) {
            throw ValidationException::withMessages([
                'barang_id' =>
                    'Barang tidak dapat digunakan karena supplier sudah tidak aktif.'
            ]);
        }


        DB::transaction(function () use (
            $request,
            &$barangMasuk
        ) {

            /*
            |--------------------------------------------------------------------------
            | AMBIL BARANG
            |--------------------------------------------------------------------------
            */

            $barang = Barang::with([
                'satuan',
                'konversiSatuan'
            ])
            ->lockForUpdate()
            ->findOrFail(
                $request->barang_id
            );


            /*
            |--------------------------------------------------------------------------
            | KONVERSI SATUAN
            |--------------------------------------------------------------------------
            |
            | Stok database selalu menggunakan SATUAN DASAR.
            |
            | Contoh:
            |
            | Satuan dasar  = PCS
            | Satuan transaksi = DUS
            | 1 DUS = 100 PCS
            |
            */


            $nilaiKonversi = 1;


            /*
            |--------------------------------------------------------------------------
            | JIKA SATUAN TRANSAKSI BUKAN SATUAN DASAR
            |--------------------------------------------------------------------------
            */

            if (
                (int) $request->satuan_id
                !==
                (int) $barang->satuan_id
            ) {

                $konversi = $barang
                    ->konversiSatuan
                    ->firstWhere(
                        'satuan_id',
                        $request->satuan_id
                    );


                if (!$konversi) {

                    abort(
                        422,
                        'Konversi satuan untuk barang ini belum tersedia.'
                    );
                }


                $nilaiKonversi =
                    (int) $konversi->nilai_konversi;
            }


            /*
            |--------------------------------------------------------------------------
            | HITUNG JUMLAH SATUAN DASAR
            |--------------------------------------------------------------------------
            |
            | Contoh:
            |
            | Jumlah transaksi = 5 DUS
            | Konversi          = 100 PCS
            |
            | 5 × 100 = 500 PCS
            |
            */

            $jumlahDasar =
                (int) $request->jumlah
                *
                $nilaiKonversi;


            /*
            |--------------------------------------------------------------------------
            | GENERATE KODE TRANSAKSI
            |--------------------------------------------------------------------------
            */

            $kodeTransaksi =
                $this->generateKode();


            /*
            |--------------------------------------------------------------------------
            | SIMPAN BARANG MASUK
            |--------------------------------------------------------------------------
            */

            $barangMasuk =
                BarangMasuk::create([

                    'kode_transaksi' =>
                        $kodeTransaksi,

                    'barang_id' =>
                        $barang->id,

                    'supplier_id' =>
                        $barang->supplier_id,

                    'tanggal_masuk' =>
                        $request->tanggal_masuk,


                    /*
                    |--------------------------------------------------------------------------
                    | JUMLAH TRANSAKSI
                    |--------------------------------------------------------------------------
                    |
                    | Ini TIDAK dikonversi.
                    |
                    | Kalau input:
                    | 5 DUS
                    |
                    | maka database:
                    | jumlah = 5
                    |
                    */

                    'jumlah' =>
                        $request->jumlah,


                    /*
                    |--------------------------------------------------------------------------
                    | SATUAN TRANSAKSI
                    |--------------------------------------------------------------------------
                    |
                    | Kalau transaksi menggunakan DUS,
                    | maka satuan_id = DUS.
                    |
                    */

                    'satuan_id' =>
                        $request->satuan_id,


                    /*
                    |--------------------------------------------------------------------------
                    | NILAI KONVERSI
                    |--------------------------------------------------------------------------
                    |
                    | 1 DUS = 100 PCS
                    |
                    */

                    'nilai_konversi' =>
                        $nilaiKonversi,


                    /*
                    |--------------------------------------------------------------------------
                    | JUMLAH SATUAN DASAR
                    |--------------------------------------------------------------------------
                    |
                    | 5 DUS × 100 PCS = 500 PCS
                    |
                    */

                    'jumlah_dasar' =>
                        $jumlahDasar,


                    /*
                    |--------------------------------------------------------------------------
                    | HARGA BELI
                    |--------------------------------------------------------------------------
                    |
                    | Harga beli mengikuti SATUAN TRANSAKSI.
                    |
                    | Contoh:
                    |
                    | 1 DUS = Rp50.000
                    | Jumlah = 5 DUS
                    |
                    | harga_beli = Rp50.000
                    |
                    |
                    | BUKAN Rp500 per PCS.
                    |
                    */

                    'harga_beli' =>
                        $request->harga_beli,


                    'expired_date' =>
                        $request->expired_date,

                    'nomor_faktur' =>
                        $request->nomor_faktur,

                    'keterangan' =>
                        $request->keterangan,

                    'user_id' =>
                        Auth::id(),

                ]);


            /*
            |--------------------------------------------------------------------------
            | UPDATE STOK BARANG
            |--------------------------------------------------------------------------
            |
            | Stok menggunakan jumlah_dasar.
            |
            | 5 DUS × 100 PCS
            | = 500 PCS
            |
            */

            $barang->increment(
                'stok',
                $jumlahDasar
            );


            /*
            |--------------------------------------------------------------------------
            | UPDATE PERMINTAAN PENGADAAN
            |--------------------------------------------------------------------------
            */

            if ($request->filled('permintaan_id')) {

                $permintaan =
                    PermintaanPengadaan::findOrFail(
                        $request->permintaan_id
                    );


                $permintaan->update([

                    'status' =>
                        'Selesai',

                    'barang_masuk_id' =>
                        $barangMasuk->id,

                ]);
            }

        });


        /*
        |--------------------------------------------------------------------------
        | LOG AKTIVITAS
        |--------------------------------------------------------------------------
        */

        LogAktivitas::create([

            'user_id' =>
                Auth::id(),

            'modul' =>
                'Barang Masuk',

            'aktivitas' =>
                'CREATE',

            'deskripsi' =>
                'Menambahkan barang masuk '
                . $barangMasuk->kode_transaksi,

            'ip_address' =>
                $request->ip(),

        ]);


        return redirect()
            ->route('barang-masuk.index')
            ->with(
                'success',
                'Barang masuk berhasil disimpan dan stok diperbarui.'
            );
    }


    /**
     * Detail barang masuk.
     * Owner dan Petugas bisa melihat.
     */
    public function show(BarangMasuk $barangMasuk)
    {
        $barangMasuk->load([
            'barang.satuan',
            'supplier',
            'satuan',
            'user'
        ]);

        return view(
            'barang-masuk.show',
            compact('barangMasuk')
        );
    }


    /**
     * Form edit barang masuk.
     */
    public function edit(BarangMasuk $barangMasuk)
    {
        /*
        * Barang aktif beserta supplier,
        * satuan, dan konfigurasi konversinya.
        */
        $barang = Barang::with([
            'satuan',
            'supplier',
            'konversiSatuan.satuan'
        ])
        ->where('status', 'Aktif')
        ->whereHas('supplier', function ($query) {
            $query->where('status', 'Aktif');
        })
        ->orderBy('nama_barang')
        ->get();

        /*
        * Semua satuan
        */
        $satuan = Satuan::orderBy('nama_satuan')
            ->get();

        /*
        * Permintaan pengadaan.
        */
        $permintaan = PermintaanPengadaan::with('barang')
            ->where('status', 'Disetujui')
            ->get();

        /*
        * Load data transaksi beserta relasi
        */
        $barangMasuk->load([
            'barang.satuan',
            'barang.supplier',
            'satuan'
        ]);

        return view(
            'barang-masuk.edit',
            compact(
                'barangMasuk',
                'barang',
                'satuan',
                'permintaan'
            )
        );
    }


    /**
     * Update barang masuk.
     */
    public function update(
        Request $request,
        BarangMasuk $barangMasuk
    ) {

        $request->validate([

            'barang_id' =>
                'required|exists:barang,id',

            'tanggal_masuk' =>
                'required|date',

            'jumlah' =>
                'required|integer|min:1',

            'satuan_id' =>
                'required|exists:satuan,id',

            'harga_beli' =>
                'required|numeric|min:0',

            'expired_date' =>
                'nullable|date',

            'nomor_faktur' =>
                'nullable|string|max:100',

            'keterangan' =>
                'nullable|string',

        ]);

        $barangValid = Barang::with('supplier')
            ->where('id', $request->barang_id)
            ->where('status', 'Aktif')
            ->first();

        if (!$barangValid) {
            throw ValidationException::withMessages([
                'barang_id' => 'Barang tidak aktif atau tidak dapat digunakan.'
            ]);
        }

        if (!$barangValid->supplier || $barangValid->supplier->status !== 'Aktif') {
            throw ValidationException::withMessages([
                'barang_id' => 'Barang tidak dapat digunakan karena supplier sudah tidak aktif.'
            ]);
        }


        DB::transaction(function () use (
            $request,
            $barangMasuk
        ) {

            /*
             * Kembalikan stok transaksi lama.
             *
             * Gunakan jumlah_dasar,
             * bukan jumlah transaksi.
             */
            $barangLama =
                Barang::lockForUpdate()
                    ->findOrFail(
                        $barangMasuk->barang_id
                    );


            $barangLama->decrement(
                'stok',
                $barangMasuk->jumlah_dasar
            );


            /*
             * Ambil barang baru.
             */
            $barangBaru =
                Barang::with([
                    'konversiSatuan'
                ])
                ->lockForUpdate()
                ->findOrFail(
                    $request->barang_id
                );


            /*
             * Default konversi.
             */
            $nilaiKonversi = 1;


            /*
             * Cari konversi jika satuan transaksi
             * berbeda dari satuan dasar barang.
             */
            if (
                (int) $request->satuan_id
                !==
                (int) $barangBaru->satuan_id
            ) {

                $konversi =
                    $barangBaru
                        ->konversiSatuan
                        ->firstWhere(
                            'satuan_id',
                            $request->satuan_id
                        );


                if (!$konversi) {

                    abort(
                        422,
                        'Konversi satuan untuk barang ini belum tersedia.'
                    );
                }


                $nilaiKonversi =
                    $konversi->nilai_konversi;
            }


            /*
             * Hitung jumlah dasar baru.
             */
            $jumlahDasar =
                $request->jumlah * $nilaiKonversi;


            /*
             * Tambahkan stok baru.
             */
            $barangBaru->increment(
                'stok',
                $jumlahDasar
            );


            /*
             * Update transaksi.
             */
            $barangMasuk->update([

                'barang_id' =>
                    $request->barang_id,

                'supplier_id' =>
                    $barangBaru->supplier_id,

                'tanggal_masuk' =>
                    $request->tanggal_masuk,

                'jumlah' =>
                    $request->jumlah,

                'satuan_id' =>
                    $request->satuan_id,

                'nilai_konversi' =>
                    $nilaiKonversi,

                'jumlah_dasar' =>
                    $jumlahDasar,

                'harga_beli' =>
                    $request->harga_beli,

                'expired_date' =>
                    $request->expired_date,

                'nomor_faktur' =>
                    $request->nomor_faktur,

                'keterangan' =>
                    $request->keterangan,

            ]);


            /*
             * Update permintaan pengadaan
             * jika transaksi ini berasal dari
             * permintaan.
             */
            $permintaan =
                PermintaanPengadaan::where(
                    'barang_masuk_id',
                    $barangMasuk->id
                )->first();


            if ($permintaan) {

                $permintaan->update([

                    'barang_id' =>
                        $request->barang_id,

                    'jumlah' =>
                        $request->jumlah,

                ]);
            }
        });


        /*
         * LOG AKTIVITAS
         */
        LogAktivitas::create([

            'user_id' =>
                Auth::id(),

            'modul' =>
                'Barang Masuk',

            'aktivitas' =>
                'UPDATE',

            'deskripsi' =>
                'Mengubah barang masuk '
                . $barangMasuk->kode_transaksi,

            'ip_address' =>
                $request->ip(),

        ]);


        return redirect()
            ->route('barang-masuk.index')
            ->with(
                'success',
                'Barang masuk berhasil diperbarui.'
            );
    }


    /**
     * Hapus barang masuk.
     */
    public function destroy(
        BarangMasuk $barangMasuk
    ) {

        /*
         * Simpan kode transaksi
         * sebelum dihapus.
         */
        $kodeTransaksi =
            $barangMasuk->kode_transaksi;


        DB::transaction(function () use (
            $barangMasuk
        ) {

            /*
             * Kurangi stok berdasarkan
             * jumlah dalam satuan dasar.
             */
            $barang =
                Barang::lockForUpdate()
                    ->findOrFail(
                        $barangMasuk->barang_id
                    );


            $barang->decrement(
                'stok',
                $barangMasuk->jumlah_dasar
            );


            /*
             * Jika transaksi berasal
             * dari permintaan pengadaan.
             */
            $permintaan =
                PermintaanPengadaan::where(
                    'barang_masuk_id',
                    $barangMasuk->id
                )->first();


            if ($permintaan) {

                $permintaan->update([

                    'status' =>
                        'Disetujui',

                    'barang_masuk_id' =>
                        null,

                ]);
            }


            /*
             * Hapus transaksi.
             */
            $barangMasuk->delete();
        });


        /*
         * LOG AKTIVITAS
         */
        LogAktivitas::create([

            'user_id' =>
                Auth::id(),

            'modul' =>
                'Barang Masuk',

            'aktivitas' =>
                'DELETE',

            'deskripsi' =>
                'Menghapus barang masuk '
                . $kodeTransaksi,

            'ip_address' =>
                request()->ip(),

        ]);


        return redirect()
            ->route('barang-masuk.index')
            ->with(
                'success',
                'Barang masuk berhasil dihapus.'
            );
    }


    /**
     * Generate kode transaksi.
     */
    private function generateKode()
    {
        $last =
            BarangMasuk::latest('id')
                ->first();


        if (!$last) {

            return 'BM0001';
        }


        $number =
            (int) substr(
                $last->kode_transaksi,
                2
            );


        return 'BM' .
            str_pad(
                $number + 1,
                4,
                '0',
                STR_PAD_LEFT
            );
    }
}