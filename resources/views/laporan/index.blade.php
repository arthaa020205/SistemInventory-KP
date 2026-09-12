@extends('adminlte::page')

@section('title','Menu Laporan')

@section('content')

<x-shared.page-header>

<x-slot:title>
    Menu Laporan
</x-slot:title>

</x-shared.page-header>

<div class="row">

{{-- LAPORAN STOK --}}
<div class="col-md-3 mb-4">

    <div class="card h-100">

        <div class="card-body text-center">

            <i class="fas fa-boxes fa-3x text-primary mb-3"></i>

            <h4>Laporan Stok</h4>

            <p>
                Lihat kondisi dan jumlah stok barang.
            </p>

            <a
                href="{{ route('laporan.stok') }}"
                class="btn btn-primary">

                <i class="fas fa-boxes"></i>
                Buka Laporan

            </a>

        </div>

    </div>

</div>


{{-- LAPORAN BARANG MASUK --}}
<div class="col-md-3 mb-4">

    <div class="card h-100">

        <div class="card-body text-center">

            <i class="fas fa-arrow-down fa-3x text-success mb-3"></i>

            <h4>Laporan Barang Masuk</h4>

            <p>
                Lihat dan export laporan barang masuk.
            </p>

            <a
                href="{{ route('laporan.barang-masuk') }}"
                class="btn btn-success">

                <i class="fas fa-arrow-down"></i>
                Buka Laporan

            </a>

        </div>

    </div>

</div>


{{-- LAPORAN BARANG KELUAR --}}
<div class="col-md-3 mb-4">

    <div class="card h-100">

        <div class="card-body text-center">

            <i class="fas fa-arrow-up fa-3x text-danger mb-3"></i>

            <h4>Laporan Barang Keluar</h4>

            <p>
                Lihat dan export laporan barang keluar.
            </p>

            <a
                href="{{ route('laporan.barang-keluar') }}"
                class="btn btn-danger">

                <i class="fas fa-arrow-up"></i>
                Buka Laporan

            </a>

        </div>

    </div>

</div>


{{-- LAPORAN STOCK OPNAME --}}
<div class="col-md-3 mb-4">

    <div class="card h-100">

        <div class="card-body text-center">

            <i class="fas fa-clipboard-check fa-3x text-info mb-3"></i>

            <h4>Laporan Stock Opname</h4>

            <p>
                Lihat hasil stock opname.
            </p>

            <a
                href="{{ route('laporan.stock-opname') }}"
                class="btn btn-info">

                <i class="fas fa-clipboard-check"></i>
                Buka Laporan

            </a>

        </div>

    </div>

</div>

</div>

@stop
