@extends('adminlte::page')

@section('title', 'Data Barang')

@section('content')

<br>

<x-shared.page-header>

<x-slot:title>
    Data Barang
</x-slot:title>

<x-slot:action>

    @can('barang.create')

        <a href="{{ route('barang.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Tambah Barang
        </a>

    @endcan

</x-slot:action>

</x-shared.page-header>

<x-shared.alert />

<x-shared.card>

{{-- FILTER --}}
<form method="GET" class="mb-3">

    <div class="row">

        <div class="col-md-4">

            <input
                type="text"
                name="search"
                value="{{ $search }}"
                class="form-control"
                placeholder="Cari kode atau nama barang">

        </div>


        <div class="col-md-3">

            <select
                name="kategori"
                class="form-control">

                <option value="">
                    Semua Kategori
                </option>

                @foreach($kategoris as $kategoriItem)

                    <option
                        value="{{ $kategoriItem->id }}"
                        @selected($kategori == $kategoriItem->id)>

                        {{ $kategoriItem->nama_kategori }}

                    </option>

                @endforeach

            </select>

        </div>


        <div class="col-md-2">

            <button
                type="submit"
                class="btn btn-primary w-100">

                <i class="fas fa-search"></i>
                Cari

            </button>

        </div>


        <div class="col-md-2">

            <a
                href="{{ route('barang.index') }}"
                class="btn btn-secondary w-100">

                <i class="fas fa-sync-alt"></i>
                Reset

            </a>

        </div>

    </div>

</form>


{{-- TABLE --}}
<div class="table-responsive">

    <table class="table table-bordered table-hover">

        <thead class="table-light">

            <tr>

                <th>No</th>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Supplier</th>
                <th>Satuan</th>
                <th>Lokasi Rak</th>
                <th class="text-center">Stok</th>
                <th class="text-center">Minimum</th>
                <th class="text-center">Status Stok</th>
                <th class="text-center">Status</th>
                <th class="text-center">QR</th>

                @canany(['barang.edit', 'barang.delete'])

                    <th width="170" class="text-center">
                        Aksi
                    </th>

                @endcanany

            </tr>

        </thead>


        <tbody>

            @forelse($data as $item)

                <tr>

                    {{-- NO --}}
                    <td>

                        {{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}

                    </td>


                    {{-- KODE --}}
                    <td>

                        {{ $item->kode_barang }}

                    </td>


                    {{-- NAMA --}}
                    <td>

                        {{ $item->nama_barang }}

                    </td>


                    {{-- KATEGORI --}}
                    <td>

                        {{ $item->kategori->nama_kategori }}

                    </td>


                    {{-- SUPPLIER --}}
                    <td>

                        {{ $item->supplier->nama_supplier }}

                    </td>


                    {{-- SATUAN --}}
                    <td>

                        {{ $item->satuan->nama_satuan }}

                    </td>


                    {{-- LOKASI RAK --}}
                    <td>

                        {{ $item->lokasi_rak ?? '-' }}

                    </td>


                    {{-- STOK --}}
                    <td class="text-center">

                        {{ $item->stok }}

                    </td>


                    {{-- MINIMUM --}}
                    <td class="text-center">

                        {{ $item->stok_minimum }}

                    </td>


                    {{-- STATUS STOK --}}
                    <td class="text-center">

                        @if($item->stok == 0)

                            <span class="badge badge-danger">
                                Kosong
                            </span>

                        @elseif($item->stok <= $item->stok_minimum)

                            <span class="badge badge-warning">
                                Menipis
                            </span>

                        @else

                            <span class="badge badge-success">
                                Aman
                            </span>

                        @endif

                    </td>


                    {{-- STATUS --}}
                    <td class="text-center">

                        @if($item->status == 'Aktif')

                            <span class="badge badge-success">
                                Aktif
                            </span>

                        @else

                            <span class="badge badge-secondary">
                                Nonaktif
                            </span>

                        @endif

                    </td>


                    {{-- QR --}}
                    <td class="text-center">

                        <button
                            type="button"
                            class="btn btn-dark btn-sm btn-show-qr"
                            data-kode="{{ $item->kode_barang }}"
                            data-nama="{{ $item->nama_barang }}"
                            title="Lihat QR Code">

                            <i class="fas fa-qrcode"></i>

                        </button>

                    </td>


                    {{-- AKSI --}}
                    @canany(['barang.edit', 'barang.delete'])

                        <td class="text-center">

                            @can('barang.edit')

                                <a
                                    href="{{ route('barang.edit', $item) }}"
                                    class="btn btn-warning btn-sm"
                                    title="Edit">

                                    <i class="fas fa-edit"></i>

                                </a>

                            @endcan


                            @can('barang.delete')

                                <form
                                    action="{{ route('barang.destroy', $item) }}"
                                    method="POST"
                                    class="d-inline">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-danger btn-sm"
                                        title="Hapus"
                                        onclick="return confirm('Yakin ingin menghapus barang ini?')">

                                        <i class="fas fa-trash"></i>

                                    </button>

                                </form>

                            @endcan

                        </td>

                    @endcanany

                </tr>


            @empty

                <x-shared.empty
                    :colspan="auth()->user()->canany(['barang.edit', 'barang.delete']) ? 13 : 12"
                />

            @endforelse

        </tbody>

    </table>

</div>


{{-- PAGINATION --}}
<div class="mt-3">

    {{ $data->links() }}

</div>

</x-shared.card>

{{-- ========================================================= --}}
{{-- MODAL QR --}}
{{-- ========================================================= --}}

<div
    class="modal fade"
    id="qrModal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="qrModalLabel"
    aria-hidden="true">

<div
    class="modal-dialog modal-dialog-centered"
    role="document">

    <div class="modal-content">


        {{-- MODAL HEADER --}}
        <div class="modal-header">

            <h5
                class="modal-title"
                id="qrModalLabel">

                <i class="fas fa-qrcode"></i>
                QR Code Barang

            </h5>


            <button
                type="button"
                class="close"
                data-dismiss="modal"
                aria-label="Close">

                <span aria-hidden="true">
                    &times;
                </span>

            </button>

        </div>


        {{-- MODAL BODY --}}
        <div class="modal-body text-center">

            <h5 id="qrNamaBarang">
                -
            </h5>


            <p class="text-muted mb-3">

                Kode:
                <strong id="qrKodeBarang">
                    -
                </strong>

            </p>


            {{-- QR CODE --}}
            <div
                id="qrcode"
                class="d-flex justify-content-center mb-3">
            </div>


            <p class="small text-muted mb-0">

                QR Code ini digunakan untuk
                identifikasi barang.

            </p>

        </div>


        {{-- MODAL FOOTER --}}
        <div class="modal-footer">

            <button
                type="button"
                class="btn btn-secondary"
                data-dismiss="modal">

                <i class="fas fa-times mr-1"></i>
                Tutup

            </button>

            <button
                type="button"
                class="btn btn-success"
                id="btnDownloadQr">

                <i class="fas fa-download mr-1"></i>
                Unduh QR

            </button>

        </div>

    </div>

</div>

</div>

{{-- ========================================================= --}}
{{-- JAVASCRIPT QR --}}
{{-- ========================================================= --}}

@push('js')

{{-- QR CODE GENERATOR --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>

    document.addEventListener('DOMContentLoaded', function () {

        const qrContainer =
            document.getElementById('qrcode');

        const qrKode =
            document.getElementById('qrKodeBarang');

        const qrNama =
            document.getElementById('qrNamaBarang');

        const btnDownloadQr =
            document.getElementById('btnDownloadQr');


        /*
         * KODE BARANG AKTIF
         *
         * Digunakan ketika QR sedang ditampilkan.
         */
        let kodeBarangAktif = '';


        /*
         * ========================================================
         * TOMBOL LIHAT QR
         * ========================================================
         */

        document
            .querySelectorAll('.btn-show-qr')
            .forEach(function (button) {

                button.addEventListener('click', function () {

                    const kode =
                        this.dataset.kode;

                    const nama =
                        this.dataset.nama;


                    /*
                     * Simpan kode barang yang sedang aktif
                     */
                    kodeBarangAktif = kode;


                    /*
                     * Bersihkan QR sebelumnya
                     */
                    qrContainer.innerHTML = '';


                    /*
                     * Tampilkan nama barang
                     */
                    qrNama.textContent =
                        nama;


                    /*
                     * Tampilkan kode barang
                     */
                    qrKode.textContent =
                        kode;


                    /*
                     * Generate QR Code
                     *
                     * Isi QR hanya kode barang.
                     */
                    new QRCode(
                        qrContainer,
                        {
                            text: kode,
                            width: 220,
                            height: 220,
                            correctLevel: QRCode.CorrectLevel.H
                        }
                    );


                    /*
                     * Buka modal
                     */
                    $('#qrModal').modal('show');

                });

            });


        /*
         * ========================================================
         * UNDUH QR CODE
         * ========================================================
         */

        btnDownloadQr.addEventListener('click', function () {

            if (!kodeBarangAktif) {

                alert('QR Code belum tersedia.');

                return;
            }


            /*
             * QRCode.js biasanya menghasilkan canvas.
             */
            const canvas =
                qrContainer.querySelector('canvas');


            if (canvas) {

                const link =
                    document.createElement('a');

                link.href =
                    canvas.toDataURL('image/png');

                link.download =
                    'QR-' + kodeBarangAktif + '.png';

                link.click();

                return;
            }


            /*
             * Fallback jika QR berupa image.
             */
            const image =
                qrContainer.querySelector('img');


            if (image) {

                const link =
                    document.createElement('a');

                link.href =
                    image.src;

                link.download =
                    'QR-' + kodeBarangAktif + '.png';

                link.click();

                return;
            }


            alert('QR Code belum selesai dibuat.');

        });


        /*
         * ========================================================
         * BERSIHKAN MODAL
         * ========================================================
         */

        $('#qrModal').on(
            'hidden.bs.modal',
            function () {

                qrContainer.innerHTML = '';

                qrKode.textContent = '-';

                qrNama.textContent = '-';

                kodeBarangAktif = '';

            }
        );

    });

</script>

@endpush

@endsection
