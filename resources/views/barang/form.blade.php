@php
    $isEdit = isset($barang);

    $konversi = $isEdit
        ? $barang->konversiSatuan->first()
        : null;
@endphp

<div class="mb-3">
    <label class="form-label">
        Kode Barang
    </label>

    <input
        type="text"
        name="kode_barang"
        class="form-control"
        value="{{ old('kode_barang', $kode ?? ($barang->kode_barang ?? '')) }}"
        readonly>
</div>

<div class="mb-3">
    <label class="form-label">
        Nama Barang
        <span class="text-danger">*</span>
    </label>

    <input
        type="text"
        name="nama_barang"
        class="form-control @error('nama_barang') is-invalid @enderror"
        value="{{ old('nama_barang', $barang->nama_barang ?? '') }}"
        placeholder="Masukkan nama barang">

    @error('nama_barang')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

<div class="row">

    <div class="col-md-6">

        <label class="form-label">
            Kategori
            <span class="text-danger">*</span>
        </label>

        <select
            name="kategori_id"
            class="form-select @error('kategori_id') is-invalid @enderror">

            <option value="">
                -- Pilih Kategori --
            </option>

            @foreach($kategori as $item)

                <option
                    value="{{ $item->id }}"
                    @selected(
                        old(
                            'kategori_id',
                            $barang->kategori_id ?? ''
                        ) == $item->id
                    )>

                    {{ $item->nama_kategori }}

                </option>

            @endforeach

        </select>

        @error('kategori_id')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>

    <div class="col-md-6">

        <label class="form-label">
            Supplier Utama
            <span class="text-danger">*</span>
        </label>

        <select
            name="supplier_id"
            class="form-select @error('supplier_id') is-invalid @enderror">

            <option value="">
                -- Pilih Supplier --
            </option>

            @foreach($supplier as $item)

                <option
                    value="{{ $item->id }}"
                    @selected(
                        old(
                            'supplier_id',
                            $barang->supplier_id ?? ''
                        ) == $item->id
                    )>

                    {{ $item->nama_supplier }}

                </option>

            @endforeach

        </select>

        @error('supplier_id')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>

</div>

<div class="row mt-3">

    <div class="col-md-6">

        <label class="form-label">
            Satuan Dasar
            <span class="text-danger">*</span>
        </label>

        <select
            name="satuan_id"
            id="satuan_id"
            class="form-select @error('satuan_id') is-invalid @enderror">

            <option value="">
                -- Pilih Satuan --
            </option>

            @foreach($satuan as $item)

                <option
                    value="{{ $item->id }}"
                    data-nama="{{ strtoupper($item->kode_satuan ?: $item->nama_satuan) }}"
                    @selected(
                        old(
                            'satuan_id',
                            $barang->satuan_id ?? ''
                        ) == $item->id
                    )>

                    {{ strtoupper($item->kode_satuan ?: $item->nama_satuan) }}

                </option>

            @endforeach

        </select>

        @error('satuan_id')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>

    <div class="col-md-6">

        <label class="form-label">
            Lokasi Rak
        </label>

        <input
            type="text"
            name="lokasi_rak"
            class="form-control @error('lokasi_rak') is-invalid @enderror"
            value="{{ old('lokasi_rak', $barang->lokasi_rak ?? '') }}"
            placeholder="Contoh : A-01">

        @error('lokasi_rak')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>

</div>


{{-- KONVERSI SATUAN --}}

<div class="card border mt-4">

    <div class="card-header bg-light">

        <h5 class="mb-0">
            <i class="fas fa-boxes me-1"></i>
            Konversi Satuan
        </h5>

    </div>

    <div class="card-body">

        <p class="text-muted small mb-3">
            Konversi bersifat opsional.
            Gunakan jika barang memiliki satuan kemasan
            seperti BOX, PACK, DUS, dan sebagainya.
        </p>

        <div class="row">

            {{-- SATUAN KEMASAN --}}

            <div class="col-md-6">

                <label class="form-label">
                    Satuan Kemasan
                </label>

                <select
                    name="konversi_satuan_id"
                    id="konversi_satuan_id"
                    class="form-select @error('konversi_satuan_id') is-invalid @enderror">

                    <option value="">
                        -- Tidak Ada Konversi --
                    </option>

                    @foreach($satuan as $item)

                        <option
                            value="{{ $item->id }}"
                            data-nama="{{ strtoupper($item->kode_satuan ?: $item->nama_satuan) }}"
                            @selected(
                                old(
                                    'konversi_satuan_id',
                                    $konversi->satuan_id ?? ''
                                ) == $item->id
                            )>

                            {{ strtoupper($item->kode_satuan ?: $item->nama_satuan) }}

                        </option>

                    @endforeach

                </select>

                @error('konversi_satuan_id')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            {{-- NILAI KONVERSI --}}

            <div class="col-md-6">

                <label class="form-label">
                    Isi dalam Satuan Dasar
                </label>

                <div class="input-group">

                    <input
                        type="number"
                        name="nilai_konversi"
                        id="nilai_konversi"
                        class="form-control @error('nilai_konversi') is-invalid @enderror"
                        min="1"
                        value="{{ old(
                            'nilai_konversi',
                            $konversi->nilai_konversi ?? ''
                        ) }}"
                        placeholder="Contoh: 50">

                    <span
                        class="input-group-text"
                        id="satuan-dasar-label">

                        -

                    </span>

                </div>

                @error('nilai_konversi')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                @enderror

            </div>

        </div>


        {{-- PREVIEW KONVERSI --}}

        <div
            id="preview-konversi"
            class="alert alert-info mt-3 mb-0 d-none">

            <i class="fas fa-info-circle me-1"></i>

            <strong>Konversi:</strong>

            <span id="text-konversi"></span>

        </div>

    </div>

</div>


{{-- STOK MINIMUM + STATUS --}}

<div class="row mt-3">

    <div class="col-md-6">

        <label class="form-label">
            Stok Minimum
            <span class="text-danger">*</span>
        </label>

        <input
            type="number"
            name="stok_minimum"
            class="form-control @error('stok_minimum') is-invalid @enderror"
            min="0"
            value="{{ old('stok_minimum', $barang->stok_minimum ?? 10) }}">

        @error('stok_minimum')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="col-md-6">

        <label class="form-label">
            Status
            <span class="text-danger">*</span>
        </label>

        <select
            name="status"
            class="form-select @error('status') is-invalid @enderror">

            <option
                value="Aktif"
                @selected(
                    old(
                        'status',
                        $barang->status ?? 'Aktif'
                    ) == 'Aktif'
                )>

                Aktif

            </option>

            <option
                value="Nonaktif"
                @selected(
                    old(
                        'status',
                        $barang->status ?? ''
                    ) == 'Nonaktif'
                )>

                Nonaktif

            </option>

        </select>

        @error('status')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>

</div>


{{-- BUTTON --}}

<div class="mt-4">

    <button
        type="submit"
        class="btn btn-primary">

        <i class="fas fa-save me-1"></i>

        Simpan

    </button>

    <a
        href="{{ route('barang.index') }}"
        class="btn btn-secondary">

        Kembali

    </a>

</div>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const satuanDasar =
        document.getElementById('satuan_id');

    const satuanKemasan =
        document.getElementById('konversi_satuan_id');

    const nilaiKonversi =
        document.getElementById('nilai_konversi');

    const labelSatuanDasar =
        document.getElementById('satuan-dasar-label');

    const preview =
        document.getElementById('preview-konversi');

    const textKonversi =
        document.getElementById('text-konversi');


    if (
        !satuanDasar ||
        !satuanKemasan ||
        !nilaiKonversi
    ) {
        return;
    }


    function getNamaSatuan(select) {

        if (!select.value) {
            return '';
        }

        const option =
            select.options[select.selectedIndex];

        return (
            option.dataset.nama ||
            option.text.trim()
        );

    }


    function updateKonversi() {

        const namaDasar =
            getNamaSatuan(satuanDasar);

        let namaKemasan =
            getNamaSatuan(satuanKemasan);

        const nilai =
            nilaiKonversi.value;


        // Update label satuan dasar

        labelSatuanDasar.textContent =
            namaDasar || '-';


        // Sembunyikan satuan dasar
        // dari pilihan satuan kemasan

        Array.from(
            satuanKemasan.options
        ).forEach(option => {

            if (!option.value) {
                return;
            }

            option.hidden =
                option.value === satuanDasar.value;

        });


        // Cegah satuan dasar menjadi
        // satuan kemasan

        if (
            satuanKemasan.value &&
            satuanKemasan.value === satuanDasar.value
        ) {

            satuanKemasan.value = '';

            namaKemasan = '';

        }


        // Preview

        if (
            namaDasar &&
            namaKemasan &&
            nilai &&
            Number(nilai) >= 1
        ) {

            textKonversi.textContent =
                `1 ${namaKemasan} = ${nilai} ${namaDasar}`;

            preview.classList.remove('d-none');

        } else {

            preview.classList.add('d-none');

        }

    }


    satuanDasar.addEventListener(
        'change',
        updateKonversi
    );


    satuanKemasan.addEventListener(
        'change',
        updateKonversi
    );


    nilaiKonversi.addEventListener(
        'input',
        updateKonversi
    );


    updateKonversi();

});

</script>