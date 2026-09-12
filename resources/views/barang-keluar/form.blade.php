{{-- resources/views/barang-keluar/form.blade.php --}}

@php
$selectedBarangId = old('barang_id', $barangKeluar->barang_id ?? '');
$selectedSatuanId = old('satuan_id', $barangKeluar->satuan_id ?? '');
$selectedJenis = old('jenis_keluar', $barangKeluar->jenis_keluar ?? '');

$selectedTanggal = old(
    'tanggal_keluar',
    isset($barangKeluar->tanggal_keluar)
        ? \Carbon\Carbon::parse($barangKeluar->tanggal_keluar)->format('Y-m-d')
        : date('Y-m-d')
);

/*
|--------------------------------------------------------------------------
| DATA ITEM PENJUALAN
|--------------------------------------------------------------------------
*/

$oldItems = old('items');

if ($oldItems) {
    $penjualanItems = $oldItems;
} elseif (
    isset($barangKeluar) &&
    $barangKeluar->jenis_keluar === 'Penjualan' &&
    $barangKeluar->details &&
    $barangKeluar->details->count()
) {
    $penjualanItems = $barangKeluar->details->map(function ($detail) {
        return [
            'barang_id' => $detail->barang_id,
            'satuan_id' => $detail->satuan_id,
            'jumlah' => $detail->jumlah,
            'harga_jual' => $detail->harga_jual,
        ];
    })->toArray();
} elseif (
    isset($barangKeluar) &&
    $barangKeluar->jenis_keluar === 'Penjualan'
) {
    $penjualanItems = [
        [
            'barang_id' => $barangKeluar->barang_id,
            'satuan_id' => $barangKeluar->satuan_id,
            'jumlah' => $barangKeluar->jumlah,
            'harga_jual' => $barangKeluar->harga_jual,
        ]
    ];
} else {
    $penjualanItems = [
        [
            'barang_id' => '',
            'satuan_id' => '',
            'jumlah' => '',
            'harga_jual' => '',
        ]
    ];
}

@endphp

{{-- ========================================================= --}}
{{-- KODE TRANSAKSI --}}
{{-- ========================================================= --}}

<div class="mb-3">
    <label for="kode_transaksi" class="form-label">
        Kode Transaksi
    </label>

<input
    type="text"
    id="kode_transaksi"
    class="form-control"
    value="{{ old('kode_transaksi', $barangKeluar->kode_transaksi ?? $kode ?? '') }}"
    readonly
>

</div>

{{-- ========================================================= --}}
{{-- JENIS PENGELUARAN --}}
{{-- ========================================================= --}}

<div class="mb-3">
    <label for="jenis_keluar" class="form-label">
        Jenis Pengeluaran <span class="text-danger">*</span>
    </label>

<select
    name="jenis_keluar"
    id="jenis_keluar"
    class="form-select @error('jenis_keluar') is-invalid @enderror"
    required
>
    <option value="">-- Pilih Jenis Pengeluaran --</option>

    <option
        value="Transfer Ke Toko"
        @selected($selectedJenis === 'Transfer Ke Toko')
    >
        Transfer Ke Toko
    </option>

    <option
        value="Penjualan"
        @selected($selectedJenis === 'Penjualan')
    >
        Penjualan
    </option>

    <option
        value="Rusak"
        @selected($selectedJenis === 'Rusak')
    >
        Rusak
    </option>

    <option
        value="Pemakaian Internal"
        @selected($selectedJenis === 'Pemakaian Internal')
    >
        Pemakaian Internal
    </option>

    <option
        value="Kadaluarsa"
        @selected($selectedJenis === 'Kadaluarsa')
    >
        Kadaluarsa
    </option>

    <option
        value="Retur"
        @selected($selectedJenis === 'Retur')
    >
        Retur
    </option>
</select>

@error('jenis_keluar')
    <div class="invalid-feedback">
        {{ $message }}
    </div>
@enderror

</div>

{{-- ========================================================= --}}
{{-- AREA BARANG NON PENJUALAN --}}
{{-- ========================================================= --}}

<div id="single-barang-wrapper">

<div class="mb-3">
    <label for="barang_id" class="form-label">
        Barang <span class="text-danger">*</span>
    </label>

    <div class="input-group mb-2">

        <select
            name="barang_id"
            id="barang_id"
            class="form-select @error('barang_id') is-invalid @enderror"
        >
            <option value="">-- Pilih Barang --</option>

            @foreach ($barang as $item)
                <option
                    value="{{ $item->id }}"
                    data-kode="{{ $item->kode_barang }}"
                    data-nama="{{ $item->nama_barang }}"
                    data-kategori="{{ $item->kategori->nama_kategori ?? '-' }}"
                    data-satuan-id="{{ $item->satuan_id }}"
                    data-satuan="{{ $item->satuan->kode_satuan ?? $item->satuan->nama_satuan ?? '-' }}"
                    data-stok="{{ $item->stok }}"
                    data-rak="{{ $item->lokasi_rak ?? '-' }}"
                    data-konversi='@json($item->konversiSatuan)'
                    @selected($selectedBarangId == $item->id)
                >
                    {{ $item->kode_barang }} - {{ $item->nama_barang }}
                </option>
            @endforeach
        </select>

        <button
            type="button"
            class="btn btn-dark"
            id="btnScanQr"
        >
            <i class="fas fa-qrcode me-1"></i>
            Scan QR
        </button>

    </div>

    @error('barang_id')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror


    {{-- CARI KODE --}}
    <div class="input-group">

        <input
            type="text"
            id="inputKodeBarang"
            class="form-control"
            placeholder="Masukkan kode barang, contoh: BRG0001"
        >

        <button
            type="button"
            class="btn btn-primary"
            id="btnCariKode"
        >
            <i class="fas fa-search me-1"></i>
            Cari Kode
        </button>

    </div>

    <small class="form-text text-muted">
        Pilih barang secara manual, scan QR, atau cari berdasarkan kode barang.
    </small>

    <div
        id="kodeSearchMessage"
        class="mt-2"
        style="display:none;"
    ></div>

</div>


{{-- ===================================================== --}}
{{-- QR SCANNER --}}
{{-- ===================================================== --}}

<div
    id="qrScannerContainer"
    class="card border mb-3"
    style="display:none;"
>

    <div class="card-header bg-dark text-white">
        <strong>
            <i class="fas fa-camera me-1"></i>
            Scan QR Barang
        </strong>
    </div>

    <div class="card-body text-center">

        <div
            id="qr-reader"
            style="width:500px; max-width:100%; margin:0 auto;"
        ></div>

        <div
            id="qrMessage"
            class="alert alert-info mt-3 mb-0"
        >
            Arahkan kamera ke QR Code barang.
        </div>

        <button
            type="button"
            class="btn btn-danger mt-3"
            id="btnCloseScanner"
        >
            <i class="fas fa-times me-1"></i>
            Tutup Scanner
        </button>

    </div>

</div>


{{-- ===================================================== --}}
{{-- INFORMASI BARANG --}}
{{-- ===================================================== --}}

<div class="card border mb-3">

    <div class="card-header bg-light">
        <strong>
            <i class="fas fa-info-circle me-1"></i>
            Informasi Barang
        </strong>
    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-4 mb-3">
                <label class="form-label">
                    Kode Barang
                </label>

                <input
                    type="text"
                    id="kode_barang"
                    class="form-control"
                    readonly
                >
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">
                    Kategori
                </label>

                <input
                    type="text"
                    id="kategori_barang"
                    class="form-control"
                    readonly
                >
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">
                    Satuan Dasar
                </label>

                <input
                    type="text"
                    id="satuan_barang"
                    class="form-control"
                    readonly
                >
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">
                    Stok Saat Ini
                </label>

                <input
                    type="text"
                    id="stok_barang"
                    class="form-control"
                    readonly
                >
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">
                    Lokasi Rak
                </label>

                <input
                    type="text"
                    id="rak_barang"
                    class="form-control"
                    readonly
                >
            </div>

        </div>

    </div>

</div>


{{-- ===================================================== --}}
{{-- JUMLAH NON PENJUALAN --}}
{{-- ===================================================== --}}

<div class="row mb-3">

    <div class="col-md-4">

        <label for="jumlah" class="form-label">
            Jumlah Keluar <span class="text-danger">*</span>
        </label>

        <input
            type="number"
            name="jumlah"
            id="jumlah"
            class="form-control @error('jumlah') is-invalid @enderror"
            min="0.01"
            step="0.01"
            value="{{ old('jumlah', $barangKeluar->jumlah ?? '') }}"
        >

        @error('jumlah')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="col-md-4">

        <label for="satuan_id" class="form-label">
            Satuan Keluar <span class="text-danger">*</span>
        </label>

        <select
            name="satuan_id"
            id="satuan_id"
            class="form-select @error('satuan_id') is-invalid @enderror"
        >
            <option value="">
                -- Pilih Barang Dahulu --
            </option>
        </select>

        @error('satuan_id')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="col-md-4">

        <label class="form-label">
            Maksimal Pengeluaran
        </label>

        <input
            type="text"
            id="batas_pengeluaran"
            class="form-control"
            readonly
        >

    </div>

</div>


{{-- ===================================================== --}}
{{-- KONVERSI NON PENJUALAN --}}
{{-- ===================================================== --}}

<div
    id="konversi-wrapper"
    class="alert alert-info border mb-3 d-none"
>

    <div class="mb-2">
        <strong>
            <i class="fas fa-exchange-alt me-1"></i>
            Konversi Satuan
        </strong>
    </div>

    <div class="row">

        <div class="col-md-6">

            <small class="text-muted">
                Konversi:
            </small>

            <div>
                <strong id="text-konversi">
                    -
                </strong>
            </div>

        </div>

        <div class="col-md-6">

            <small class="text-muted">
                Jumlah dalam Satuan Dasar:
            </small>

            <div>
                <strong id="jumlah-dasar-preview">
                    0
                </strong>

                <span id="satuan-dasar-preview">
                    -
                </span>
            </div>

        </div>

    </div>

</div>


<input
    type="hidden"
    name="nilai_konversi"
    id="nilai_konversi"
    value="{{ old('nilai_konversi', $barangKeluar->nilai_konversi ?? 1) }}"
>

<input
    type="hidden"
    name="jumlah_dasar"
    id="jumlah_dasar"
    value="{{ old('jumlah_dasar', $barangKeluar->jumlah_dasar ?? '') }}"
>

</div>

{{-- ========================================================= --}}
{{-- TANGGAL --}}
{{-- ========================================================= --}}

<div class="mb-3">

<label for="tanggal_keluar" class="form-label">
    Tanggal Keluar <span class="text-danger">*</span>
</label>

<input
    type="date"
    name="tanggal_keluar"
    id="tanggal_keluar"
    class="form-control @error('tanggal_keluar') is-invalid @enderror"
    value="{{ $selectedTanggal }}"
    required
>

@error('tanggal_keluar')
    <div class="invalid-feedback">
        {{ $message }}
    </div>
@enderror

</div>

{{-- ========================================================= --}}
{{-- PENJUALAN MULTI ITEM --}}
{{-- ========================================================= --}}

<div
    id="penjualan-wrapper"
    class="card border mb-3 d-none"
>

<div class="card-header bg-light d-flex justify-content-between align-items-center">

    <strong>
        <i class="fas fa-shopping-cart me-1"></i>
        Detail Penjualan
    </strong>

    <button
        type="button"
        class="btn btn-sm btn-primary"
        id="btnTambahBarang"
    >
        <i class="fas fa-plus me-1"></i>
        Tambah Barang
    </button>

</div>


<div class="card-body">

    {{-- PELANGGAN --}}

    <div class="row">

        <div class="col-md-6 mb-3">

            <label
                for="pelanggan_id"
                class="form-label"
            >
                Pelanggan <span class="text-danger">*</span>
            </label>

            <select
                name="pelanggan_id"
                id="pelanggan_id"
                class="form-select @error('pelanggan_id') is-invalid @enderror"
            >

                <option value="">
                    -- Pilih Pelanggan --
                </option>

                @foreach ($pelanggan as $customer)

                    <option
                        value="{{ $customer->id }}"
                        @selected(
                            old(
                                'pelanggan_id',
                                $barangKeluar->pelanggan_id ?? ''
                            ) == $customer->id
                        )
                    >
                        {{ $customer->nama_pelanggan }}
                    </option>

                @endforeach

            </select>

            @error('pelanggan_id')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

    </div>


    {{-- ================================================= --}}
    {{-- CONTAINER ITEM --}}
    {{-- ================================================= --}}

    <div id="penjualan-items">

        @foreach ($penjualanItems as $index => $item)

            <div
                class="penjualan-item border rounded p-3 mb-3"
                data-index="{{ $index }}"
            >

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <strong>
                        Barang #{{ $index + 1 }}
                    </strong>

                    <button
                        type="button"
                        class="btn btn-sm btn-outline-danger btn-hapus-item"
                        {{ count($penjualanItems) <= 1 ? 'disabled' : '' }}
                    >
                        <i class="fas fa-trash"></i>
                    </button>

                </div>


                <div class="row">

                    {{-- BARANG --}}

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Barang <span class="text-danger">*</span>
                        </label>

                        <select
                            name="items[{{ $index }}][barang_id]"
                            class="form-select item-barang"
                            required
                        >

                            <option value="">
                                -- Pilih Barang --
                            </option>

                            @foreach ($barang as $barangItem)

                                <option
                                    value="{{ $barangItem->id }}"
                                    data-kode="{{ $barangItem->kode_barang }}"
                                    data-kategori="{{ $barangItem->kategori->nama_kategori ?? '-' }}"
                                    data-satuan-id="{{ $barangItem->satuan_id }}"
                                    data-satuan="{{ $barangItem->satuan->kode_satuan ?? $barangItem->satuan->nama_satuan ?? '-' }}"
                                    data-stok="{{ $barangItem->stok }}"
                                    data-rak="{{ $barangItem->lokasi_rak ?? '-' }}"
                                    data-konversi='@json($barangItem->konversiSatuan)'
                                    @selected(($item['barang_id'] ?? '') == $barangItem->id)
                                >
                                    {{ $barangItem->kode_barang }} - {{ $barangItem->nama_barang }}
                                </option>

                            @endforeach

                        </select>

                        @error("items.$index.barang_id")
                            <div class="text-danger small mt-1">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- SATUAN --}}

                    <div class="col-md-3 mb-3">

                        <label class="form-label">
                            Satuan <span class="text-danger">*</span>
                        </label>

                        <select
                            name="items[{{ $index }}][satuan_id]"
                            class="form-select item-satuan"
                            required
                        >
                            <option value="">
                                -- Pilih Barang --
                            </option>
                        </select>

                        @error("items.$index.satuan_id")
                            <div class="text-danger small mt-1">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- JUMLAH --}}

                    <div class="col-md-3 mb-3">

                        <label class="form-label">
                            Jumlah <span class="text-danger">*</span>
                        </label>

                        <input
                            type="number"
                            name="items[{{ $index }}][jumlah]"
                            class="form-control item-jumlah"
                            min="0.01"
                            step="0.01"
                            value="{{ $item['jumlah'] ?? '' }}"
                            required
                        >

                        @error("items.$index.jumlah")
                            <div class="text-danger small mt-1">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- HARGA --}}

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Harga Jual / Satuan
                            <span class="text-danger">*</span>
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                Rp
                            </span>

                            <input
                                type="number"
                                name="items[{{ $index }}][harga_jual]"
                                class="form-control item-harga"
                                min="0"
                                step="1"
                                value="{{ $item['harga_jual'] ?? '' }}"
                                required
                            >

                        </div>

                        @error("items.$index.harga_jual")
                            <div class="text-danger small mt-1">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- MAKSIMAL --}}

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Maksimal Pengeluaran
                        </label>

                        <input
                            type="text"
                            class="form-control item-batas"
                            readonly
                        >

                    </div>


                    {{-- SUBTOTAL --}}

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Subtotal
                        </label>

                        <input
                            type="text"
                            class="form-control item-subtotal"
                            value="Rp 0"
                            readonly
                        >

                    </div>

                </div>


                {{-- KONVERSI ITEM --}}

                <div class="alert alert-info item-konversi-wrapper d-none mb-0">

                    <div class="row">

                        <div class="col-md-6">

                            <small class="text-muted">
                                Konversi:
                            </small>

                            <div>
                                <strong class="item-text-konversi">
                                    -
                                </strong>
                            </div>

                        </div>

                        <div class="col-md-6">

                            <small class="text-muted">
                                Jumlah dalam Satuan Dasar:
                            </small>

                            <div>
                                <strong class="item-jumlah-dasar-preview">
                                    0
                                </strong>

                                <span class="item-satuan-dasar-preview">
                                    -
                                </span>
                            </div>

                        </div>

                    </div>

                </div>


                {{-- HIDDEN KONVERSI --}}

                <input
                    type="hidden"
                    name="items[{{ $index }}][nilai_konversi]"
                    class="item-nilai-konversi"
                    value="1"
                >

                <input
                    type="hidden"
                    name="items[{{ $index }}][jumlah_dasar]"
                    class="item-jumlah-dasar"
                    value=""
                >

            </div>

        @endforeach

    </div>


    {{-- TOTAL --}}

    <div class="alert alert-success mb-0">

        <div class="d-flex justify-content-between align-items-center">

            <span>
                <strong>Total Harga Penjualan</strong>
            </span>

            <strong
                id="total-harga-penjualan"
                class="fs-5"
            >
                Rp 0
            </strong>

        </div>

        <small class="text-muted">
            Total dihitung dari seluruh subtotal barang yang dijual.
        </small>

    </div>

</div>

</div>

{{-- ========================================================= --}}
{{-- TUJUAN --}}
{{-- ========================================================= --}}

<div class="mb-3">

<label for="tujuan" class="form-label">
    Tujuan Pengeluaran Barang
</label>

<input
    type="text"
    name="tujuan"
    id="tujuan"
    class="form-control @error('tujuan') is-invalid @enderror"
    value="{{ old('tujuan', $barangKeluar->tujuan ?? '') }}"
    maxlength="100"
    placeholder="Contoh: Toko Utama / Bagian Produksi / Pelanggan"
>

@error('tujuan')
    <div class="invalid-feedback">
        {{ $message }}
    </div>
@enderror

</div>

{{-- ========================================================= --}}
{{-- KETERANGAN --}}
{{-- ========================================================= --}}

<div class="mb-3">

<label for="keterangan" class="form-label">
    Keterangan
</label>

<textarea
    name="keterangan"
    id="keterangan"
    rows="3"
    class="form-control @error('keterangan') is-invalid @enderror"
    placeholder="Tambahkan keterangan jika diperlukan..."
>{{ old('keterangan', $barangKeluar->keterangan ?? '') }}</textarea>

@error('keterangan')
    <div class="invalid-feedback">
        {{ $message }}
    </div>
@enderror

</div>

@push('js')

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | ELEMENT SINGLE ITEM
    |--------------------------------------------------------------------------
    */

    const barangSelect =
        document.getElementById('barang_id');

    const satuanSelect =
        document.getElementById('satuan_id');

    const jumlahInput =
        document.getElementById('jumlah');

    const jenisSelect =
        document.getElementById('jenis_keluar');

    const singleBarangWrapper =
        document.getElementById('single-barang-wrapper');


    /*
    |--------------------------------------------------------------------------
    | ELEMENT PENJUALAN
    |--------------------------------------------------------------------------
    */

    const penjualanWrapper =
        document.getElementById('penjualan-wrapper');

    const penjualanItems =
        document.getElementById('penjualan-items');

    const btnTambahBarang =
        document.getElementById('btnTambahBarang');

    const pelangganSelect =
        document.getElementById('pelanggan_id');

    const totalHargaPenjualan =
        document.getElementById('total-harga-penjualan');


    /*
    |--------------------------------------------------------------------------
    | ELEMENT INFORMASI SINGLE
    |--------------------------------------------------------------------------
    */

    const kodeBarang =
        document.getElementById('kode_barang');

    const kategoriBarang =
        document.getElementById('kategori_barang');

    const satuanBarang =
        document.getElementById('satuan_barang');

    const stokBarang =
        document.getElementById('stok_barang');

    const rakBarang =
        document.getElementById('rak_barang');

    const batasPengeluaran =
        document.getElementById('batas_pengeluaran');

    const konversiWrapper =
        document.getElementById('konversi-wrapper');

    const textKonversi =
        document.getElementById('text-konversi');

    const jumlahDasarPreview =
        document.getElementById('jumlah-dasar-preview');

    const satuanDasarPreview =
        document.getElementById('satuan-dasar-preview');

    const nilaiKonversiInput =
        document.getElementById('nilai_konversi');

    const jumlahDasarInput =
        document.getElementById('jumlah_dasar');


    /*
    |--------------------------------------------------------------------------
    | SEARCH / QR
    |--------------------------------------------------------------------------
    */

    const inputKodeBarang =
        document.getElementById('inputKodeBarang');

    const btnCariKode =
        document.getElementById('btnCariKode');

    const kodeSearchMessage =
        document.getElementById('kodeSearchMessage');

    const btnScanQr =
        document.getElementById('btnScanQr');

    const btnCloseScanner =
        document.getElementById('btnCloseScanner');

    const qrScannerContainer =
        document.getElementById('qrScannerContainer');

    const qrMessage =
        document.getElementById('qrMessage');


    const initialSatuanId =
        '{{ (string) $selectedSatuanId }}';


    let firstLoad = true;

    let html5QrCode = null;

    let scannerRunning = false;

    let sedangMemproses = false;

    let itemCounter =
        {{ count($penjualanItems) }};


    /*
    |--------------------------------------------------------------------------
    | FORMAT RUPIAH
    |--------------------------------------------------------------------------
    */

    function formatRupiah(number) {

        return 'Rp ' + Number(number || 0)
            .toLocaleString('id-ID');

    }


    /*
    |--------------------------------------------------------------------------
    | FORMAT KONVERSI
    |--------------------------------------------------------------------------
    */

    function getKonversi(option) {

        if (!option) {
            return [];
        }

        try {

            return JSON.parse(
                option.dataset.konversi || '[]'
            );

        } catch (error) {

            console.error(
                'Data konversi tidak valid:',
                error
            );

            return [];

        }

    }


    /*
    |--------------------------------------------------------------------------
    | SINGLE ITEM
    |--------------------------------------------------------------------------
    */

    function tampilkanDataBarang() {

        const option =
            barangSelect.options[
                barangSelect.selectedIndex
            ];


        if (!option || !option.value) {

            kodeBarang.value = '';
            kategoriBarang.value = '';
            satuanBarang.value = '';
            stokBarang.value = '';
            rakBarang.value = '';

            satuanSelect.innerHTML =
                '<option value="">-- Pilih Barang Dahulu --</option>';

            batasPengeluaran.value = '';

            konversiWrapper.classList.add('d-none');

            nilaiKonversiInput.value = 1;

            jumlahDasarInput.value = '';

            jumlahDasarPreview.textContent =
                '0';

            satuanDasarPreview.textContent =
                '-';

            return;

        }


        const kode =
            option.dataset.kode || '-';

        const kategori =
            option.dataset.kategori || '-';

        const satuanDasar =
            option.dataset.satuan || '-';

        const stokDasar =
            Number(option.dataset.stok || 0);

        const satuanDasarId =
            option.dataset.satuanId;


        kodeBarang.value =
            kode;

        kategoriBarang.value =
            kategori;

        satuanBarang.value =
            satuanDasar;

        stokBarang.value =
            stokDasar.toLocaleString('id-ID')
            + ' '
            + satuanDasar;

        rakBarang.value =
            option.dataset.rak || '-';


        satuanSelect.innerHTML = '';


        /*
        | SATUAN DASAR
        */

        const baseOption =
            document.createElement('option');

        baseOption.value =
            satuanDasarId;

        baseOption.textContent =
            satuanDasar;

        baseOption.dataset.nilai =
            '1';

        satuanSelect.appendChild(
            baseOption
        );


        /*
        | SATUAN KONVERSI
        */

        const konversi =
            getKonversi(option);


        konversi.forEach(function (item) {

            if (
                String(item.satuan_id)
                ===
                String(satuanDasarId)
            ) {
                return;
            }


            const namaSatuan =
                item.satuan?.kode_satuan
                ||
                item.satuan?.nama_satuan
                ||
                ('Satuan #' + item.satuan_id);


            const satuanOption =
                document.createElement('option');

            satuanOption.value =
                item.satuan_id;

            satuanOption.textContent =
                namaSatuan;

            satuanOption.dataset.nilai =
                item.nilai_konversi;

            satuanSelect.appendChild(
                satuanOption
            );

        });


        /*
        | PERTAHANKAN SATUAN EDIT
        */

        if (
            firstLoad
            &&
            initialSatuanId
        ) {

            const exists =
                Array.from(
                    satuanSelect.options
                ).some(function (item) {

                    return String(item.value)
                        ===
                        String(initialSatuanId);

                });


            if (exists) {

                satuanSelect.value =
                    initialSatuanId;

            }

        } else {

            satuanSelect.value =
                satuanDasarId;

        }


        updateKonversi();

    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE KONVERSI SINGLE
    |--------------------------------------------------------------------------
    */

    function updateKonversi() {

        const optionBarang =
            barangSelect.options[
                barangSelect.selectedIndex
            ];


        if (
            !optionBarang
            ||
            !optionBarang.value
        ) {
            return;
        }


        const optionSatuan =
            satuanSelect.options[
                satuanSelect.selectedIndex
            ];


        if (!optionSatuan) {
            return;
        }


        const nilai =
            Number(
                optionSatuan.dataset.nilai || 1
            );


        const namaSatuanTransaksi =
            optionSatuan.textContent;


        const namaSatuanDasar =
            optionBarang.dataset.satuan || '-';


        const stokDasar =
            Number(
                optionBarang.dataset.stok || 0
            );


        nilaiKonversiInput.value =
            nilai;


        const maxTransaksi =
            Math.floor(
                stokDasar / nilai
            );


        jumlahInput.max =
            maxTransaksi;


        batasPengeluaran.value =
            maxTransaksi
            + ' '
            + namaSatuanTransaksi;


        textKonversi.textContent =
            '1 '
            + namaSatuanTransaksi
            + ' = '
            + nilai
            + ' '
            + namaSatuanDasar;


        konversiWrapper.classList.remove(
            'd-none'
        );


        hitungJumlahDasar();

    }


    /*
    |--------------------------------------------------------------------------
    | JUMLAH DASAR SINGLE
    |--------------------------------------------------------------------------
    */

    function hitungJumlahDasar() {

        const jumlah =
            Number(
                jumlahInput.value || 0
            );


        const nilai =
            Number(
                nilaiKonversiInput.value || 1
            );


        const jumlahDasar =
            jumlah * nilai;


        jumlahDasarInput.value =
            jumlahDasar;


        jumlahDasarPreview.textContent =
            jumlahDasar.toLocaleString(
                'id-ID'
            );


        const optionBarang =
            barangSelect.options[
                barangSelect.selectedIndex
            ];


        if (optionBarang) {

            satuanDasarPreview.textContent =
                optionBarang.dataset.satuan
                || '-';

        }

    }


    /*
    |--------------------------------------------------------------------------
    | PENJUALAN
    |--------------------------------------------------------------------------
    */

    function tampilkanPenjualan() {

        const isPenjualan =
            jenisSelect.value === 'Penjualan';

        /*
        |--------------------------------------------------------------------------
        | MODE PENJUALAN
        |--------------------------------------------------------------------------
        */

        if (isPenjualan) {

            // Sembunyikan input single item
            singleBarangWrapper.classList.add('d-none');

            // Tampilkan multi item
            penjualanWrapper.classList.remove('d-none');

            // Pelanggan wajib diisi
            pelangganSelect.required = true;
            pelangganSelect.disabled = false;

            // Matikan input single item
            barangSelect.required = false;
            satuanSelect.required = false;
            jumlahInput.required = false;

            barangSelect.disabled = true;
            satuanSelect.disabled = true;
            jumlahInput.disabled = true;
            nilaiKonversiInput.disabled = true;
            jumlahDasarInput.disabled = true;

            // Aktifkan semua input Penjualan
            document
                .querySelectorAll('#penjualan-items input, #penjualan-items select')
                .forEach(function (input) {
                    input.disabled = false;
                });

            // Pastikan required item aktif
            document
                .querySelectorAll('#penjualan-items .item-barang')
                .forEach(function (input) {
                    input.required = true;
                });

            document
                .querySelectorAll('#penjualan-items .item-satuan')
                .forEach(function (input) {
                    input.required = true;
                });

            document
                .querySelectorAll('#penjualan-items .item-jumlah')
                .forEach(function (input) {
                    input.required = true;
                });

            document
                .querySelectorAll('#penjualan-items .item-harga')
                .forEach(function (input) {
                    input.required = true;
                });

            initSemuaItemPenjualan();
            hitungTotalPenjualan();

        }

        /*
        |--------------------------------------------------------------------------
        | MODE SELAIN PENJUALAN
        |--------------------------------------------------------------------------
        */

        else {

            // Tampilkan input single item
            singleBarangWrapper.classList.remove('d-none');

            // Sembunyikan multi item
            penjualanWrapper.classList.add('d-none');

            // Pelanggan tidak digunakan
            pelangganSelect.required = false;
            pelangganSelect.disabled = true;

            // Aktifkan input single item
            barangSelect.disabled = false;
            satuanSelect.disabled = false;
            jumlahInput.disabled = false;
            nilaiKonversiInput.disabled = false;
            jumlahDasarInput.disabled = false;

            barangSelect.required = true;
            satuanSelect.required = true;
            jumlahInput.required = true;

            /*
            |--------------------------------------------------------------------------
            | PENTING:
            | Disable semua input Penjualan supaya browser tidak
            | memvalidasi required field yang sedang tersembunyi.
            |--------------------------------------------------------------------------
            */

            document
                .querySelectorAll('#penjualan-items input, #penjualan-items select')
                .forEach(function (input) {

                    input.disabled = true;
                    input.required = false;

                });

        }

    }


    /*
    |--------------------------------------------------------------------------
    | INIT SEMUA ITEM PENJUALAN
    |--------------------------------------------------------------------------
    */

    function initSemuaItemPenjualan() {

        document
            .querySelectorAll('.penjualan-item')
            .forEach(function (row) {

                initItemPenjualan(row);

            });

    }


    /*
    |--------------------------------------------------------------------------
    | INIT ITEM PENJUALAN
    |--------------------------------------------------------------------------
    */

    function initItemPenjualan(row) {

        const barang =
            row.querySelector('.item-barang');

        const satuan =
            row.querySelector('.item-satuan');

        const jumlah =
            row.querySelector('.item-jumlah');

        const harga =
            row.querySelector('.item-harga');


        const selectedSatuan =
            satuan.dataset.selected
            ||
            satuan.value;


        if (
            barang.value
        ) {

            isiSatuanItem(
                row,
                selectedSatuan
            );

        }


        barang.addEventListener(
            'change',
            function () {

                isiSatuanItem(
                    row,
                    ''
                );

                hitungItemPenjualan(row);

            }
        );


        satuan.addEventListener(
            'change',
            function () {

                updateItemKonversi(row);

            }
        );


        jumlah.addEventListener(
            'input',
            function () {

                updateItemKonversi(row);

                hitungItemPenjualan(row);

            }
        );


        harga.addEventListener(
            'input',
            function () {

                hitungItemPenjualan(row);

            }
        );


        const hapusButton =
            row.querySelector('.btn-hapus-item');


        hapusButton.addEventListener(
            'click',
            function () {

                hapusItemPenjualan(row);

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | ISI SATUAN ITEM
    |--------------------------------------------------------------------------
    */

    function isiSatuanItem(
        row,
        selectedSatuan = ''
    ) {

        const barang =
            row.querySelector('.item-barang');

        const satuan =
            row.querySelector('.item-satuan');


        const option =
            barang.options[
                barang.selectedIndex
            ];


        satuan.innerHTML =
            '<option value="">-- Pilih Satuan --</option>';


        if (
            !option
            ||
            !option.value
        ) {

            resetItemPenjualan(row);

            return;

        }


        const satuanDasarId =
            option.dataset.satuanId;


        const satuanDasar =
            option.dataset.satuan || '-';


        /*
        | SATUAN DASAR
        */

        const baseOption =
            document.createElement('option');

        baseOption.value =
            satuanDasarId;

        baseOption.textContent =
            satuanDasar;

        baseOption.dataset.nilai =
            '1';

        satuan.appendChild(
            baseOption
        );


        /*
        | SATUAN KONVERSI
        */

        const konversi =
            getKonversi(option);


        konversi.forEach(function (item) {

            if (
                String(item.satuan_id)
                ===
                String(satuanDasarId)
            ) {
                return;
            }


            const namaSatuan =
                item.satuan?.kode_satuan
                ||
                item.satuan?.nama_satuan
                ||
                ('Satuan #' + item.satuan_id);


            const satuanOption =
                document.createElement('option');

            satuanOption.value =
                item.satuan_id;

            satuanOption.textContent =
                namaSatuan;

            satuanOption.dataset.nilai =
                item.nilai_konversi;

            satuan.appendChild(
                satuanOption
            );

        });


        if (
            selectedSatuan
            &&
            Array.from(satuan.options).some(
                function (option) {
                    return String(option.value)
                        ===
                        String(selectedSatuan);
                }
            )
        ) {

            satuan.value =
                selectedSatuan;

        } else {

            satuan.value =
                satuanDasarId;

        }


        updateItemKonversi(row);

    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE KONVERSI ITEM
    |--------------------------------------------------------------------------
    */

    function updateItemKonversi(row) {

        const barang =
            row.querySelector('.item-barang');

        const satuan =
            row.querySelector('.item-satuan');

        const jumlah =
            row.querySelector('.item-jumlah');

        const nilaiInput =
            row.querySelector('.item-nilai-konversi');

        const jumlahDasarInput =
            row.querySelector('.item-jumlah-dasar');

        const batas =
            row.querySelector('.item-batas');

        const wrapper =
            row.querySelector('.item-konversi-wrapper');

        const textKonversi =
            row.querySelector('.item-text-konversi');

        const jumlahDasarPreview =
            row.querySelector('.item-jumlah-dasar-preview');

        const satuanDasarPreview =
            row.querySelector('.item-satuan-dasar-preview');


        const barangOption =
            barang.options[
                barang.selectedIndex
            ];

        const satuanOption =
            satuan.options[
                satuan.selectedIndex
            ];


        if (
            !barangOption
            ||
            !barangOption.value
            ||
            !satuanOption
        ) {

            wrapper.classList.add(
                'd-none'
            );

            return;

        }


        const nilai =
            Number(
                satuanOption.dataset.nilai || 1
            );


        const stok =
            Number(
                barangOption.dataset.stok || 0
            );


        const maxTransaksi =
            Math.floor(
                stok / nilai
            );


        const jumlahDasar =
            Number(
                jumlah.value || 0
            ) * nilai;


        nilaiInput.value =
            nilai;


        jumlahDasarInput.value =
            jumlahDasar;


        jumlahDasarPreview.textContent =
            jumlahDasar.toLocaleString(
                'id-ID'
            );


        satuanDasarPreview.textContent =
            barangOption.dataset.satuan || '-';


        batas.value =
            maxTransaksi
            + ' '
            + satuanOption.textContent;


        textKonversi.textContent =
            '1 '
            + satuanOption.textContent
            + ' = '
            + nilai
            + ' '
            + (barangOption.dataset.satuan || '-');


        wrapper.classList.remove(
            'd-none'
        );

    }


    /*
    |--------------------------------------------------------------------------
    | HITUNG SUBTOTAL
    |--------------------------------------------------------------------------
    */

    function hitungItemPenjualan(row) {

        const jumlah =
            Number(
                row.querySelector('.item-jumlah').value || 0
            );


        const harga =
            Number(
                row.querySelector('.item-harga').value || 0
            );


        const subtotal =
            jumlah * harga;


        row.querySelector('.item-subtotal').value =
            formatRupiah(subtotal);


        hitungTotalPenjualan();

    }


    /*
    |--------------------------------------------------------------------------
    | HITUNG TOTAL PENJUALAN
    |--------------------------------------------------------------------------
    */

    function hitungTotalPenjualan() {

        let total = 0;


        document
            .querySelectorAll('.penjualan-item')
            .forEach(function (row) {

                const jumlah =
                    Number(
                        row.querySelector('.item-jumlah').value || 0
                    );


                const harga =
                    Number(
                        row.querySelector('.item-harga').value || 0
                    );


                total +=
                    jumlah * harga;

            });


        totalHargaPenjualan.textContent =
            formatRupiah(total);

    }


    /*
    |--------------------------------------------------------------------------
    | TAMBAH ITEM
    |--------------------------------------------------------------------------
    */

    btnTambahBarang.addEventListener(
        'click',
        function () {

            const index =
                itemCounter++;

            const row =
                document.createElement('div');


            row.className =
                'penjualan-item border rounded p-3 mb-3';


            row.dataset.index =
                index;


            row.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-3">

                    <strong>
                        Barang #${index + 1}
                    </strong>

                    <button
                        type="button"
                        class="btn btn-sm btn-outline-danger btn-hapus-item"
                    >
                        <i class="fas fa-trash"></i>
                    </button>

                </div>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Barang <span class="text-danger">*</span>
                        </label>

                        <select
                            name="items[${index}][barang_id]"
                            class="form-select item-barang"
                            required
                        >

                            <option value="">
                                -- Pilih Barang --
                            </option>

                            @foreach ($barang as $barangItem)

                                <option
                                    value="{{ $barangItem->id }}"
                                    data-kode="{{ $barangItem->kode_barang }}"
                                    data-kategori="{{ $barangItem->kategori->nama_kategori ?? '-' }}"
                                    data-satuan-id="{{ $barangItem->satuan_id }}"
                                    data-satuan="{{ $barangItem->satuan->kode_satuan ?? $barangItem->satuan->nama_satuan ?? '-' }}"
                                    data-stok="{{ $barangItem->stok }}"
                                    data-rak="{{ $barangItem->lokasi_rak ?? '-' }}"
                                    data-konversi='@json($barangItem->konversiSatuan)'
                                >
                                    {{ $barangItem->kode_barang }} - {{ $barangItem->nama_barang }}
                                </option>

                            @endforeach

                        </select>

                    </div>

                    <div class="col-md-3 mb-3">

                        <label class="form-label">
                            Satuan <span class="text-danger">*</span>
                        </label>

                        <select
                            name="items[${index}][satuan_id]"
                            class="form-select item-satuan"
                            required
                        >

                            <option value="">
                                -- Pilih Barang --
                            </option>

                        </select>

                    </div>

                    <div class="col-md-3 mb-3">

                        <label class="form-label">
                            Jumlah <span class="text-danger">*</span>
                        </label>

                        <input
                            type="number"
                            name="items[${index}][jumlah]"
                            class="form-control item-jumlah"
                            min="0.01"
                            step="0.01"
                            required
                        >

                    </div>

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Harga Jual / Satuan
                            <span class="text-danger">*</span>
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                Rp
                            </span>

                            <input
                                type="number"
                                name="items[${index}][harga_jual]"
                                class="form-control item-harga"
                                min="0"
                                step="1"
                                required
                            >

                        </div>

                    </div>

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Maksimal Pengeluaran
                        </label>

                        <input
                            type="text"
                            class="form-control item-batas"
                            readonly
                        >

                    </div>

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Subtotal
                        </label>

                        <input
                            type="text"
                            class="form-control item-subtotal"
                            value="Rp 0"
                            readonly
                        >

                    </div>

                </div>

                <div class="alert alert-info item-konversi-wrapper d-none mb-0">

                    <div class="row">

                        <div class="col-md-6">

                            <small class="text-muted">
                                Konversi:
                            </small>

                            <div>
                                <strong class="item-text-konversi">
                                    -
                                </strong>
                            </div>

                        </div>

                        <div class="col-md-6">

                            <small class="text-muted">
                                Jumlah dalam Satuan Dasar:
                            </small>

                            <div>

                                <strong class="item-jumlah-dasar-preview">
                                    0
                                </strong>

                                <span class="item-satuan-dasar-preview">
                                    -
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

                <input
                    type="hidden"
                    name="items[${index}][nilai_konversi]"
                    class="item-nilai-konversi"
                    value="1"
                >

                <input
                    type="hidden"
                    name="items[${index}][jumlah_dasar]"
                    class="item-jumlah-dasar"
                    value=""
                >
            `;


            penjualanItems.appendChild(row);


            initItemPenjualan(row);


            updateNomorItem();


        }
    );


    /*
    |--------------------------------------------------------------------------
    | HAPUS ITEM
    |--------------------------------------------------------------------------
    */

    function hapusItemPenjualan(row) {

        const rows =
            document.querySelectorAll(
                '.penjualan-item'
            );


        if (rows.length <= 1) {

            return;

        }


        row.remove();


        updateNomorItem();

        hitungTotalPenjualan();

    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE NOMOR ITEM
    |--------------------------------------------------------------------------
    */

    function updateNomorItem() {

        document
            .querySelectorAll('.penjualan-item')
            .forEach(function (row, index) {

                const title =
                    row.querySelector('strong');


                if (title) {

                    title.textContent =
                        'Barang #' + (index + 1);

                }


                const button =
                    row.querySelector('.btn-hapus-item');


                if (button) {

                    button.disabled =
                        document.querySelectorAll(
                            '.penjualan-item'
                        ).length <= 1;

                }

            });

    }


    /*
    |--------------------------------------------------------------------------
    | RESET ITEM
    |--------------------------------------------------------------------------
    */

    function resetItemPenjualan(row) {

        row.querySelector('.item-satuan').innerHTML =
            '<option value="">-- Pilih Barang --</option>';

        row.querySelector('.item-batas').value =
            '';

        row.querySelector('.item-subtotal').value =
            'Rp 0';

        row.querySelector('.item-nilai-konversi').value =
            '1';

        row.querySelector('.item-jumlah-dasar').value =
            '';

        row.querySelector('.item-jumlah-dasar-preview').textContent =
            '0';

        row.querySelector('.item-satuan-dasar-preview').textContent =
            '-';

        row.querySelector('.item-konversi-wrapper')
            .classList.add('d-none');

    }


    /*
    |--------------------------------------------------------------------------
    | CARI BARANG BERDASARKAN KODE
    |--------------------------------------------------------------------------
    */

    async function cariKodeBarang() {

        const kode =
            inputKodeBarang.value.trim();


        if (!kode) {

            kodeSearchMessage.className =
                'mt-2 alert alert-warning';

            kodeSearchMessage.textContent =
                'Masukkan kode barang terlebih dahulu.';

            kodeSearchMessage.style.display =
                'block';

            return;

        }


        kodeSearchMessage.className =
            'mt-2 alert alert-info';

        kodeSearchMessage.textContent =
            'Mencari barang dengan kode ' + kode + '...';

        kodeSearchMessage.style.display =
            'block';


        try {

            const response =
                await fetch(
                    "{{ url('/barang/cari-qr') }}/"
                    +
                    encodeURIComponent(kode)
                );


            const result =
                await response.json();


            if (
                !response.ok
                ||
                !result.success
            ) {

                throw new Error(
                    result.message
                    ||
                    'Barang tidak ditemukan.'
                );

            }


            const data =
                result.data;


            /*
            | Jika Penjualan:
            | masukkan ke item pertama yang kosong.
            */

            if (
                jenisSelect.value === 'Penjualan'
            ) {

                const emptyRow =
                    Array.from(
                        document.querySelectorAll(
                            '.penjualan-item'
                        )
                    ).find(function (row) {

                        return !row.querySelector(
                            '.item-barang'
                        ).value;

                    });


                if (emptyRow) {

                    emptyRow.querySelector(
                        '.item-barang'
                    ).value =
                        data.id;

                    isiSatuanItem(
                        emptyRow,
                        ''
                    );

                    hitungItemPenjualan(
                        emptyRow
                    );

                } else {

                    btnTambahBarang.click();


                    const rows =
                        document.querySelectorAll(
                            '.penjualan-item'
                        );

                    const lastRow =
                        rows[rows.length - 1];


                    lastRow.querySelector(
                        '.item-barang'
                    ).value =
                        data.id;


                    isiSatuanItem(
                        lastRow,
                        ''
                    );

                    hitungItemPenjualan(
                        lastRow
                    );

                }

            } else {

                barangSelect.value =
                    data.id;


                firstLoad = false;


                barangSelect.dispatchEvent(
                    new Event('change')
                );

            }


            kodeSearchMessage.className =
                'mt-2 alert alert-success';


            kodeSearchMessage.innerHTML =
                '<strong>Barang ditemukan!</strong><br>'
                +
                'Kode: '
                +
                data.kode_barang
                +
                '<br>'
                +
                'Nama: '
                +
                data.nama_barang;


            inputKodeBarang.value = '';


        } catch (error) {

            console.error(
                'SEARCH KODE ERROR:',
                error
            );


            kodeSearchMessage.className =
                'mt-2 alert alert-danger';


            kodeSearchMessage.textContent =
                error.message
                ||
                'Barang tidak ditemukan.';

        }

    }


    /*
    |--------------------------------------------------------------------------
    | PROSES QR
    |--------------------------------------------------------------------------
    */

    async function prosesQr(kode) {

        if (sedangMemproses) {

            return;

        }


        sedangMemproses = true;


        qrMessage.className =
            'alert alert-info mt-3 mb-0';


        qrMessage.textContent =
            'QR terdeteksi. Mencari barang...';


        try {

            const response =
                await fetch(
                    "{{ url('/barang/cari-qr') }}/"
                    +
                    encodeURIComponent(kode)
                );


            const result =
                await response.json();


            if (
                !response.ok
                ||
                !result.success
            ) {

                throw new Error(
                    result.message
                    ||
                    'Barang tidak ditemukan.'
                );

            }


            const data =
                result.data;


            /*
            | Penjualan
            */

            if (
                jenisSelect.value === 'Penjualan'
            ) {

                const emptyRow =
                    Array.from(
                        document.querySelectorAll(
                            '.penjualan-item'
                        )
                    ).find(function (row) {

                        return !row.querySelector(
                            '.item-barang'
                        ).value;

                    });


                let targetRow;


                if (emptyRow) {

                    targetRow =
                        emptyRow;

                } else {

                    btnTambahBarang.click();


                    const rows =
                        document.querySelectorAll(
                            '.penjualan-item'
                        );


                    targetRow =
                        rows[rows.length - 1];

                }


                targetRow.querySelector(
                    '.item-barang'
                ).value =
                    data.id;


                isiSatuanItem(
                    targetRow,
                    ''
                );


                hitungItemPenjualan(
                    targetRow
                );

            } else {

                barangSelect.value =
                    data.id;


                firstLoad = false;


                barangSelect.dispatchEvent(
                    new Event('change')
                );

            }


            qrMessage.className =
                'alert alert-success mt-3 mb-0';


            qrMessage.innerHTML =
                '<strong>Barang ditemukan!</strong><br>'
                +
                data.kode_barang
                +
                ' - '
                +
                data.nama_barang;


            setTimeout(
                function () {

                    tutupScanner();

                },
                800
            );


        } catch (error) {

            console.error(
                'QR ERROR:',
                error
            );


            qrMessage.className =
                'alert alert-danger mt-3 mb-0';


            qrMessage.textContent =
                error.message
                ||
                'QR tidak valid.';

        }


        setTimeout(
            function () {

                sedangMemproses = false;

            },
            1000
        );

    }


    /*
    |--------------------------------------------------------------------------
    | SCANNER
    |--------------------------------------------------------------------------
    */

    async function mulaiScanner() {

        if (scannerRunning) {

            return;

        }


        qrScannerContainer.style.display =
            'block';


        qrMessage.className =
            'alert alert-info mt-3 mb-0';


        qrMessage.textContent =
            'Memeriksa kamera...';


        try {

            document.getElementById(
                'qr-reader'
            ).innerHTML = '';


            const cameras =
                await Html5Qrcode.getCameras();


            console.log(
                'CAMERA LIST:',
                cameras
            );


            if (
                !cameras
                ||
                cameras.length === 0
            ) {

                throw new Error(
                    'Tidak ada kamera yang terdeteksi oleh browser.'
                );

            }


            let cameraId =
                cameras[0].id;


            const kameraBelakang =
                cameras.find(
                    function(camera) {

                        const label =
                            (
                                camera.label
                                ||
                                ''
                            ).toLowerCase();


                        return (
                            label.includes('back')
                            ||
                            label.includes('rear')
                            ||
                            label.includes('environment')
                        );

                    }
                );


            if (kameraBelakang) {

                cameraId =
                    kameraBelakang.id;

            }


            html5QrCode =
                new Html5Qrcode(
                    'qr-reader'
                );


            qrMessage.textContent =
                'Membuka kamera...';


            await html5QrCode.start(

                cameraId,

                {

                    fps: 20,

                    qrbox: {
                        width: 450,
                        height: 450
                    },

                    aspectRatio: 1.0,

                    disableFlip: false,

                    formatsToSupport: [
                        Html5QrcodeSupportedFormats.QR_CODE
                    ],

                    experimentalFeatures: {
                        useBarCodeDetectorIfSupported: true
                    }

                },

                function(decodedText) {

                    console.log(
                        'QR TERDETEKSI:',
                        decodedText
                    );


                    prosesQr(
                        decodedText
                    );

                },

                function(errorMessage) {

                    // Error scanning normal.

                }

            );


            scannerRunning =
                true;


            qrMessage.className =
                'alert alert-success mt-3 mb-0';


            qrMessage.textContent =
                'Kamera aktif. Arahkan QR Code ke dalam kotak.';


            const video =
                document.querySelector(
                    '#qr-reader video'
                );


            if (video) {

                video.style.width =
                    '500px';

                video.style.height =
                    '500px';

                video.style.maxWidth =
                    '100%';

                video.style.objectFit =
                    'cover';

                video.style.display =
                    'block';

                video.style.margin =
                    '0 auto';

            }

        } catch (error) {

            console.error(
                'SCANNER ERROR:',
                error
            );


            qrMessage.className =
                'alert alert-danger mt-3 mb-0';


            qrMessage.textContent =
                error.message
                ||
                'Kamera tidak dapat digunakan.';


            html5QrCode =
                null;


            scannerRunning =
                false;

        }

    }


    /*
    |--------------------------------------------------------------------------
    | TUTUP SCANNER
    |--------------------------------------------------------------------------
    */

    async function tutupScanner() {

        if (
            html5QrCode
            &&
            scannerRunning
        ) {

            try {

                await html5QrCode.stop();

                await html5QrCode.clear();

            } catch (error) {

                console.error(
                    'STOP SCANNER ERROR:',
                    error
                );

            }

        }


        html5QrCode =
            null;

        scannerRunning =
            false;

        sedangMemproses =
            false;


        qrScannerContainer.style.display =
            'none';


        document.getElementById(
            'qr-reader'
        ).innerHTML =
            '';

    }


    /*
    |--------------------------------------------------------------------------
    | EVENT SINGLE ITEM
    |--------------------------------------------------------------------------
    */

    barangSelect.addEventListener(
        'change',
        function () {

            firstLoad = false;

            tampilkanDataBarang();

        }
    );


    satuanSelect.addEventListener(
        'change',
        function () {

            updateKonversi();

        }
    );


    jumlahInput.addEventListener(
        'input',
        function () {

            const max =
                Number(
                    jumlahInput.max || 0
                );


            const jumlah =
                Number(
                    jumlahInput.value || 0
                );


            if (
                max > 0
                &&
                jumlah > max
            ) {

                jumlahInput.value =
                    max;

            }


            hitungJumlahDasar();

        }
    );


    jenisSelect.addEventListener(
        'change',
        function () {

            tampilkanPenjualan();

        }
    );


    btnCariKode.addEventListener(
        'click',
        function () {

            cariKodeBarang();

        }
    );


    inputKodeBarang.addEventListener(
        'keydown',
        function (event) {

            if (
                event.key === 'Enter'
            ) {

                event.preventDefault();

                cariKodeBarang();

            }

        }
    );


    btnScanQr.addEventListener(
        'click',
        function () {

            mulaiScanner();

        }
    );


    btnCloseScanner.addEventListener(
        'click',
        function () {

            tutupScanner();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | LOAD AWAL
    |--------------------------------------------------------------------------
    */

    if (barangSelect.value) {

        tampilkanDataBarang();

    }


    /*
    | Inisialisasi item Penjualan
    */

    document
        .querySelectorAll('.penjualan-item')
        .forEach(function (row, index) {

            const satuan =
                row.querySelector('.item-satuan');

            const oldSatuan =
                @json($penjualanItems);

            if (
                oldSatuan[index]
                &&
                oldSatuan[index].satuan_id
            ) {

                satuan.dataset.selected =
                    oldSatuan[index].satuan_id;

            }

        });


    tampilkanPenjualan();


    updateNomorItem();

});

</script>

@endpush
