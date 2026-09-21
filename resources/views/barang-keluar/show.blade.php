@extends('adminlte::page')

@section('title', 'Detail Barang Keluar')

@section('content')

<x-shared.page-header>

<x-slot:title>
    Detail Barang Keluar
</x-slot:title>

<x-slot:action>

    @if($barangKeluar->jenis_keluar == 'Penjualan')

        <a href="{{ route('barang-keluar.invoice', $barangKeluar) }}"
           target="_blank"
           class="btn btn-success">

            <i class="fas fa-file-invoice-dollar"></i>
            Cetak Faktur

        </a>

        <a href="{{ route('barang-keluar.surat-jalan', $barangKeluar) }}"
           target="_blank"
           class="btn btn-primary ml-1">

            <i class="fas fa-truck"></i>
            Cetak Surat Jalan

        </a>

    @endif

    <a href="{{ route('barang-keluar.index') }}"
       class="btn btn-secondary">

        <i class="fas fa-arrow-left"></i>
        Kembali

    </a>

</x-slot:action>

</x-shared.page-header>

<x-shared.card>

@php

/*
|--------------------------------------------------------------------------
| CEK PENJUALAN
|--------------------------------------------------------------------------
*/

$isPenjualan = $barangKeluar->jenis_keluar == 'Penjualan';


/*
|--------------------------------------------------------------------------
| DETAIL PENJUALAN
|--------------------------------------------------------------------------
*/

$details = $barangKeluar->details ?? collect();


/*
|--------------------------------------------------------------------------
| TOTAL SATUAN DASAR PENJUALAN
|--------------------------------------------------------------------------
*/

$totalJumlahDasar = 0;

if ($isPenjualan && $details->count() > 0) {

    $totalJumlahDasar = $details->sum(function ($detail) {

        return (float) ($detail->jumlah_dasar ?? 0);

    });

} else {

    $totalJumlahDasar =
        (float) ($barangKeluar->jumlah_dasar ?? 0);

}

@endphp

{{-- ========================================================= --}}
{{-- INFORMASI TRANSAKSI --}}
{{-- ========================================================= --}}

<div class="row">

{{-- ========================================================= --}}
{{-- KOLOM KIRI --}}
{{-- ========================================================= --}}

<div class="col-md-6">

    <table class="table table-bordered">

        <tr>

            <th width="180">
                Kode Transaksi
            </th>

            <td>
                {{ $barangKeluar->kode_transaksi }}
            </td>

        </tr>


        <tr>

            <th>
                Tanggal Keluar
            </th>

            <td>

                {{ \Carbon\Carbon::parse($barangKeluar->tanggal_keluar)->format('d-m-Y') }}

            </td>

        </tr>


        @if(!$isPenjualan)

            {{-- ================================================= --}}
            {{-- BARANG NON PENJUALAN --}}
            {{-- ================================================= --}}

            <tr>

                <th>
                    Barang
                </th>

                <td>

                    {{ $barangKeluar->barang->nama_barang ?? '-' }}

                </td>

            </tr>


            <tr>

                <th>
                    Jumlah Keluar
                </th>

                <td>

                    <strong>

                        {{ rtrim(rtrim(number_format($barangKeluar->jumlah, 2, ',', '.'), '0'), ',') }}

                        {{ $barangKeluar->satuan->nama_satuan ?? '-' }}

                    </strong>

                </td>

            </tr>


            <tr>

                <th>
                    Konversi
                </th>

                <td>

                    @php

                        $nilaiKonversi =
                            (float) ($barangKeluar->nilai_konversi ?? 0);

                        if ($nilaiKonversi <= 0) {

                            $konversi =
                                $barangKeluar->barang
                                    ->konversiSatuan
                                    ->firstWhere(
                                        'satuan_id',
                                        $barangKeluar->satuan_id
                                    );

                            if ($konversi) {

                                $nilaiKonversi =
                                    (float) $konversi->nilai_konversi;

                            }

                        }

                        if (
                            $barangKeluar->satuan_id
                            ==
                            $barangKeluar->barang->satuan_id
                        ) {

                            $nilaiKonversi = 1;

                        }

                    @endphp

                    1
                    {{ $barangKeluar->satuan->nama_satuan ?? '-' }}

                    =

                    {{ rtrim(rtrim(number_format($nilaiKonversi, 2, ',', '.'), '0'), ',') }}

                    {{ $barangKeluar->barang->satuan->nama_satuan ?? '-' }}

                </td>

            </tr>


            <tr>

                <th>
                    Jumlah dalam Satuan Dasar
                </th>

                <td>

                    @php

                        $jumlahDasar =
                            (float) ($barangKeluar->jumlah_dasar ?? 0);

                        if (
                            $jumlahDasar <= 0
                            &&
                            $nilaiKonversi > 0
                        ) {

                            $jumlahDasar =
                                (float) $barangKeluar->jumlah
                                *
                                $nilaiKonversi;

                        }

                    @endphp

                    <strong>

                        {{ rtrim(rtrim(number_format($jumlahDasar, 2, ',', '.'), '0'), ',') }}

                        {{ $barangKeluar->barang->satuan->nama_satuan ?? '-' }}

                    </strong>

                </td>

            </tr>

        @endif


        @if($isPenjualan)

            {{-- ================================================= --}}
            {{-- PELANGGAN --}}
            {{-- ================================================= --}}

            <tr>

                <th>
                    Pelanggan
                </th>

                <td>

                    {{ $barangKeluar->pelanggan->nama_pelanggan ?? '-' }}

                </td>

            </tr>


            {{-- ================================================= --}}
            {{-- TOTAL PENJUALAN --}}
            {{-- ================================================= --}}

            <tr>

                <th>
                    Total Penjualan
                </th>

                <td>

                    <strong class="text-success">

                        Rp
                        {{ number_format($barangKeluar->total_harga ?? 0, 0, ',', '.') }}

                    </strong>

                </td>

            </tr>

        @endif

    </table>

</div>


{{-- ========================================================= --}}
{{-- KOLOM KANAN --}}
{{-- ========================================================= --}}

<div class="col-md-6">

    <table class="table table-bordered">

        <tr>

            <th width="180">
                Jenis Keluar
            </th>

            <td>

                @switch($barangKeluar->jenis_keluar)

                    @case('Penjualan')

                        <span class="badge bg-primary">
                            Penjualan
                        </span>

                        @break


                    @case('Transfer ke Toko')

                        <span class="badge bg-success">
                            Transfer ke Toko
                        </span>

                        @break


                    @case('Rusak')

                        <span class="badge bg-danger">
                            Rusak
                        </span>

                        @break


                    @case('Kadaluarsa')

                        <span class="badge bg-warning text-dark">
                            Kadaluarsa
                        </span>

                        @break


                    @case('Pemakaian Internal')

                        <span class="badge bg-info text-dark">
                            Pemakaian Internal
                        </span>

                        @break


                    @case('Return')

                        <span class="badge bg-secondary">
                            Return
                        </span>

                        @break


                    @default

                        <span class="badge bg-secondary">
                            {{ $barangKeluar->jenis_keluar }}
                        </span>

                @endswitch

            </td>

        </tr>


        <tr>

            <th>
                Tujuan
            </th>

            <td>

                {{ $barangKeluar->tujuan ?: '-' }}

            </td>

        </tr>


        <tr>

            <th>
                Petugas
            </th>

            <td>

                {{ $barangKeluar->user->name ?? '-' }}

            </td>

        </tr>


        <tr>

            <th>
                Keterangan
            </th>

            <td>

                {{ $barangKeluar->keterangan ?: '-' }}

            </td>

        </tr>

    </table>

</div>

</div>

{{-- ============================================================= --}}
{{-- DETAIL BARANG PENJUALAN --}}
{{-- ============================================================= --}}

@if($isPenjualan)

<div class="card mt-3">

    <div class="card-header bg-primary">

        <h5 class="mb-0">

            <i class="fas fa-shopping-cart mr-1"></i>

            Detail Barang Penjualan

        </h5>

    </div>


    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-bordered table-striped mb-0">

                <thead>

                    <tr>

                        <th width="50">
                            No
                        </th>

                        <th>
                            Kode Barang
                        </th>

                        <th>
                            Nama Barang
                        </th>

                        <th class="text-center">
                            Jumlah
                        </th>

                        <th class="text-center">
                            Konversi
                        </th>

                        <th class="text-center">
                            Satuan Dasar
                        </th>

                        <th class="text-right">
                            Harga / Satuan
                        </th>

                        <th class="text-right">
                            Subtotal
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($details as $index => $detail)

                        @php

                            $jumlah =
                                (float) ($detail->jumlah ?? 0);

                            $nilaiKonversi =
                                (float) ($detail->nilai_konversi ?? 0);

                            if ($nilaiKonversi <= 0) {

                                if (
                                    $detail->satuan_id
                                    ==
                                    $detail->barang->satuan_id
                                ) {

                                    $nilaiKonversi = 1;

                                } else {

                                    $konversi =
                                        $detail->barang
                                            ->konversiSatuan
                                            ->firstWhere(
                                                'satuan_id',
                                                $detail->satuan_id
                                            );

                                    if ($konversi) {

                                        $nilaiKonversi =
                                            (float) $konversi->nilai_konversi;

                                    }

                                }

                            }

                            $jumlahDasar =
                                (float) ($detail->jumlah_dasar ?? 0);

                            if (
                                $jumlahDasar <= 0
                                &&
                                $nilaiKonversi > 0
                            ) {

                                $jumlahDasar =
                                    $jumlah
                                    *
                                    $nilaiKonversi;

                            }

                        @endphp


                        <tr>

                            <td class="text-center">

                                {{ $index + 1 }}

                            </td>


                            <td>

                                {{ $detail->barang->kode_barang ?? '-' }}

                            </td>


                            <td>

                                <strong>

                                    {{ $detail->barang->nama_barang ?? '-' }}

                                </strong>

                            </td>


                            <td class="text-center">

                                {{ rtrim(rtrim(number_format($jumlah, 2, ',', '.'), '0'), ',') }}

                                {{ $detail->satuan->nama_satuan ?? '-' }}

                            </td>


                            <td class="text-center">

                                1
                                {{ $detail->satuan->nama_satuan ?? '-' }}

                                =

                                {{ rtrim(rtrim(number_format($nilaiKonversi, 2, ',', '.'), '0'), ',') }}

                                {{ $detail->barang->satuan->nama_satuan ?? '-' }}

                            </td>


                            <td class="text-center">

                                {{ rtrim(rtrim(number_format($jumlahDasar, 2, ',', '.'), '0'), ',') }}

                                {{ $detail->barang->satuan->nama_satuan ?? '-' }}

                            </td>


                            <td class="text-right">

                                Rp
                                {{ number_format($detail->harga_jual ?? 0, 0, ',', '.') }}

                            </td>


                            <td class="text-right">

                                <strong>

                                    Rp
                                    {{ number_format($detail->subtotal ?? ($detail->jumlah * $detail->harga_jual), 0, ',', '.') }}

                                </strong>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="8"
                                class="text-center text-muted py-4">

                                <i class="fas fa-box-open fa-2x mb-2"></i>

                                <br>

                                Tidak ada detail barang penjualan.

                            </td>

                        </tr>

                    @endforelse

                </tbody>


                @if($details->count() > 0)

                    <tfoot>

                        <tr>

                            <th colspan="7"
                                class="text-right">

                                Total Penjualan

                            </th>

                            <th class="text-right text-success">

                                Rp
                                {{ number_format($details->sum('subtotal'), 0, ',', '.') }}

                            </th>

                        </tr>

                    </tfoot>

                @endif

            </table>

        </div>

    </div>

</div>

@endif

{{-- ============================================================= --}}
{{-- INFORMASI PENGURANGAN STOK --}}
{{-- ============================================================= --}}

<div class="alert alert-info mt-3 mb-0">

<div>

    <strong>

        <i class="fas fa-boxes mr-1"></i>

        Pengurangan Stok

    </strong>

</div>


<div class="mt-1">

    @if($isPenjualan && $details->count() > 0)

        Transaksi ini mengurangi stok dari
        <strong>
            {{ $details->count() }}
        </strong>
        jenis barang dengan total pengurangan sebesar
        <strong>
            {{ rtrim(rtrim(number_format($totalJumlahDasar, 2, ',', '.'), '0'), ',') }}
        </strong>
        dalam satuan dasar masing-masing barang.

    @else

        Transaksi ini mengurangi stok sebesar

        <strong>

            {{ rtrim(rtrim(number_format($totalJumlahDasar, 2, ',', '.'), '0'), ',') }}

            {{ $barangKeluar->barang->satuan->nama_satuan ?? '-' }}

        </strong>

        dari stok barang.

    @endif

</div>

</div>

</x-shared.card>

@endsection
