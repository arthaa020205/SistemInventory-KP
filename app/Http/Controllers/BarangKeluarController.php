<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\BarangKeluarDetail;
use App\Models\Pelanggan;
use App\Models\Satuan;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BarangKeluarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $jenis = $request->jenis;
        $dari = $request->dari;
        $sampai = $request->sampai;

        $data = BarangKeluar::with([
            'barang',
            'details.barang',
            'details.satuan',
            'pelanggan',
            'user'
        ])

        ->when($search, function ($query) use ($search) {

            $query->where(function ($q) use ($search) {

                $q->where(
                    'kode_transaksi',
                    'like',
                    "%{$search}%"
                )

                ->orWhereHas('barang', function ($q) use ($search) {

                    $q->where(
                        'nama_barang',
                        'like',
                        "%{$search}%"
                    );

                })

                ->orWhereHas('details.barang', function ($q) use ($search) {

                    $q->where(
                        'nama_barang',
                        'like',
                        "%{$search}%"
                    );

                });

            });

        })

        ->when($jenis, function ($query) use ($jenis) {

            $query->where(
                'jenis_keluar',
                $jenis
            );

        })

        ->when($dari, function ($query) use ($dari) {

            $query->whereDate(
                'tanggal_keluar',
                '>=',
                $dari
            );

        })

        ->when($sampai, function ($query) use ($sampai) {

            $query->whereDate(
                'tanggal_keluar',
                '<=',
                $sampai
            );

        })

        ->latest()
        ->paginate(10)
        ->withQueryString();

        return view(
            'barang-keluar.index',
            compact(
                'data',
                'search',
                'jenis',
                'dari',
                'sampai'
            )
        );
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $barang = Barang::with([
            'kategori',
            'supplier',
            'satuan',
            'konversiSatuan.satuan'
        ])
        ->where('status', 'Aktif')
        ->orderBy('nama_barang')
        ->get();

        $pelanggan = Pelanggan::orderBy(
            'nama_pelanggan'
        )->get();

        $satuan = Satuan::orderBy(
            'nama_satuan'
        )->get();

        $kode = $this->generateKode();

        return view(
            'barang-keluar.create',
            compact(
                'barang',
                'pelanggan',
                'satuan',
                'kode'
            )
        );
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDASI DASAR
        |--------------------------------------------------------------------------
        */

        $request->validate([

            'tanggal_keluar' =>
                'required|date',

            'jenis_keluar' =>
                'required|in:Transfer Ke Toko,Penjualan,Rusak,Pemakaian Internal,Kadaluarsa,Retur',

            'pelanggan_id' =>
                'nullable|exists:pelanggan,id',

            'tujuan' =>
                'nullable|string|max:100',

            'keterangan' =>
                'nullable|string',

        ]);


        /*
        |--------------------------------------------------------------------------
        | JIKA PENJUALAN
        |--------------------------------------------------------------------------
        |
        | Penjualan dapat memiliki banyak barang.
        |
        */

        if (
            $request->jenis_keluar === 'Penjualan'
        ) {

            $request->validate([

                'items' =>
                    'required|array|min:1',

                'items.*.barang_id' =>
                    'required|exists:barang,id',

                'items.*.satuan_id' =>
                    'required|exists:satuan,id',

                'items.*.jumlah' =>
                    'required|numeric|min:0.01',

                'items.*.harga_jual' =>
                    'required|numeric|min:0',

            ]);

        }

        /*
        |--------------------------------------------------------------------------
        | JIKA BUKAN PENJUALAN
        |--------------------------------------------------------------------------
        |
        | Tetap menggunakan satu barang seperti sistem lama.
        |
        */

        else {

            $request->validate([

                'barang_id' =>
                    'required|exists:barang,id',

                'jumlah' =>
                    'required|numeric|min:0.01',

                'satuan_id' =>
                    'required|exists:satuan,id',

            ]);

        }


        $barangKeluar = null;


        DB::transaction(function () use (
            $request,
            &$barangKeluar
        ) {

            /*
            |--------------------------------------------------------------------------
            | PENJUALAN MULTI BARANG
            |--------------------------------------------------------------------------
            */

            if (
                $request->jenis_keluar === 'Penjualan'
            ) {

                $items =
                    $request->input(
                        'items',
                        []
                    );


                /*
                |--------------------------------------------------------------------------
                | CEGAH BARANG DUPLIKAT
                |--------------------------------------------------------------------------
                |
                | Satu barang sebaiknya hanya muncul
                | satu kali dalam satu transaksi.
                |
                */

                $barangIds =
                    collect($items)
                    ->pluck('barang_id')
                    ->map(fn ($id) => (int) $id);


                if (
                    $barangIds->count()
                    !==
                    $barangIds->unique()->count()
                ) {

                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'items' =>
                            'Barang yang sama tidak boleh dimasukkan dua kali dalam satu transaksi penjualan.'
                    ]);

                }


                $details = [];

                $totalHarga = 0;


                /*
                |--------------------------------------------------------------------------
                | PROSES SETIAP BARANG
                |--------------------------------------------------------------------------
                */

                foreach ($items as $item) {

                    /*
                    |--------------------------------------------------------------------------
                    | LOCK BARANG
                    |--------------------------------------------------------------------------
                    */

                    $barang = Barang::with([
                        'satuan',
                        'konversiSatuan'
                    ])
                    ->where('status', 'Aktif')
                    ->lockForUpdate()
                    ->findOrFail(
                        $item['barang_id']
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | KONVERSI
                    |--------------------------------------------------------------------------
                    */

                    $nilaiKonversi = 1;


                    if (
                        (int) $item['satuan_id']
                        !==
                        (int) $barang->satuan_id
                    ) {

                        $konversi =
                            $barang
                            ->konversiSatuan
                            ->firstWhere(
                                'satuan_id',
                                $item['satuan_id']
                            );


                        if (!$konversi) {

                            throw \Illuminate\Validation\ValidationException::withMessages([
                                'items' =>
                                    'Konversi satuan untuk barang '
                                    . $barang->nama_barang
                                    . ' belum tersedia.'
                            ]);

                        }


                        $nilaiKonversi =
                            (float) $konversi->nilai_konversi;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | JUMLAH
                    |--------------------------------------------------------------------------
                    */

                    $jumlah =
                        (float) $item['jumlah'];


                    $jumlahDasar =
                        $jumlah * $nilaiKonversi;


                    /*
                    |--------------------------------------------------------------------------
                    | CEK STOK
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $jumlahDasar >
                        (float) $barang->stok
                    ) {

                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'items' =>
                                'Stok barang '
                                . $barang->nama_barang
                                . ' tidak mencukupi. Stok tersedia: '
                                . number_format(
                                    $barang->stok,
                                    2,
                                    ',',
                                    '.'
                                )
                                . ' '
                                . $barang->satuan->nama_satuan
                        ]);

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | HARGA
                    |--------------------------------------------------------------------------
                    */

                    $hargaJual =
                        (float) $item['harga_jual'];


                    $subtotal =
                        $jumlah * $hargaJual;


                    $totalHarga +=
                        $subtotal;


                    /*
                    |--------------------------------------------------------------------------
                    | SIMPAN DATA DETAIL SEMENTARA
                    |--------------------------------------------------------------------------
                    */

                    $details[] = [

                        'barang' =>
                            $barang,

                        'barang_id' =>
                            $barang->id,

                        'satuan_id' =>
                            $item['satuan_id'],

                        'jumlah' =>
                            $jumlah,

                        'nilai_konversi' =>
                            $nilaiKonversi,

                        'jumlah_dasar' =>
                            $jumlahDasar,

                        'harga_jual' =>
                            $hargaJual,

                        'subtotal' =>
                            $subtotal,

                    ];

                }


                /*
                |--------------------------------------------------------------------------
                | BUAT HEADER TRANSAKSI
                |--------------------------------------------------------------------------
                |
                | Field barang_id dan field lama tetap diisi
                | menggunakan barang pertama agar kompatibel
                | dengan struktur sistem lama.
                |
                */

                $detailPertama =
                    $details[0];


                $barangKeluar =
                    BarangKeluar::create([

                        'kode_transaksi' =>
                            $this->generateKode(),

                        'barang_id' =>
                            $detailPertama['barang_id'],

                        'pelanggan_id' =>
                            $request->pelanggan_id,

                        'tanggal_keluar' =>
                            $request->tanggal_keluar,

                        'jumlah' =>
                            $detailPertama['jumlah'],

                        'satuan_id' =>
                            $detailPertama['satuan_id'],

                        'nilai_konversi' =>
                            $detailPertama['nilai_konversi'],

                        'jumlah_dasar' =>
                            $detailPertama['jumlah_dasar'],

                        'harga_jual' =>
                            $detailPertama['harga_jual'],

                        'total_harga' =>
                            $totalHarga,

                        'jenis_keluar' =>
                            'Penjualan',

                        'tujuan' =>
                            $request->tujuan,

                        'keterangan' =>
                            $request->keterangan,

                        'user_id' =>
                            Auth::id(),

                    ]);


                /*
                |--------------------------------------------------------------------------
                | SIMPAN DETAIL + KURANGI STOK
                |--------------------------------------------------------------------------
                */

                foreach ($details as $detail) {

                    BarangKeluarDetail::create([

                        'barang_keluar_id' =>
                            $barangKeluar->id,

                        'barang_id' =>
                            $detail['barang_id'],

                        'satuan_id' =>
                            $detail['satuan_id'],

                        'jumlah' =>
                            $detail['jumlah'],

                        'nilai_konversi' =>
                            $detail['nilai_konversi'],

                        'jumlah_dasar' =>
                            $detail['jumlah_dasar'],

                        'harga_jual' =>
                            $detail['harga_jual'],

                        'subtotal' =>
                            $detail['subtotal'],

                    ]);


                    $detail['barang']->decrement(
                        'stok',
                        $detail['jumlah_dasar']
                    );

                }

            }


            /*
            |--------------------------------------------------------------------------
            | BUKAN PENJUALAN
            |--------------------------------------------------------------------------
            |
            | Tetap satu barang seperti sebelumnya.
            |
            */

            else {

                $barang = Barang::with([
                    'satuan',
                    'konversiSatuan'
                ])
                ->where('status', 'Aktif')
                ->lockForUpdate()
                ->findOrFail(
                    $request->barang_id
                );


                /*
                |--------------------------------------------------------------------------
                | KONVERSI
                |--------------------------------------------------------------------------
                */

                $nilaiKonversi = 1;


                if (
                    (int) $request->satuan_id
                    !==
                    (int) $barang->satuan_id
                ) {

                    $konversi =
                        $barang
                        ->konversiSatuan
                        ->firstWhere(
                            'satuan_id',
                            $request->satuan_id
                        );


                    if (!$konversi) {

                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'satuan_id' =>
                                'Konversi satuan untuk barang ini belum tersedia.'
                        ]);

                    }


                    $nilaiKonversi =
                        (float) $konversi->nilai_konversi;

                }


                /*
                |--------------------------------------------------------------------------
                | JUMLAH DASAR
                |--------------------------------------------------------------------------
                */

                $jumlah =
                    (float) $request->jumlah;


                $jumlahDasar =
                    $jumlah * $nilaiKonversi;


                /*
                |--------------------------------------------------------------------------
                | CEK STOK
                |--------------------------------------------------------------------------
                */

                if (
                    $jumlahDasar >
                    (float) $barang->stok
                ) {

                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'jumlah' =>
                            'Stok barang tidak mencukupi. '
                            . 'Stok tersedia: '
                            . number_format(
                                $barang->stok,
                                2,
                                ',',
                                '.'
                            )
                            . ' '
                            . $barang->satuan->nama_satuan
                    ]);

                }


                /*
                |--------------------------------------------------------------------------
                | SIMPAN TRANSAKSI
                |--------------------------------------------------------------------------
                */

                $barangKeluar =
                    BarangKeluar::create([

                        'kode_transaksi' =>
                            $this->generateKode(),

                        'barang_id' =>
                            $barang->id,

                        'pelanggan_id' =>
                            null,

                        'tanggal_keluar' =>
                            $request->tanggal_keluar,

                        'jumlah' =>
                            $jumlah,

                        'satuan_id' =>
                            $request->satuan_id,

                        'nilai_konversi' =>
                            $nilaiKonversi,

                        'jumlah_dasar' =>
                            $jumlahDasar,

                        'harga_jual' =>
                            0,

                        'total_harga' =>
                            0,

                        'jenis_keluar' =>
                            $request->jenis_keluar,

                        'tujuan' =>
                            $request->tujuan,

                        'keterangan' =>
                            $request->keterangan,

                        'user_id' =>
                            Auth::id(),

                    ]);


                /*
                |--------------------------------------------------------------------------
                | KURANGI STOK
                |--------------------------------------------------------------------------
                */

                $barang->decrement(
                    'stok',
                    $jumlahDasar
                );

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
                'Barang Keluar',

            'aktivitas' =>
                'CREATE',

            'deskripsi' =>
                'Menambahkan barang keluar '
                . $barangKeluar->kode_transaksi,

            'ip_address' =>
                $request->ip(),

        ]);


        return redirect()
            ->route('barang-keluar.index')
            ->with(
                'success',
                'Barang keluar berhasil disimpan dan stok diperbarui.'
            );
    }


    /**
     * Display the specified resource.
     */
    public function show(BarangKeluar $barangKeluar)
    {
        $barangKeluar->load([

            'barang.satuan',

            'barang.konversiSatuan.satuan',

            'details.barang.satuan',

            'details.barang.konversiSatuan.satuan',

            'details.satuan',

            'pelanggan',

            'user'

        ]);


        return view(
            'barang-keluar.show',
            compact('barangKeluar')
        );
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $barangKeluar =
            BarangKeluar::with([

                'barang.kategori',

                'barang.satuan',

                'barang.konversiSatuan.satuan',

                'details.barang.kategori',

                'details.barang.satuan',

                'details.barang.konversiSatuan.satuan',

                'details.satuan',

                'pelanggan',

                'user'

            ])
            ->findOrFail($id);


        $barang = Barang::with([

            'kategori',

            'supplier',

            'satuan',

            'konversiSatuan.satuan'

        ])
        ->where('status', 'Aktif')
        ->orderBy('nama_barang')
        ->get();


        $pelanggan =
            Pelanggan::orderBy(
                'nama_pelanggan'
            )->get();


        $satuan =
            Satuan::orderBy(
                'nama_satuan'
            )->get();


        return view(
            'barang-keluar.edit',
            compact(
                'barangKeluar',
                'barang',
                'pelanggan',
                'satuan'
            )
        );
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(
        Request $request,
        BarangKeluar $barangKeluar
    ) {

        /*
        |--------------------------------------------------------------------------
        | VALIDASI DASAR
        |--------------------------------------------------------------------------
        */

        $request->validate([

            'tanggal_keluar' =>
                'required|date',

            'jenis_keluar' =>
                'required|in:Transfer Ke Toko,Penjualan,Rusak,Pemakaian Internal,Kadaluarsa,Retur',

            'pelanggan_id' =>
                'nullable|exists:pelanggan,id',

            'tujuan' =>
                'nullable|string|max:255',

            'keterangan' =>
                'nullable|string',

        ]);


        /*
        |--------------------------------------------------------------------------
        | UPDATE PENJUALAN MULTI BARANG
        |--------------------------------------------------------------------------
        */

        if (
            $request->jenis_keluar === 'Penjualan'
        ) {

            $request->validate([

                'items' =>
                    'required|array|min:1',

                'items.*.barang_id' =>
                    'required|exists:barang,id',

                'items.*.satuan_id' =>
                    'required|exists:satuan,id',

                'items.*.jumlah' =>
                    'required|numeric|min:0.01',

                'items.*.harga_jual' =>
                    'required|numeric|min:0',

            ]);

        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE NON PENJUALAN
        |--------------------------------------------------------------------------
        */

        else {

            $request->validate([

                'barang_id' =>
                    'required|exists:barang,id',

                'jumlah' =>
                    'required|numeric|min:0.01',

                'satuan_id' =>
                    'required|exists:satuan,id',

            ]);

        }


        DB::transaction(function () use (
            $request,
            $barangKeluar
        ) {

            /*
            |--------------------------------------------------------------------------
            | 1. KEMBALIKAN STOK TRANSAKSI LAMA
            |--------------------------------------------------------------------------
            */

            if (
                $barangKeluar->jenis_keluar === 'Penjualan'
                &&
                $barangKeluar->details()->exists()
            ) {

                /*
                | Multi-item lama
                */

                $detailsLama =
                    $barangKeluar
                    ->details()
                    ->lockForUpdate()
                    ->get();


                foreach ($detailsLama as $detail) {

                    $barangLama =
                        Barang::lockForUpdate()
                        ->findOrFail(
                            $detail->barang_id
                        );


                    $barangLama->increment(
                        'stok',
                        $detail->jumlah_dasar
                    );

                }

            }

            else {

                /*
                | Transaksi lama satu barang
                */

                $barangLama =
                    Barang::lockForUpdate()
                    ->findOrFail(
                        $barangKeluar->barang_id
                    );


                $barangLama->increment(
                    'stok',
                    $barangKeluar->jumlah_dasar
                );

            }


            /*
            |--------------------------------------------------------------------------
            | 2. JIKA PENJUALAN
            |--------------------------------------------------------------------------
            */

            if (
                $request->jenis_keluar === 'Penjualan'
            ) {

                $items =
                    $request->input(
                        'items',
                        []
                    );


                /*
                |--------------------------------------------------------------------------
                | CEK DUPLIKAT
                |--------------------------------------------------------------------------
                */

                $barangIds =
                    collect($items)
                    ->pluck('barang_id')
                    ->map(fn ($id) => (int) $id);


                if (
                    $barangIds->count()
                    !==
                    $barangIds->unique()->count()
                ) {

                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'items' =>
                            'Barang yang sama tidak boleh dimasukkan dua kali dalam satu transaksi penjualan.'
                    ]);

                }


                /*
                |--------------------------------------------------------------------------
                | HAPUS DETAIL LAMA
                |--------------------------------------------------------------------------
                */

                $barangKeluar
                    ->details()
                    ->delete();


                $detailsBaru = [];

                $totalHarga = 0;


                /*
                |--------------------------------------------------------------------------
                | PROSES ITEM BARU
                |--------------------------------------------------------------------------
                */

                foreach ($items as $item) {

                    $barang =
                        Barang::with([
                            'satuan',
                            'konversiSatuan'
                        ])
                        ->where('status', 'Aktif')
                        ->lockForUpdate()
                        ->findOrFail(
                            $item['barang_id']
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | KONVERSI
                    |--------------------------------------------------------------------------
                    */

                    $nilaiKonversi = 1;


                    if (
                        (int) $item['satuan_id']
                        !==
                        (int) $barang->satuan_id
                    ) {

                        $konversi =
                            $barang
                            ->konversiSatuan
                            ->firstWhere(
                                'satuan_id',
                                $item['satuan_id']
                            );


                        if (!$konversi) {

                            throw \Illuminate\Validation\ValidationException::withMessages([
                                'items' =>
                                    'Konversi satuan untuk barang '
                                    . $barang->nama_barang
                                    . ' belum tersedia.'
                            ]);

                        }


                        $nilaiKonversi =
                            (float) $konversi->nilai_konversi;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | JUMLAH
                    |--------------------------------------------------------------------------
                    */

                    $jumlah =
                        (float) $item['jumlah'];


                    $jumlahDasar =
                        $jumlah * $nilaiKonversi;


                    /*
                    |--------------------------------------------------------------------------
                    | CEK STOK
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $jumlahDasar >
                        (float) $barang->stok
                    ) {

                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'items' =>
                                'Stok barang '
                                . $barang->nama_barang
                                . ' tidak mencukupi. Stok tersedia: '
                                . number_format(
                                    $barang->stok,
                                    2,
                                    ',',
                                    '.'
                                )
                                . ' '
                                . $barang->satuan->nama_satuan
                        ]);

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | HARGA
                    |--------------------------------------------------------------------------
                    */

                    $hargaJual =
                        (float) $item['harga_jual'];


                    $subtotal =
                        $jumlah * $hargaJual;


                    $totalHarga +=
                        $subtotal;


                    $detailsBaru[] = [

                        'barang' =>
                            $barang,

                        'barang_id' =>
                            $barang->id,

                        'satuan_id' =>
                            $item['satuan_id'],

                        'jumlah' =>
                            $jumlah,

                        'nilai_konversi' =>
                            $nilaiKonversi,

                        'jumlah_dasar' =>
                            $jumlahDasar,

                        'harga_jual' =>
                            $hargaJual,

                        'subtotal' =>
                            $subtotal,

                    ];

                }


                /*
                |--------------------------------------------------------------------------
                | 3. UPDATE HEADER
                |--------------------------------------------------------------------------
                */

                $pertama =
                    $detailsBaru[0];


                $barangKeluar->update([

                    'barang_id' =>
                        $pertama['barang_id'],

                    'pelanggan_id' =>
                        $request->pelanggan_id,

                    'tanggal_keluar' =>
                        $request->tanggal_keluar,

                    'jumlah' =>
                        $pertama['jumlah'],

                    'satuan_id' =>
                        $pertama['satuan_id'],

                    'nilai_konversi' =>
                        $pertama['nilai_konversi'],

                    'jumlah_dasar' =>
                        $pertama['jumlah_dasar'],

                    'harga_jual' =>
                        $pertama['harga_jual'],

                    'total_harga' =>
                        $totalHarga,

                    'jenis_keluar' =>
                        'Penjualan',

                    'tujuan' =>
                        $request->tujuan,

                    'keterangan' =>
                        $request->keterangan,

                ]);


                /*
                |--------------------------------------------------------------------------
                | 4. SIMPAN DETAIL BARU
                |--------------------------------------------------------------------------
                */

                foreach ($detailsBaru as $detail) {

                    BarangKeluarDetail::create([

                        'barang_keluar_id' =>
                            $barangKeluar->id,

                        'barang_id' =>
                            $detail['barang_id'],

                        'satuan_id' =>
                            $detail['satuan_id'],

                        'jumlah' =>
                            $detail['jumlah'],

                        'nilai_konversi' =>
                            $detail['nilai_konversi'],

                        'jumlah_dasar' =>
                            $detail['jumlah_dasar'],

                        'harga_jual' =>
                            $detail['harga_jual'],

                        'subtotal' =>
                            $detail['subtotal'],

                    ]);


                    $detail['barang']->decrement(
                        'stok',
                        $detail['jumlah_dasar']
                    );

                }

            }


            /*
            |--------------------------------------------------------------------------
            | 5. UPDATE NON PENJUALAN
            |--------------------------------------------------------------------------
            */

            else {

                $barangBaru =
                    Barang::with([
                        'satuan',
                        'konversiSatuan'
                    ])
                    ->where('status', 'Aktif')
                    ->lockForUpdate()
                    ->findOrFail(
                        $request->barang_id
                    );


                /*
                |--------------------------------------------------------------------------
                | KONVERSI
                |--------------------------------------------------------------------------
                */

                $nilaiKonversi = 1;


                if (
                    (int) $request->satuan_id
                    !==
                    (int) $barangBaru->satuan_id
                ) {

                    $konversi =
                        $barangBaru
                        ->konversiSatuan
                        ->where(
                            'satuan_id',
                            $request->satuan_id
                        )
                        ->first();


                    if (!$konversi) {

                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'satuan_id' =>
                                'Konversi satuan tidak ditemukan untuk barang ini.'
                        ]);

                    }


                    $nilaiKonversi =
                        (float) $konversi->nilai_konversi;

                }


                /*
                |--------------------------------------------------------------------------
                | JUMLAH
                |--------------------------------------------------------------------------
                */

                $jumlah =
                    (float) $request->jumlah;


                $jumlahDasar =
                    $jumlah * $nilaiKonversi;


                /*
                |--------------------------------------------------------------------------
                | CEK STOK
                |--------------------------------------------------------------------------
                */

                if (
                    $jumlahDasar >
                    (float) $barangBaru->stok
                ) {

                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'jumlah' =>
                            'Stok tidak mencukupi. Stok tersedia hanya '
                            . number_format(
                                $barangBaru->stok,
                                2,
                                ',',
                                '.'
                            )
                            . ' '
                            . $barangBaru->satuan->nama_satuan
                    ]);

                }


                /*
                |--------------------------------------------------------------------------
                | UPDATE TRANSAKSI
                |--------------------------------------------------------------------------
                */

                $barangKeluar->update([

                    'barang_id' =>
                        $barangBaru->id,

                    'pelanggan_id' =>
                        null,

                    'tanggal_keluar' =>
                        $request->tanggal_keluar,

                    'jumlah' =>
                        $jumlah,

                    'satuan_id' =>
                        $request->satuan_id,

                    'nilai_konversi' =>
                        $nilaiKonversi,

                    'jumlah_dasar' =>
                        $jumlahDasar,

                    'harga_jual' =>
                        0,

                    'total_harga' =>
                        0,

                    'jenis_keluar' =>
                        $request->jenis_keluar,

                    'tujuan' =>
                        $request->tujuan,

                    'keterangan' =>
                        $request->keterangan,

                ]);


                /*
                |--------------------------------------------------------------------------
                | KURANGI STOK
                |--------------------------------------------------------------------------
                */

                $barangBaru->decrement(
                    'stok',
                    $jumlahDasar
                );

            }

        });


        return redirect()
            ->route(
                'barang-keluar.show',
                $barangKeluar
            )
            ->with(
                'success',
                'Data barang keluar berhasil diperbarui.'
            );
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        BarangKeluar $barangKeluar
    ) {

        DB::transaction(function () use (
            $barangKeluar
        ) {

            /*
            |--------------------------------------------------------------------------
            | JIKA PENJUALAN MULTI BARANG
            |--------------------------------------------------------------------------
            */

            if (
                $barangKeluar->jenis_keluar === 'Penjualan'
                &&
                $barangKeluar->details()->exists()
            ) {

                $details =
                    $barangKeluar
                    ->details()
                    ->lockForUpdate()
                    ->get();


                foreach ($details as $detail) {

                    $barang =
                        Barang::lockForUpdate()
                        ->findOrFail(
                            $detail->barang_id
                        );


                    $barang->increment(
                        'stok',
                        $detail->jumlah_dasar
                    );

                }


                $barangKeluar->details()->delete();

            }

            /*
            |--------------------------------------------------------------------------
            | TRANSAKSI LAMA / NON PENJUALAN
            |--------------------------------------------------------------------------
            */

            else {

                $barang =
                    Barang::lockForUpdate()
                    ->findOrFail(
                        $barangKeluar->barang_id
                    );


                $barang->increment(
                    'stok',
                    $barangKeluar->jumlah_dasar
                );

            }


            /*
            |--------------------------------------------------------------------------
            | HAPUS HEADER
            |--------------------------------------------------------------------------
            */

            $barangKeluar->delete();

        });


        return redirect()
            ->route(
                'barang-keluar.index'
            )
            ->with(
                'success',
                'Data barang keluar berhasil dihapus dan stok dikembalikan.'
            );
    }


    /**
     * Generate transaction code.
     */
    private function generateKode()
    {
        $last =
            BarangKeluar::latest('id')
            ->first();


        if (!$last) {

            return 'BK0001';

        }


        $nomor =
            (int) substr(
                $last->kode_transaksi,
                2
            );


        return 'BK'
            . str_pad(
                $nomor + 1,
                4,
                '0',
                STR_PAD_LEFT
            );
    }


    public function suratJalan(BarangKeluar $barangKeluar)
    {
        $barangKeluar->load([
            'pelanggan',
            'user',
            'details.barang.satuan',
            'details.satuan',
        ]);

        if ($barangKeluar->jenis_keluar !== 'Penjualan') {
            abort(404);
        }

        return view('barang-keluar.surat-jalan', compact('barangKeluar'));
    }


    public function invoice(BarangKeluar $barangKeluar)
    {
        $barangKeluar->load([
            'pelanggan',
            'user',
            'details.barang.satuan',
            'details.satuan',
        ]);

        if ($barangKeluar->jenis_keluar !== 'Penjualan') {
            abort(404);
        }

        return view('barang-keluar.invoice', compact('barangKeluar'));
    }
}