@extends('adminlte::page')

@section('title','Detail Stock Opname')

@section('content')

<x-shared.page-header>

    <x-slot:title>

        Detail Stock Opname

    </x-slot:title>

</x-shared.page-header>

<x-shared.card>

<div class="row">

    <div class="col-md-6">

        <table class="table table-bordered">

            <tr>

                <th width="180">

                    Kode Transaksi

                </th>

                <td>

                    {{ $stockOpname->kode_transaksi }}

                </td>

            </tr>

            <tr>

                <th>

                    Tanggal

                </th>

                <td>

                    {{ $stockOpname->tanggal_opname->format('d-m-Y') }}

                </td>

            </tr>

            <tr>

                <th>

                    Barang

                </th>

                <td>

                    {{ $stockOpname->barang->nama_barang }}

                </td>

            </tr>

            <tr>

                <th>

                    Stok Sistem

                </th>

                <td>

                    {{ $stockOpname->stok_sistem }}

                </td>

            </tr>

            <tr>

                <th>

                    Stok Fisik

                </th>

                <td>

                    {{ $stockOpname->stok_fisik }}

                </td>

            </tr>

            <tr>

                <th>

                    Selisih

                </th>

                <td>

                    @if($stockOpname->selisih > 0)

                        <span class="text-success">

                            +{{ $stockOpname->selisih }}

                        </span>

                    @elseif($stockOpname->selisih < 0)

                        <span class="text-danger">

                            {{ $stockOpname->selisih }}

                        </span>

                    @else

                        0

                    @endif

                </td>

            </tr>

            <tr>

                <th>

                    Status

                </th>

                <td>

                    @if($stockOpname->status == 'Sesuai')

                        <span class="badge bg-success">

                            Sesuai

                        </span>

                    @else

                        <span class="badge bg-warning">

                            Selisih

                        </span>

                    @endif

                </td>

            </tr>

            <tr>

                <th>

                    Petugas

                </th>

                <td>

                    {{ $stockOpname->user->name }}

                </td>

            </tr>

            <tr>

                <th>

                    Keterangan

                </th>

                <td>

                    {{ $stockOpname->keterangan ?: '-' }}

                </td>

            </tr>

        </table>

    </div>

</div>

@if($stockOpname->adjustment)

<hr>

<h5>

    Histori Stock Adjustment

</h5>

<table class="table table-bordered">

    <tr>

        <th width="220">

            Kode Adjustment

        </th>

        <td>

            {{ $stockOpname->adjustment->kode_transaksi }}

        </td>

    </tr>

    <tr>

        <th>

            Stok Sebelum

        </th>

        <td>

            {{ $stockOpname->adjustment->stok_sebelum }}

        </td>

    </tr>

    <tr>

        <th>

            Stok Sesudah

        </th>

        <td>

            {{ $stockOpname->adjustment->stok_sesudah }}

        </td>

    </tr>

    <tr>

        <th>

            Selisih

        </th>

        <td>

            {{ $stockOpname->adjustment->selisih }}

        </td>

    </tr>

    <tr>

        <th>

            Keterangan

        </th>

        <td>

            {{ $stockOpname->adjustment->keterangan }}

        </td>

    </tr>

</table>

@endif

<div class="mt-3">

    <a
        href="{{ route('stock-opname.index') }}"
        class="btn btn-secondary">

        <i class="fas fa-arrow-left"></i>

        Kembali

    </a>

</div>

</x-shared.card>

@stop