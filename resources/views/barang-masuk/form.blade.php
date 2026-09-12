{{-- KODE TRANSAKSI --}}

<div class="mb-3">

    <label class="form-label">
        Kode Transaksi
    </label>

    <input
        type="text"
        class="form-control"
        value="{{ old('kode_transaksi', $kode ?? ($barangMasuk->kode_transaksi ?? '')) }}"
        readonly>

</div>


{{-- SUMBER BARANG --}}

<div class="mb-3">

    <label class="form-label">
        Sumber Barang Masuk
    </label>

    <select
        id="sumber"
        name="sumber"
        class="form-select">

        <option
            value="langsung"
            @selected(old('sumber', 'langsung') == 'langsung')>
            Pembelian Langsung
        </option>

        <option
            value="permintaan"
            @selected(old('sumber') == 'permintaan')>
            Dari Permintaan Pengadaan
        </option>

    </select>

</div>


{{-- PERMINTAAN PENGADAAN --}}

<div
    class="mb-3"
    id="permintaan-wrapper"
    style="display:none;">

    <label class="form-label">
        Permintaan Pengadaan
    </label>

    <select
        id="permintaan_id"
        name="permintaan_id"
        class="form-select @error('permintaan_id') is-invalid @enderror">

        <option value="">
            -- Pilih Permintaan --
        </option>

        @foreach($permintaan as $item)

            <option
                value="{{ $item->id }}"
                data-barang="{{ $item->barang_id }}"
                data-jumlah="{{ $item->jumlah }}"
                data-keterangan="{{ $item->kode_permintaan }}"

                @selected(
                    old(
                        'permintaan_id',
                        $barangMasuk->permintaan_id ?? ''
                    ) == $item->id
                )
            >

                {{ $item->kode_permintaan }}
                -
                {{ $item->barang->nama_barang }}
                -
                {{ $item->jumlah }}

            </option>

        @endforeach

    </select>

    @error('permintaan_id')

        <div class="invalid-feedback">
            {{ $message }}
        </div>

    @enderror

</div>


{{-- BARANG --}}

<div class="mb-3">

    <label class="form-label">
        Barang
        <span class="text-danger">*</span>
    </label>


    {{-- PILIH BARANG + SCAN QR --}}

    <div class="input-group mb-2">

        <select
            id="barang_id"
            name="barang_id"
            class="form-select @error('barang_id') is-invalid @enderror"
            required>

            <option value="">
                -- Pilih Barang --
            </option>

            @foreach($barang as $item)

                <option
                    value="{{ $item->id }}"

                    data-kode="{{ $item->kode_barang }}"

                    data-nama="{{ $item->nama_barang }}"

                    data-kategori="{{ $item->kategori->nama_kategori ?? '-' }}"

                    data-satuan-id="{{ $item->satuan_id }}"

                    data-satuan="{{ $item->satuan->nama_satuan ?? '-' }}"

                    data-stok="{{ $item->stok }}"

                    data-rak="{{ $item->lokasi_rak ?? '-' }}"

                    data-supplier-id="{{ $item->supplier_id }}"

                    data-supplier-nama="{{ $item->supplier->nama_supplier ?? '-' }}"

                    data-konversi="{{ $item->konversiSatuan->toJson() }}"

                    @selected(
                        old(
                            'barang_id',
                            $barangMasuk->barang_id ?? ''
                        ) == $item->id
                    )
                >

                    {{ $item->kode_barang }}
                    -
                    {{ $item->nama_barang }}

                </option>

            @endforeach

        </select>


        <div class="input-group-append">

            <button
                type="button"
                class="btn btn-dark"
                id="btnScanQr">

                <i class="fas fa-qrcode"></i>
                Scan QR

            </button>

        </div>

    </div>


    {{-- CARI KODE BARANG --}}

    <div class="input-group mb-1">

        <input
            type="text"
            id="inputKodeBarang"
            class="form-control"
            placeholder="Masukkan kode barang, contoh: BRG0001">

        <div class="input-group-append">

            <button
                type="button"
                class="btn btn-primary"
                id="btnCariKode">

                <i class="fas fa-search"></i>
                Cari Kode

            </button>

        </div>

    </div>


    <small class="form-text text-muted">

        Pilih barang secara manual, scan QR, atau cari berdasarkan kode barang.

    </small>


    <div
        id="kodeSearchMessage"
        class="mt-2"
        style="display:none;">
    </div>


    @error('barang_id')

        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>

    @enderror

</div>


{{-- SCANNER QR --}}

<div
    id="qrScannerContainer"
    class="card mb-3"
    style="display:none;">

    <div class="card-header">

        <strong>

            <i class="fas fa-qrcode"></i>

            Scan QR Barang

        </strong>

    </div>


    <div class="card-body text-center">

        <div
            id="qr-reader"
            style="width:100%;">
        </div>


        <div
            id="qrMessage"
            class="alert alert-info mt-3 mb-0">

            Arahkan kamera ke QR Code barang.

        </div>


        <button
            type="button"
            class="btn btn-secondary mt-3"
            id="btnCloseScanner">

            <i class="fas fa-times"></i>

            Tutup Scanner

        </button>

    </div>

</div>


{{-- INFORMASI BARANG --}}

<div class="row mb-3">

    <div class="col-md-4">

        <label class="form-label">
            Kode Barang
        </label>

        <input
            type="text"
            id="kode_barang"
            class="form-control"
            readonly>

    </div>


    <div class="col-md-4">

        <label class="form-label">
            Kategori
        </label>

        <input
            type="text"
            id="kategori_barang"
            class="form-control"
            readonly>

    </div>


    <div class="col-md-4">

        <label class="form-label">
            Lokasi Rak
        </label>

        <input
            type="text"
            id="lokasi_rak"
            class="form-control"
            readonly>

    </div>

</div>


{{-- SATUAN --}}

<div class="card mb-3">

    <div class="card-header">

        <strong>
            Satuan Barang
        </strong>

    </div>


    <div class="card-body">

        <div class="row">

            {{-- SATUAN DASAR --}}

            <div class="col-md-6">

                <label class="form-label">
                    Satuan Dasar
                </label>

                <input
                    type="text"
                    id="satuan_dasar"
                    class="form-control"
                    readonly>

                <small class="text-muted">

                    Stok sistem selalu disimpan dalam satuan dasar.

                </small>

            </div>


            {{-- SATUAN TRANSAKSI --}}

            <div class="col-md-6">

                <label class="form-label">

                    Satuan Transaksi

                    <span class="text-danger">*</span>

                </label>

                <select
                    name="satuan_id"
                    id="satuan_id"
                    class="form-select @error('satuan_id') is-invalid @enderror"
                    required>

                    <option value="">
                        -- Pilih satuan --
                    </option>

                </select>


                @error('satuan_id')

                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>

                @enderror

            </div>

        </div>


        {{-- KONVERSI --}}

        <div
            class="alert alert-info mt-3"
            id="konversi-wrapper"
            style="display:none;">

            <i class="fas fa-info-circle"></i>

            <span id="konversi-text"></span>

        </div>

    </div>

</div>


{{-- JUMLAH --}}

<div class="card mb-3">

    <div class="card-header">

        <strong>
            Jumlah Barang
        </strong>

    </div>


    <div class="card-body">

        <div class="row">

            <div class="col-md-4">

                <label class="form-label">

                    Jumlah Masuk

                    <span class="text-danger">*</span>

                </label>

                <input
                    type="number"
                    min="1"
                    name="jumlah"
                    id="jumlah"
                    class="form-control @error('jumlah') is-invalid @enderror"
                    placeholder="Contoh: 3"
                    value="{{ old('jumlah', $barangMasuk->jumlah ?? '') }}"
                    required>

                @error('jumlah')

                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>

                @enderror

            </div>


            <div class="col-md-4">

                <label class="form-label">
                    Satuan
                </label>

                <input
                    type="text"
                    id="satuan_jumlah"
                    class="form-control"
                    readonly>

            </div>


            <div class="col-md-4">

                <label class="form-label">
                    Jumlah dalam Satuan Dasar
                </label>

                <input
                    type="text"
                    id="jumlah_dasar_preview"
                    class="form-control"
                    readonly>

                <small class="text-muted">

                    Jumlah yang akan ditambahkan ke stok.

                </small>

            </div>

        </div>

    </div>

</div>


{{-- STOK SAAT INI --}}

<div class="mb-3">

    <label class="form-label">
        Stok Saat Ini
    </label>

    <input
        type="text"
        id="stok_barang"
        class="form-control"
        readonly>

</div>


{{-- SUPPLIER OTOMATIS --}}

<div class="mb-3">

    <label class="form-label">
        Supplier
    </label>

    <input
        type="text"
        id="supplier_nama"
        class="form-control"
        value=""
        placeholder="Supplier akan terisi otomatis setelah memilih barang"
        readonly>

    <small class="text-muted">

        Supplier mengikuti supplier yang terdaftar pada Data Barang.

    </small>

</div>


{{-- TANGGAL --}}

<div class="mb-3">

    <label class="form-label">

        Tanggal Masuk

        <span class="text-danger">*</span>

    </label>

    <input
        type="date"
        name="tanggal_masuk"
        class="form-control @error('tanggal_masuk') is-invalid @enderror"

        value="{{
            old(
                'tanggal_masuk',
                isset($barangMasuk)
                    ? $barangMasuk->tanggal_masuk->format('Y-m-d')
                    : now()->format('Y-m-d')
            )
        }}"

        required>

    @error('tanggal_masuk')

        <div class="invalid-feedback">
            {{ $message }}
        </div>

    @enderror

</div>


{{-- HARGA BELI & EXPIRED --}}

<div class="row mb-3">

    <div class="col-md-6">

        <label class="form-label">

            Harga Beli

            <span
                id="harga-satuan-label"
                class="text-primary">

                per Satuan Transaksi

            </span>

            <span class="text-danger">*</span>

        </label>


        <div class="input-group">

            <span class="input-group-text">
                Rp
            </span>

            <input
                type="number"
                min="0"
                step="0.01"
                name="harga_beli"
                id="harga_beli"
                class="form-control @error('harga_beli') is-invalid @enderror"
                placeholder="Contoh: 50000"
                value="{{ old(
                    'harga_beli',
                    $barangMasuk->harga_beli ?? ''
                ) }}"
                required>

        </div>


        <small
            class="text-muted"
            id="harga-satuan-info">

            Pilih satuan transaksi terlebih dahulu.

        </small>


        @error('harga_beli')

            <div class="invalid-feedback d-block">
                {{ $message }}
            </div>

        @enderror

    </div>


    <div class="col-md-6">

        <label class="form-label">
            Tanggal Expired
        </label>

        <input
            type="date"
            name="expired_date"
            class="form-control @error('expired_date') is-invalid @enderror"

            value="{{
                old(
                    'expired_date',
                    isset($barangMasuk) &&
                    $barangMasuk->expired_date
                        ? $barangMasuk->expired_date->format('Y-m-d')
                        : ''
                )
            }}">

        <small class="text-muted">

            Kosongkan apabila barang tidak memiliki masa kedaluwarsa.

        </small>


        @error('expired_date')

            <div class="invalid-feedback">
                {{ $message }}
            </div>

        @enderror

    </div>

</div>


{{-- NOMOR FAKTUR --}}

<div class="mb-3">

    <label class="form-label">
        Nomor Faktur
    </label>

    <input
        type="text"
        name="nomor_faktur"
        class="form-control @error('nomor_faktur') is-invalid @enderror"
        placeholder="Contoh: INV-20260831-001"

        value="{{ old(
            'nomor_faktur',
            $barangMasuk->nomor_faktur ?? ''
        ) }}">

    <small class="text-muted">

        Isi sesuai nomor invoice dari supplier (boleh dikosongkan).

    </small>


    @error('nomor_faktur')

        <div class="invalid-feedback">
            {{ $message }}
        </div>

    @enderror

</div>


{{-- KETERANGAN --}}

<div class="mb-3">

    <label class="form-label">
        Keterangan
    </label>

    <textarea
        name="keterangan"
        rows="3"
        class="form-control @error('keterangan') is-invalid @enderror"
        placeholder="Contoh: Barang diterima dalam kondisi baik">{{ old(
            'keterangan',
            $barangMasuk->keterangan ?? ''
        ) }}</textarea>

    @error('keterangan')

        <div class="invalid-feedback">
            {{ $message }}
        </div>

    @enderror

</div>


{{-- BUTTON --}}

<div class="mt-4">

    <a
        href="{{ route('barang-masuk.index') }}"
        class="btn btn-secondary me-2">

        <i class="fas fa-arrow-left"></i>

        Kembali

    </a>


    <button
        type="submit"
        class="btn btn-primary">

        <i class="fas fa-save"></i>

        Simpan

    </button>

</div>


@push('js')

<script src="https://unpkg.com/html5-qrcode"></script>


<script>

document.addEventListener('DOMContentLoaded', function () {


    /*
    |--------------------------------------------------------------------------
    | ELEMENT
    |--------------------------------------------------------------------------
    */

    const sumber =
        document.getElementById('sumber');

    const permintaanWrapper =
        document.getElementById('permintaan-wrapper');

    const permintaanSelect =
        document.getElementById('permintaan_id');

    const barangSelect =
        document.getElementById('barang_id');

    const kodeBarang =
        document.getElementById('kode_barang');

    const kategoriBarang =
        document.getElementById('kategori_barang');

    const stokBarang =
        document.getElementById('stok_barang');

    const lokasiRak =
        document.getElementById('lokasi_rak');

    const supplierNama =
        document.getElementById('supplier_nama');

    const satuanDasar =
        document.getElementById('satuan_dasar');

    const satuanSelect =
        document.getElementById('satuan_id');

    const jumlahInput =
        document.getElementById('jumlah');

    const hargaSatuanLabel =
        document.getElementById('harga-satuan-label');

    const hargaSatuanInfo =
        document.getElementById('harga-satuan-info');

    const satuanJumlah =
        document.getElementById('satuan_jumlah');

    const jumlahDasarPreview =
        document.getElementById('jumlah_dasar_preview');

    const konversiWrapper =
        document.getElementById('konversi-wrapper');

    const konversiText =
        document.getElementById('konversi-text');


    /*
    |--------------------------------------------------------------------------
    | QR ELEMENT
    |--------------------------------------------------------------------------
    */

    const btnScanQr =
        document.getElementById('btnScanQr');

    const btnCloseScanner =
        document.getElementById('btnCloseScanner');

    const qrScannerContainer =
        document.getElementById('qrScannerContainer');

    const qrMessage =
        document.getElementById('qrMessage');

    const inputKodeBarang =
        document.getElementById('inputKodeBarang');

    const btnCariKode =
        document.getElementById('btnCariKode');

    const kodeSearchMessage =
        document.getElementById('kodeSearchMessage');


    let html5QrCode = null;

    let scannerRunning = false;

    let sedangMemproses = false;


    /*
    |--------------------------------------------------------------------------
    | TAMPILKAN DATA BARANG
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

            stokBarang.value = '';

            lokasiRak.value = '';

            supplierNama.value = '';

            satuanDasar.value = '';

            satuanSelect.innerHTML =
                '<option value="">-- Pilih satuan --</option>';

            jumlahDasarPreview.value = '';

            satuanJumlah.value = '';

            konversiWrapper.style.display =
                'none';

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | INFORMASI BARANG
        |--------------------------------------------------------------------------
        */

        kodeBarang.value =
            option.dataset.kode || '';

        kategoriBarang.value =
            option.dataset.kategori || '-';

        stokBarang.value =
            option.dataset.stok || '0';

        lokasiRak.value =
            option.dataset.rak || '-';


        /*
        |--------------------------------------------------------------------------
        | SUPPLIER OTOMATIS
        |--------------------------------------------------------------------------
        */

        supplierNama.value =
            option.dataset.supplierNama || '-';


        /*
        |--------------------------------------------------------------------------
        | SATUAN DASAR
        |--------------------------------------------------------------------------
        */

        const satuanDasarId =
            option.dataset.satuanId;

        const namaSatuanDasar =
            option.dataset.satuan || '-';

        satuanDasar.value =
            namaSatuanDasar;


        /*
        |--------------------------------------------------------------------------
        | DATA KONVERSI
        |--------------------------------------------------------------------------
        */

        let konversi = [];

        try {

            konversi =
                JSON.parse(
                    option.dataset.konversi || '[]'
                );

        } catch (error) {

            console.error(
                'Data konversi satuan tidak valid:',
                error
            );

            konversi = [];

        }


        /*
        |--------------------------------------------------------------------------
        | RESET SATUAN
        |--------------------------------------------------------------------------
        */

        satuanSelect.innerHTML = '';


        /*
        |--------------------------------------------------------------------------
        | SATUAN DASAR
        |--------------------------------------------------------------------------
        */

        const optionDasar =
            document.createElement('option');

        optionDasar.value =
            satuanDasarId;

        optionDasar.textContent =
            namaSatuanDasar.toUpperCase();

        optionDasar.dataset.nilai =
            '1';

        satuanSelect.appendChild(
            optionDasar
        );


        /*
        |--------------------------------------------------------------------------
        | SATUAN KONVERSI
        |--------------------------------------------------------------------------
        */

        konversi.forEach(function (item) {

            const optionKonversi =
                document.createElement('option');

            optionKonversi.value =
                item.satuan_id;

            optionKonversi.textContent =
                item.satuan?.nama_satuan
                    ? item.satuan.nama_satuan.toUpperCase()
                    : '-';

            optionKonversi.dataset.nilai =
                item.nilai_konversi;

            satuanSelect.appendChild(
                optionKonversi
            );

        });


        /*
        |--------------------------------------------------------------------------
        | SATUAN LAMA SAAT EDIT
        |--------------------------------------------------------------------------
        */

        @if(isset($barangMasuk))

            const satuanLama =
                '{{ $barangMasuk->satuan_id }}';

            if (satuanLama) {

                satuanSelect.value =
                    satuanLama;

            }

        @endif


        updateKonversi();

    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE KONVERSI
    |--------------------------------------------------------------------------
    */

    function updateKonversi() {

        const option =
            satuanSelect.options[
                satuanSelect.selectedIndex
            ];


        if (!option || !option.value) {

            satuanJumlah.value = '';

            jumlahDasarPreview.value = '';

            hargaSatuanLabel.textContent =
                'per Satuan Transaksi';

            hargaSatuanInfo.textContent =
                'Pilih satuan transaksi terlebih dahulu.';

            konversiWrapper.style.display =
                'none';

            return;
        }


        const namaSatuan =
            option.textContent.trim();


        hargaSatuanLabel.textContent =
            'per ' + namaSatuan;


        hargaSatuanInfo.textContent =
            'Harga yang dimasukkan adalah harga untuk 1 ' +
            namaSatuan;


        const nilai =
            parseFloat(
                option.dataset.nilai || 1
            );


        satuanJumlah.value =
            option.textContent;


        if (nilai > 1) {

            konversiWrapper.style.display =
                'block';

            konversiText.textContent =
                '1 ' +
                option.textContent +
                ' = ' +
                nilai +
                ' ' +
                satuanDasar.value;

        } else {

            konversiWrapper.style.display =
                'none';

        }


        hitungJumlahDasar();

    }


    /*
    |--------------------------------------------------------------------------
    | HITUNG JUMLAH DASAR
    |--------------------------------------------------------------------------
    */

    function hitungJumlahDasar() {

        const jumlah =
            parseFloat(
                jumlahInput.value || 0
            );


        const option =
            satuanSelect.options[
                satuanSelect.selectedIndex
            ];


        if (
            !option ||
            !option.value ||
            jumlah <= 0
        ) {

            jumlahDasarPreview.value = '';

            return;

        }


        const nilai =
            parseFloat(
                option.dataset.nilai || 1
            );


        const hasil =
            jumlah * nilai;


        jumlahDasarPreview.value =
            hasil +
            ' ' +
            satuanDasar.value;

    }


    /*
    |--------------------------------------------------------------------------
    | TAMPILKAN PERMINTAAN
    |--------------------------------------------------------------------------
    */

    function tampilkanPermintaan() {

        if (
            sumber.value === 'permintaan'
        ) {

            permintaanWrapper.style.display =
                'block';

        } else {

            permintaanWrapper.style.display =
                'none';

            permintaanSelect.value = '';

        }

    }


    /*
    |--------------------------------------------------------------------------
    | EVENT BARANG
    |--------------------------------------------------------------------------
    */

    barangSelect.addEventListener(
        'change',
        function () {

            tampilkanDataBarang();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | EVENT SATUAN
    |--------------------------------------------------------------------------
    */

    satuanSelect.addEventListener(
        'change',
        function () {

            updateKonversi();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | EVENT JUMLAH
    |--------------------------------------------------------------------------
    */

    jumlahInput.addEventListener(
        'input',
        function () {

            hitungJumlahDasar();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | EVENT SUMBER
    |--------------------------------------------------------------------------
    */

    sumber.addEventListener(
        'change',
        function () {

            tampilkanPermintaan();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | EVENT PERMINTAAN
    |--------------------------------------------------------------------------
    */

    permintaanSelect.addEventListener(
        'change',
        function () {

            const option =
                this.options[
                    this.selectedIndex
                ];


            if (!option || !option.value) {
                return;
            }


            /*
            | Barang
            */

            const barangId =
                option.dataset.barang;


            if (barangId) {

                barangSelect.value =
                    barangId;

                tampilkanDataBarang();

            }


            /*
            | Jumlah
            */

            const jumlah =
                option.dataset.jumlah;


            if (jumlah) {

                jumlahInput.value =
                    jumlah;

                hitungJumlahDasar();

            }


            /*
            | Keterangan
            */

            const kodePermintaan =
                option.dataset.keterangan;


            if (kodePermintaan) {

                const keterangan =
                    document.querySelector(
                        'textarea[name="keterangan"]'
                    );


                if (
                    keterangan &&
                    !keterangan.value
                ) {

                    keterangan.value =
                        'Berdasarkan permintaan pengadaan ' +
                        kodePermintaan;

                }

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | QR - PILIH BARANG
    |--------------------------------------------------------------------------
    */

    function pilihBarang(id) {

        barangSelect.value = id;


        if (barangSelect.value != id) {

            throw new Error(
                'Barang ditemukan tetapi tidak tersedia pada daftar barang aktif.'
            );

        }


        barangSelect.dispatchEvent(
            new Event('change')
        );

    }


    /*
    |--------------------------------------------------------------------------
    | QR - PROSES KODE
    |--------------------------------------------------------------------------
    */

    async function prosesQr(kode) {

        kode =
            kode.trim();


        if (!kode) {
            return;
        }


        qrMessage.className =
            'alert alert-info mt-3 mb-0';

        qrMessage.textContent =
            'Mencari barang dengan kode ' +
            kode +
            '...';


        try {

            const response =
                await fetch(
                    "{{ url('/barang/cari-qr') }}/" +
                    encodeURIComponent(kode)
                );


            const result =
                await response.json();


            if (
                !response.ok ||
                !result.success
            ) {

                throw new Error(
                    result.message ||
                    'Barang tidak ditemukan.'
                );

            }


            const data =
                result.data;


            pilihBarang(data.id);


            qrMessage.className =
                'alert alert-success mt-3 mb-0';

            qrMessage.innerHTML =
                '<strong>Barang ditemukan!</strong><br>' +
                'Kode: ' +
                data.kode_barang +
                '<br>' +
                'Nama: ' +
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

            sedangMemproses =
                false;

            qrMessage.className =
                'alert alert-danger mt-3 mb-0';

            qrMessage.textContent =
                error.message ||
                'Barang tidak ditemukan.';

        }

    }


    /*
    |--------------------------------------------------------------------------
    | QR - MULAI SCANNER
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

            const qrReader =
                document.getElementById('qr-reader');


            qrReader.innerHTML = '';


            qrReader.style.width =
                '500px';

            qrReader.style.maxWidth =
                '100%';

            qrReader.style.margin =
                '0 auto';


            /*
            |--------------------------------------------------------------------------
            | AMBIL DAFTAR KAMERA
            |--------------------------------------------------------------------------
            */

            const cameras =
                await Html5Qrcode.getCameras();


            console.log(
                'CAMERA LIST:',
                cameras
            );


            if (
                !cameras ||
                cameras.length === 0
            ) {

                throw new Error(
                    'Tidak ada kamera yang terdeteksi oleh browser.'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | PILIH KAMERA
            |--------------------------------------------------------------------------
            */

            let cameraId =
                cameras[0].id;


            const kameraBelakang =
                cameras.find(function(camera) {

                    const label =
                        (camera.label || '').toLowerCase();


                    return (
                        label.includes('back') ||
                        label.includes('rear') ||
                        label.includes('environment')
                    );

                });


            if (kameraBelakang) {

                cameraId =
                    kameraBelakang.id;

            }


            console.log(
                'KAMERA YANG DIGUNAKAN:',
                cameraId
            );


            /*
            |--------------------------------------------------------------------------
            | BUAT SCANNER
            |--------------------------------------------------------------------------
            */

            html5QrCode =
                new Html5Qrcode(
                    'qr-reader'
                );


            qrMessage.textContent =
                'Membuka kamera...';


            /*
            |--------------------------------------------------------------------------
            | START CAMERA
            |--------------------------------------------------------------------------
            */

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


                    if (sedangMemproses) {
                        return;
                    }


                    sedangMemproses =
                        true;


                    prosesQr(
                        decodedText
                    );

                },


                function(errorMessage) {

                    // Error frame normal diabaikan

                }

            );


            scannerRunning =
                true;


            /*
            |--------------------------------------------------------------------------
            | SESUAIKAN UKURAN VIDEO
            |--------------------------------------------------------------------------
            */

            const video =
                qrReader.querySelector('video');


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


            const videoContainer =
                qrReader.querySelector(
                    '#qr-shaded-region'
                );


            if (videoContainer) {

                videoContainer.style.width =
                    '450px';

                videoContainer.style.height =
                    '450px';

            }


            /*
            |--------------------------------------------------------------------------
            | STATUS
            |--------------------------------------------------------------------------
            */

            qrMessage.className =
                'alert alert-success mt-3 mb-0';

            qrMessage.textContent =
                'Kamera aktif. Arahkan QR Code ke dalam kotak.';


        } catch (error) {

            console.error(
                'SCANNER ERROR:',
                error
            );


            qrMessage.className =
                'alert alert-danger mt-3 mb-0';


            qrMessage.textContent =
                error.message ||
                'Kamera tidak dapat digunakan.';


            html5QrCode =
                null;

            scannerRunning =
                false;

        }

    }


    /*
    |--------------------------------------------------------------------------
    | QR - TUTUP SCANNER
    |--------------------------------------------------------------------------
    */

    async function tutupScanner() {

        if (
            html5QrCode &&
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

    }


    /*
    |--------------------------------------------------------------------------
    | TOMBOL SCAN
    |--------------------------------------------------------------------------
    */

    btnScanQr.addEventListener(
        'click',
        function () {

            mulaiScanner();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | TOMBOL TUTUP SCANNER
    |--------------------------------------------------------------------------
    */

    btnCloseScanner.addEventListener(
        'click',
        function () {

            tutupScanner();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | CARI KODE BARANG
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
            'Mencari barang dengan kode ' +
            kode +
            '...';

        kodeSearchMessage.style.display =
            'block';


        try {

            const response =
                await fetch(
                    "{{ url('/barang/cari-qr') }}/" +
                    encodeURIComponent(kode)
                );


            const result =
                await response.json();


            if (
                !response.ok ||
                !result.success
            ) {

                throw new Error(
                    result.message ||
                    'Barang tidak ditemukan.'
                );

            }


            const data =
                result.data;


            pilihBarang(data.id);


            kodeSearchMessage.className =
                'mt-2 alert alert-success';

            kodeSearchMessage.innerHTML =
                '<strong>Barang ditemukan!</strong><br>' +
                'Kode: ' +
                data.kode_barang +
                '<br>' +
                'Nama: ' +
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
                error.message ||
                'Barang tidak ditemukan.';

        }

    }


    /*
    |--------------------------------------------------------------------------
    | EVENT CARI KODE
    |--------------------------------------------------------------------------
    */

    btnCariKode.addEventListener(
        'click',
        function () {

            cariKodeBarang();

        }
    );


    inputKodeBarang.addEventListener(
        'keydown',
        function (event) {

            if (event.key === 'Enter') {

                event.preventDefault();

                cariKodeBarang();

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | LOAD AWAL
    |--------------------------------------------------------------------------
    */

    tampilkanPermintaan();

    tampilkanDataBarang();

});

</script>

@endpush