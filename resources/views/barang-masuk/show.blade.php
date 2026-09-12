@extends('adminlte::page')

@section('title', 'Detail Barang Masuk')

@section('content')

<x-shared.page-header>

<x-slot:title>
    Detail Barang Masuk
</x-slot:title>

<x-slot:action>

    <a
        href="{{ route('barang-masuk.index') }}"
        class="btn btn-secondary">

        <i class="fas fa-arrow-left"></i>

        Kembali

    </a>

</x-slot:action>

</x-shared.page-header>

<x-shared.card>

<div class="row">

{{-- INFORMASI TRANSAKSI --}}
<div class="col-md-6">

    <table class="table table-bordered">

        <tr>

            <th width="180">
                Kode Transaksi
            </th>

            <td>
                {{ $barangMasuk->kode_transaksi }}
            </td>

        </tr>


        <tr>

            <th>
                Tanggal Masuk
            </th>

            <td>
                {{ $barangMasuk->tanggal_masuk->format('d-m-Y') }}
            </td>

        </tr>


        <tr>

            <th>
                Barang
            </th>

            <td>
                {{ $barangMasuk->barang->nama_barang }}
            </td>

        </tr>


        <tr>

            <th>
                Supplier
            </th>

            <td>
                {{ $barangMasuk->supplier->nama_supplier }}
            </td>

        </tr>


        {{-- JUMLAH TRANSAKSI --}}
        <tr>

            <th>
                Jumlah Masuk
            </th>

            <td>

                <strong>
                    {{ number_format($barangMasuk->jumlah, 0, ',', '.') }}
                </strong>

                {{ $barangMasuk->satuan->nama_satuan }}

            </td>

        </tr>


        {{-- KONVERSI --}}
        <tr>

            <th>
                Konversi
            </th>

            <td>

                @if($barangMasuk->nilai_konversi > 1)

                    1
                    {{ strtoupper($barangMasuk->satuan->nama_satuan) }}

                    =

                    {{ number_format($barangMasuk->nilai_konversi, 0, ',', '.') }}
                    {{ strtoupper($barangMasuk->barang->satuan->nama_satuan) }}

                @else

                    -

                @endif

            </td>

        </tr>


        {{-- JUMLAH DASAR --}}
        <tr>

            <th>
                Jumlah dalam Satuan Dasar
            </th>

            <td>

                <strong>
                    {{ number_format($barangMasuk->jumlah_dasar, 0, ',', '.') }}
                </strong>

                {{ $barangMasuk->barang->satuan->nama_satuan }}

            </td>

        </tr>

    </table>

</div>


{{-- INFORMASI PEMBELIAN --}}
<div class="col-md-6">

    <table class="table table-bordered">

        {{-- HARGA PER SATUAN TRANSAKSI --}}
        <tr>

            <th width="180">
                Harga Beli
            </th>

            <td>

                <strong>
                    Rp {{ number_format($barangMasuk->harga_beli, 0, ',', '.') }}
                </strong>

                / {{ $barangMasuk->satuan->nama_satuan }}

            </td>

        </tr>


        {{-- TOTAL HARGA --}}
        <tr>

            <th>
                Total Pembelian
            </th>

            <td>

                <strong>
                    Rp
                    {{ number_format(
                        $barangMasuk->harga_beli * $barangMasuk->jumlah,
                        0,
                        ',',
                        '.'
                    ) }}
                </strong>

            </td>

        </tr>


        <tr>

            <th>
                Expired Date
            </th>

            <td>

                {{ $barangMasuk->expired_date
                    ? $barangMasuk->expired_date->format('d-m-Y')
                    : '-'
                }}

            </td>

        </tr>


        <tr>

            <th>
                Nomor Faktur
            </th>

            <td>

                {{ $barangMasuk->nomor_faktur ?: '-' }}

            </td>

        </tr>


        <tr>

            <th>
                Petugas
            </th>

            <td>

                {{ $barangMasuk->user->name }}

            </td>

        </tr>


        <tr>

            <th>
                Keterangan
            </th>

            <td>

                {{ $barangMasuk->keterangan ?: '-' }}

            </td>

        </tr>

    </table>

</div>

</div>

</x-shared.card>

@stop
