<div class="mb-3">

<label class="form-label">
    Kode Transaksi
</label>

<input
    type="text"
    class="form-control"
    value="{{ old('kode_transaksi', $kode ?? $stockOpname->kode_transaksi ?? '') }}"
    readonly>

</div>

{{-- ========================================================= --}}
{{-- BARANG --}}
{{-- ========================================================= --}}

<div class="mb-3">

<label class="form-label">
    Barang
    <span class="text-danger">*</span>
</label>


{{-- PILIH BARANG --}}
<div class="input-group mb-2">

    <select
        name="barang_id"
        id="barang_id"
        class="form-control @error('barang_id') is-invalid @enderror">

        <option value="">
            -- Pilih Barang --
        </option>

        @foreach($barang as $item)

            <option
                value="{{ $item->id }}"
                data-kode="{{ $item->kode_barang }}"
                data-nama="{{ $item->nama_barang }}"
                data-stok="{{ $item->stok }}"
                data-satuan="{{ $item->satuan->nama_satuan }}"
                @selected(old('barang_id', $stockOpname->barang_id ?? '') == $item->id)>

                {{ $item->kode_barang }}
                -
                {{ $item->nama_barang }}
                (Stok : {{ $item->stok }} {{ $item->satuan->nama_satuan }})

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
<div class="input-group">

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


@error('barang_id')

    <div class="invalid-feedback d-block">
        {{ $message }}
    </div>

@enderror


</div>

{{-- ========================================================= --}}
{{-- PESAN PENCARIAN KODE --}}
{{-- ========================================================= --}}

<div
    id="kodeSearchMessage"
    class="mt-2"
    style="display: none;">
</div>

{{-- ========================================================= --}}
{{-- AREA SCANNER --}}
{{-- ========================================================= --}}

<div
    id="qrScannerContainer"
    class="mb-3"
    style="display: none;">

<div class="card border">

    <div class="card-header">

        <strong>
            <i class="fas fa-camera"></i>
            Scan QR Barang
        </strong>

    </div>


    <div class="card-body">

        <div
            id="qr-reader"
            style="width: 100%;">
        </div>


        <div
            id="qrMessage"
            class="mt-3 alert alert-info">

            Arahkan kamera ke QR Code barang.

        </div>


        <button
            type="button"
            class="btn btn-secondary"
            id="btnCloseScanner">

            <i class="fas fa-times"></i>
            Tutup Scanner

        </button>

    </div>

</div>

</div>

{{-- ========================================================= --}}
{{-- INFO BARANG --}}
{{-- ========================================================= --}}

<div
    id="infoBarang"
    class="alert alert-secondary"
    style="display: none;">

<strong>
    Barang Terpilih
</strong>

<div class="mt-2">

    <div>
        <strong>Kode:</strong>
        <span id="infoKode">-</span>
    </div>

    <div>
        <strong>Nama:</strong>
        <span id="infoNama">-</span>
    </div>

    <div>
        <strong>Stok Sistem:</strong>
        <span id="infoStok">-</span>
    </div>

</div>

</div>

{{-- ========================================================= --}}
{{-- TANGGAL + STOK SISTEM --}}
{{-- ========================================================= --}}

<div class="row">

<div class="col-md-6">

    <label class="form-label">
        Tanggal Opname
    </label>

    <input
        type="date"
        name="tanggal_opname"
        class="form-control @error('tanggal_opname') is-invalid @enderror"
        value="{{ old('tanggal_opname', date('Y-m-d')) }}">

    @error('tanggal_opname')

        <div class="invalid-feedback">
            {{ $message }}
        </div>

    @enderror

</div>


<div class="col-md-6">

    <label class="form-label">
        Stok Sistem
    </label>

    <input
        type="number"
        step="0.01"
        id="stok_sistem"
        class="form-control"
        readonly>

</div>

</div>

{{-- ========================================================= --}}
{{-- STOK FISIK + SELISIH --}}
{{-- ========================================================= --}}

<div class="row mt-3">

<div class="col-md-6">

    <label class="form-label">

        Stok Fisik
        <span class="text-danger">*</span>

    </label>

    <input
        type="number"
        step="0.01"
        min="0"
        name="stok_fisik"
        id="stok_fisik"
        value="{{ old('stok_fisik') }}"
        class="form-control @error('stok_fisik') is-invalid @enderror">

    @error('stok_fisik')

        <div class="invalid-feedback">
            {{ $message }}
        </div>

    @enderror

</div>


<div class="col-md-6">

    <label class="form-label">
        Selisih
    </label>

    <input
        type="number"
        step="0.01"
        id="selisih"
        class="form-control"
        readonly>

</div>

</div>

{{-- ========================================================= --}}
{{-- KETERANGAN --}}
{{-- ========================================================= --}}

<div class="mt-3">

<label class="form-label">
    Keterangan
</label>

<textarea
    name="keterangan"
    rows="3"
    class="form-control">{{ old('keterangan') }}</textarea>

</div>

<hr>

{{-- ========================================================= --}}
{{-- BUTTON --}}
{{-- ========================================================= --}}

<div class="d-flex justify-content-end">

<a
    href="{{ route('stock-opname.index') }}"
    class="btn btn-secondary mr-2">

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

{{-- ========================================================= --}}
{{-- JAVASCRIPT --}}
{{-- ========================================================= --}}

@push('js')

{{-- HTML5 QR CODE --}}
<script
    src="https://unpkg.com/html5-qrcode"
    type="text/javascript">
</script>


<script>

    document.addEventListener('DOMContentLoaded', function () {


        /*
        |--------------------------------------------------------------------------
        | ELEMENT
        |--------------------------------------------------------------------------
        */

        const barangSelect =
            document.getElementById('barang_id');

        const stokSistem =
            document.getElementById('stok_sistem');

        const stokFisik =
            document.getElementById('stok_fisik');

        const selisih =
            document.getElementById('selisih');

        const btnScanQr =
            document.getElementById('btnScanQr');

        const btnCloseScanner =
            document.getElementById('btnCloseScanner');

        const qrScannerContainer =
            document.getElementById('qrScannerContainer');

        const qrMessage =
            document.getElementById('qrMessage');

        const infoBarang =
            document.getElementById('infoBarang');

        const infoKode =
            document.getElementById('infoKode');

        const infoNama =
            document.getElementById('infoNama');

        const infoStok =
            document.getElementById('infoStok');

        const inputKodeBarang =
            document.getElementById('inputKodeBarang');

        const btnCariKode =
            document.getElementById('btnCariKode');

        const kodeSearchMessage =
            document.getElementById('kodeSearchMessage');


        /*
        |--------------------------------------------------------------------------
        | VARIABLE SCANNER
        |--------------------------------------------------------------------------
        */

        let html5QrCode = null;

        let scannerRunning = false;

        let sedangMemproses = false;


        /*
        |--------------------------------------------------------------------------
        | HITUNG SELISIH
        |--------------------------------------------------------------------------
        */

        function hitungSelisih() {

            const option =
                barangSelect.options[
                    barangSelect.selectedIndex
                ];


            const stok =
                parseFloat(
                    option?.dataset.stok || 0
                );


            const fisik =
                parseFloat(
                    stokFisik.value || 0
                );


            stokSistem.value =
                stok;


            if (stokFisik.value === '') {

                selisih.value = '';

                return;

            }


            selisih.value =
                (fisik - stok).toFixed(2);

        }


        /*
        |--------------------------------------------------------------------------
        | TAMPILKAN BARANG
        |--------------------------------------------------------------------------
        */

        function tampilkanBarang() {

            const option =
                barangSelect.options[
                    barangSelect.selectedIndex
                ];


            if (!option || !option.value) {

                infoBarang.style.display =
                    'none';

                stokSistem.value =
                    '';

                selisih.value =
                    '';

                return;

            }


            const kode =
                option.dataset.kode || '-';

            const nama =
                option.dataset.nama || '-';

            const stok =
                option.dataset.stok || '0';

            const satuan =
                option.dataset.satuan || '';


            infoKode.textContent =
                kode;

            infoNama.textContent =
                nama;

            infoStok.textContent =
                stok + ' ' + satuan;


            infoBarang.style.display =
                'block';


            hitungSelisih();

        }


        /*
        |--------------------------------------------------------------------------
        | EVENT PILIH BARANG
        |--------------------------------------------------------------------------
        */

        barangSelect.addEventListener(
            'change',
            function () {

                tampilkanBarang();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | EVENT STOK FISIK
        |--------------------------------------------------------------------------
        */

        stokFisik.addEventListener(
            'input',
            function () {

                hitungSelisih();

            }
        );


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


                /*
                |--------------------------------------------------------------------------
                | PILIH BARANG
                |--------------------------------------------------------------------------
                */

                barangSelect.value =
                    data.id;


                /*
                |--------------------------------------------------------------------------
                | CEK OPTION
                |--------------------------------------------------------------------------
                */

                if (
                    barangSelect.value !=
                    data.id
                ) {

                    throw new Error(
                        'Barang ditemukan tetapi tidak tersedia pada daftar barang aktif.'
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | TRIGGER CHANGE
                |--------------------------------------------------------------------------
                */

                barangSelect.dispatchEvent(
                    new Event('change')
                );


                /*
                |--------------------------------------------------------------------------
                | PESAN BERHASIL
                |--------------------------------------------------------------------------
                */

                kodeSearchMessage.className =
                    'mt-2 alert alert-success';


                kodeSearchMessage.innerHTML =
                    '<strong>Barang ditemukan!</strong><br>' +
                    'Kode: ' +
                    data.kode_barang +
                    '<br>' +
                    'Nama: ' +
                    data.nama_barang;


                /*
                |--------------------------------------------------------------------------
                | KOSONGKAN INPUT
                |--------------------------------------------------------------------------
                */

                inputKodeBarang.value =
                    '';


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
        | BUTTON CARI KODE
        |--------------------------------------------------------------------------
        */

        btnCariKode.addEventListener(
            'click',
            function () {

                cariKodeBarang();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | ENTER PADA INPUT KODE
        |--------------------------------------------------------------------------
        */

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
        | PROSES HASIL QR
        |--------------------------------------------------------------------------
        */

        async function prosesQr(kode) {

            if (sedangMemproses) {

                return;

            }


            sedangMemproses =
                true;


            kode =
                kode.trim();


            qrMessage.className =
                'mt-3 alert alert-info';


            qrMessage.textContent =
                'QR terdeteksi. Mencari barang ' +
                kode +
                '...';


            try {

                const response =
                    await fetch(
                        "{{ url('/barang/cari-qr') }}/" +
                        encodeURIComponent(kode)
                    );


                if (!response.ok) {

                    throw new Error(
                        'Barang tidak ditemukan.'
                    );

                }


                const result =
                    await response.json();


                if (!result.success) {

                    throw new Error(
                        result.message ||
                        'Barang tidak ditemukan.'
                    );

                }


                const data =
                    result.data;


                /*
                |--------------------------------------------------------------------------
                | PILIH BARANG
                |--------------------------------------------------------------------------
                */

                barangSelect.value =
                    data.id;


                /*
                |--------------------------------------------------------------------------
                | CEK OPTION
                |--------------------------------------------------------------------------
                */

                if (
                    barangSelect.value !=
                    data.id
                ) {

                    throw new Error(
                        'Barang ditemukan di database, tetapi tidak tersedia pada daftar barang aktif.'
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | TRIGGER CHANGE
                |--------------------------------------------------------------------------
                */

                barangSelect.dispatchEvent(
                    new Event('change')
                );


                /*
                |--------------------------------------------------------------------------
                | PESAN BERHASIL
                |--------------------------------------------------------------------------
                */

                qrMessage.className =
                    'mt-3 alert alert-success';


                qrMessage.textContent =
                    'Barang berhasil ditemukan: ' +
                    data.nama_barang;


                /*
                |--------------------------------------------------------------------------
                | TUTUP SCANNER
                |--------------------------------------------------------------------------
                */

                await tutupScanner();


            } catch (error) {

                console.error(
                    'QR ERROR:',
                    error
                );


                qrMessage.className =
                    'mt-3 alert alert-danger';


                qrMessage.textContent =
                    error.message ||
                    'Terjadi kesalahan saat membaca QR.';

            }


            sedangMemproses =
                false;

        }


        /*
        |--------------------------------------------------------------------------
        | BUKA SCANNER
        |--------------------------------------------------------------------------
        */

        /*
|--------------------------------------------------------------------------
| BUKA SCANNER
|--------------------------------------------------------------------------
*/

    btnScanQr.addEventListener(
        'click',
        async function () {

            if (scannerRunning) {
                return;
            }

            qrScannerContainer.style.display =
                'block';

            qrMessage.className =
                'mt-3 alert alert-info';

            qrMessage.textContent =
                'Memeriksa kamera...';

            try {

                /*
                |--------------------------------------------------------------------------
                | BERSIHKAN SCANNER
                |--------------------------------------------------------------------------
                */

                const qrReader =
                    document.getElementById('qr-reader');

                qrReader.innerHTML = '';

                /*
                |--------------------------------------------------------------------------
                | SAMAKAN UKURAN CONTAINER
                |--------------------------------------------------------------------------
                */

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


                /*
                |--------------------------------------------------------------------------
                | PRIORITASKAN KAMERA BELAKANG
                |--------------------------------------------------------------------------
                */

                const kameraBelakang =
                    cameras.find(function(camera) {

                        const label =
                            (camera.label || '')
                            .toLowerCase();

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
                | MULAI KAMERA
                |--------------------------------------------------------------------------
                */

                await html5QrCode.start(

                    cameraId,

                    {

                        /*
                        |--------------------------------------------------------------
                        | Sensitivitas scanner
                        |--------------------------------------------------------------
                        */

                        fps: 20,


                        /*
                        |--------------------------------------------------------------
                        | Ukuran area pembacaan QR
                        |--------------------------------------------------------------
                        */

                        qrbox: {
                            width: 450,
                            height: 450
                        },


                        /*
                        |--------------------------------------------------------------
                        | Rasio kamera
                        |--------------------------------------------------------------
                        */

                        aspectRatio: 1.0,


                        disableFlip: false,


                        /*
                        |--------------------------------------------------------------
                        | Hanya QR Code
                        |--------------------------------------------------------------
                        */

                        formatsToSupport: [
                            Html5QrcodeSupportedFormats.QR_CODE
                        ],


                        /*
                        |--------------------------------------------------------------
                        | Gunakan BarcodeDetector jika browser mendukung
                        |--------------------------------------------------------------
                        */

                        experimentalFeatures: {
                            useBarCodeDetectorIfSupported: true
                        }

                    },


                    /*
                    |--------------------------------------------------------------------------
                    | QR BERHASIL TERDETEKSI
                    |--------------------------------------------------------------------------
                    */

                    function(decodedText) {

                        console.log(
                            'QR TERDETEKSI:',
                            decodedText
                        );


                        prosesQr(
                            decodedText
                        );

                    },


                    /*
                    |--------------------------------------------------------------------------
                    | ERROR FRAME
                    |--------------------------------------------------------------------------
                    */

                    function(errorMessage) {

                        // Error pembacaan frame normal.
                        // Tidak perlu ditampilkan.

                    }

                );


                /*
                |--------------------------------------------------------------------------
                | SCANNER AKTIF
                |--------------------------------------------------------------------------
                */

                scannerRunning =
                    true;


                /*
                |--------------------------------------------------------------------------
                | SAMAKAN UKURAN VIDEO DENGAN BARANG KELUAR
                |--------------------------------------------------------------------------
                */

                const video =
                    qrReader.querySelector(
                        'video'
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


                /*
                |--------------------------------------------------------------------------
                | PESAN STATUS
                |--------------------------------------------------------------------------
                */

                qrMessage.className =
                    'mt-3 alert alert-success';

                qrMessage.textContent =
                    'Kamera aktif. Arahkan QR Code ke dalam kotak.';


            } catch (error) {

                console.error(
                    'CAMERA ERROR:',
                    error
                );


                qrMessage.className =
                    'mt-3 alert alert-danger';


                qrMessage.textContent =
                    error.message ||
                    'Kamera tidak dapat digunakan.';


                html5QrCode =
                    null;


                scannerRunning =
                    false;

            }

        }
    );


        /*
        |--------------------------------------------------------------------------
        | TUTUP SCANNER
        |--------------------------------------------------------------------------
        */

        async function tutupScanner() {

            if (
                html5QrCode &&
                scannerRunning
            ) {

                try {

                    await html5QrCode.stop();

                } catch (error) {

                    console.error(
                        'STOP SCANNER ERROR:',
                        error
                    );

                }


                try {

                    html5QrCode.clear();

                } catch (error) {

                    console.error(
                        'CLEAR SCANNER ERROR:',
                        error
                    );

                }


                html5QrCode =
                    null;


                scannerRunning =
                    false;

            }


            qrScannerContainer.style.display =
                'none';

        }


        /*
        |--------------------------------------------------------------------------
        | TUTUP SCANNER
        |--------------------------------------------------------------------------
        */

        btnCloseScanner.addEventListener(
            'click',
            async function () {

                await tutupScanner();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | LOAD AWAL
        |--------------------------------------------------------------------------
        */

        tampilkanBarang();

    });

</script>

@endpush
