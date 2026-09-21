<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Supplier;
use App\Models\Satuan;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\KonversiSatuan;
use Illuminate\Validation\Rule;


class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;

        $kategori = $request->kategori;

        $data = Barang::with([
                'kategori',
                'satuan'
            ])

            ->when($search, function ($q) use ($search) {

                $q->where('kode_barang', 'like', "%{$search}%")
                  ->orWhere('nama_barang', 'like', "%{$search}%");

            })

            ->when($kategori, function ($q) use ($kategori) {

                $q->where('kategori_id', $kategori);

            })

            ->latest()
            ->paginate(10)
            ->withQueryString();

        $kategoris = Kategori::orderBy('nama_kategori')->get();

        return view('barang.index', compact(
            'data',
            'search',
            'kategori',
            'kategoris'
        ));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kategori = Kategori::orderBy('nama_kategori')->get();

        $supplier = Supplier::where('status', 'Aktif')
            ->orderBy('nama_supplier')
            ->get();

        $satuan = Satuan::orderBy('nama_satuan')->get();

        $kode = $this->generateKode();

        return view('barang.create', compact(
            'kategori',
            'supplier',
            'satuan',
            'kode'
        ));
    }


    /**
     * Store a newly created resource.
     */
    public function store(Request $request)
    {
        $request->validate([

            'nama_barang'  => 'required|string|max:255',

            'kategori_id'  => 'required|exists:kategori,id',

            'supplier_id' => [
                'required',
                Rule::exists('supplier', 'id')
                    ->where(fn ($query) => $query->where('status', 'Aktif')),
            ],

            'satuan_id'    => 'required|exists:satuan,id',

            'konversi_satuan_id' =>
                'nullable|exists:satuan,id|different:satuan_id',

            'nilai_konversi' =>
                'nullable|integer|min:1|required_with:konversi_satuan_id',

            'stok_minimum' =>
                'required|integer|min:0',

            'lokasi_rak' =>
                'nullable|string|max:100',

            'status' =>
                'required|in:Aktif,Nonaktif',
        ]);


        DB::transaction(function () use ($request) {

            /*
            * GENERATE KODE BARANG
            */
            $kodeBarang = $this->generateKode();


            /*
            * SIMPAN BARANG
            */
            $barang = Barang::create([

                'kode_barang'  => $kodeBarang,

                'nama_barang'  => $request->nama_barang,

                'kategori_id'  => $request->kategori_id,

                'supplier_id'  => $request->supplier_id,

                'satuan_id'    => $request->satuan_id,

                'stok'         => 0,

                'stok_minimum' => $request->stok_minimum,

                'lokasi_rak'   => $request->lokasi_rak,

                'status'       => $request->status,

            ]);


            /*
            * SIMPAN KONVERSI SATUAN
            */
            if (
                $request->filled('konversi_satuan_id') &&
                $request->filled('nilai_konversi')
            ) {

                KonversiSatuan::create([

                    'barang_id' =>
                        $barang->id,

                    'satuan_id' =>
                        $request->konversi_satuan_id,

                    'nilai_konversi' =>
                        $request->nilai_konversi,

                ]);
            }


            /*
            * LOG AKTIVITAS
            */
            LogAktivitas::create([

                'user_id' =>
                    Auth::id(),

                'modul' =>
                    'Barang',

                'aktivitas' =>
                    'CREATE',

                'deskripsi' =>
                    'Menambahkan barang '
                    . $kodeBarang
                    . ' - '
                    . $request->nama_barang,

                'ip_address' =>
                    $request->ip(),

            ]);

        });


        return redirect()
            ->route('barang.index')
            ->with(
                'success',
                'Barang berhasil ditambahkan.'
            );
    }

    private function generateKode()
    {
        $lastBarang = Barang::orderBy('id', 'desc')->first();

        if (!$lastBarang) {
            return 'BRG0001';
        }

        $nomor = (int) str_replace('BRG', '', $lastBarang->kode_barang);

        $nomor++;

        return 'BRG' . str_pad($nomor, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Barang $barang)
    {
        $kategori = Kategori::orderBy('nama_kategori')->get();

        $supplier = Supplier::where('status', 'Aktif')
            ->orderBy('nama_supplier')
            ->get();

        $satuan = Satuan::orderBy('nama_satuan')->get();

        $barang->load('konversiSatuan.satuan');

        return view('barang.edit', compact(
            'barang',
            'kategori',
            'supplier',
            'satuan'
        ));
    }


    /**
     * Update the specified resource.
     */
    public function update(Request $request, Barang $barang)
    {
        $request->validate([

            'nama_barang'  =>
                'required|string|max:255',

            'kategori_id'  =>
                'required|exists:kategori,id',

            'supplier_id' => [
                'required',
                Rule::exists('supplier', 'id')
                    ->where(fn ($query) => $query->where('status', 'Aktif')),
            ],

            'satuan_id'    =>
                'required|exists:satuan,id',

            'konversi_satuan_id' =>
                'nullable|exists:satuan,id|different:satuan_id',

            'nilai_konversi' =>
                'nullable|integer|min:1|required_with:konversi_satuan_id',

            'stok_minimum' =>
                'required|integer|min:0',

            'lokasi_rak' =>
                'nullable|string|max:100',

            'status' =>
                'required|in:Aktif,Nonaktif',

        ]);


        $kodeBarang =
            $barang->kode_barang;

        $namaBarangLama =
            $barang->nama_barang;


        DB::transaction(function () use (
            $request,
            $barang,
            $kodeBarang,
            $namaBarangLama
        ) {

            /*
            * UPDATE DATA BARANG
            */
            $barang->update([

                'nama_barang' =>
                    $request->nama_barang,

                'kategori_id' =>
                    $request->kategori_id,

                'supplier_id' =>
                    $request->supplier_id,

                'satuan_id' =>
                    $request->satuan_id,

                'stok_minimum' =>
                    $request->stok_minimum,

                'lokasi_rak' =>
                    $request->lokasi_rak,

                'status' =>
                    $request->status,

            ]);


            /*
            * HAPUS KONVERSI LAMA
            *
            * Karena form kita saat ini
            * menggunakan satu konversi.
            */
            $barang->konversiSatuan()->delete();


            /*
            * SIMPAN KONVERSI BARU
            */
            if (
                $request->filled('konversi_satuan_id') &&
                $request->filled('nilai_konversi')
            ) {

                KonversiSatuan::create([

                    'barang_id' =>
                        $barang->id,

                    'satuan_id' =>
                        $request->konversi_satuan_id,

                    'nilai_konversi' =>
                        $request->nilai_konversi,

                ]);
            }


            /*
            * LOG AKTIVITAS
            */
            LogAktivitas::create([

                'user_id' =>
                    Auth::id(),

                'modul' =>
                    'Barang',

                'aktivitas' =>
                    'UPDATE',

                'deskripsi' =>
                    'Mengubah barang '
                    . $kodeBarang
                    . ' - '
                    . $namaBarangLama,

                'ip_address' =>
                    $request->ip(),

            ]);

        });


        return redirect()
            ->route('barang.index')
            ->with(
                'success',
                'Barang berhasil diperbarui.'
            );
    }


    /**
     * Remove the specified resource.
     */
    public function destroy(Barang $barang)
    {
        $kodeBarang = $barang->kode_barang;
        $namaBarang = $barang->nama_barang;

        /*
        * Cek apakah barang sudah memiliki riwayat
        */
        $adaRiwayat =
            $barang->barangMasuks()->exists()
            || $barang->barangKeluars()->exists()
            || $barang->barangKeluarDetails()->exists()
            || $barang->stockOpnames()->exists()
            || $barang->stockAdjustments()->exists()
            || $barang->konversiSatuan()->exists()
            || $barang->permintaanPengadaans()->exists();

        /*
        * Jika sudah memiliki riwayat,
        * jangan hapus permanen.
        * Ubah status menjadi Nonaktif.
        */
        if ($adaRiwayat) {

            $barang->update([
                'status' => 'Nonaktif',
            ]);

            /*
            * LOG AKTIVITAS
            */
            LogAktivitas::create([
                'user_id' =>
                    Auth::id(),

                'modul' =>
                    'Barang',

                'aktivitas' =>
                    'NONAKTIF',

                'deskripsi' =>
                    'Menonaktifkan barang '
                    . $kodeBarang
                    . ' - '
                    . $namaBarang
                    . ' karena memiliki riwayat transaksi.',

                'ip_address' =>
                    request()->ip(),
            ]);

            return redirect()
                ->route('barang.index')
                ->with(
                    'success',
                    'Barang memiliki riwayat transaksi sehingga tidak dihapus. Status barang diubah menjadi Nonaktif.'
                );
        }

        /*
        * Jika belum memiliki riwayat,
        * konversi boleh dihapus terlebih dahulu.
        */
        DB::transaction(function () use ($barang) {

            $barang->konversiSatuan()->delete();

            $barang->delete();
        });

        /*
        * LOG AKTIVITAS
        */
        LogAktivitas::create([
            'user_id' =>
                Auth::id(),

            'modul' =>
                'Barang',

            'aktivitas' =>
                'DELETE',

            'deskripsi' =>
                'Menghapus barang '
                . $kodeBarang
                . ' - '
                . $namaBarang,

            'ip_address' =>
                request()->ip(),
        ]);

        return redirect()
            ->route('barang.index')
            ->with(
                'success',
                'Barang berhasil dihapus.'
            );
    }

    public function cariByQr($kode)
    {
        $barang = Barang::with([
            'kategori',
            'supplier',
            'satuan',
            'konversiSatuan.satuan'
        ])
        ->where('kode_barang', $kode)
        ->where('status', 'Aktif')
        ->whereHas('supplier', function ($query) {
            $query->where('status', 'Aktif');
        })
        ->first();

        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $barang->id,
                'kode_barang' => $barang->kode_barang,
                'nama_barang' => $barang->nama_barang,
                'lokasi_rak' => $barang->lokasi_rak,
                'stok' => $barang->stok,
                'satuan' => $barang->satuan?->nama_satuan,
            ]
        ]);
    }
}