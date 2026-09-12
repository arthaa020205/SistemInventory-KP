@extends('adminlte::page')

@section('title', 'Dashboard Inventory')

@section('content_header')
@stop

@section('content')

<style>

    body {
        background: #f4f6f9;
    }

    /* WELCOME */

    .dashboard-welcome {
        border-radius: 15px;
        padding: 25px 30px;
        margin-bottom: 20px;
        color: #fff;
        background: linear-gradient(
            135deg,
            #667eea 0%,
            #764ba2 100%
        );
        box-shadow: 0 8px 25px rgba(0,0,0,.10);
    }

    .dashboard-welcome h2 {
        margin: 0;
        font-weight: 700;
        font-size: 25px;
    }

    .dashboard-welcome p {
        margin: 7px 0 0;
        opacity: .9;
    }

    .dashboard-welcome .welcome-icon {
        font-size: 65px;
        opacity: .18;
    }


    /* KPI */

    .dashboard-box {
        border-radius: 14px;
        border: none;
        overflow: hidden;
        position: relative;
        min-height: 125px;
        box-shadow: 0 5px 18px rgba(0,0,0,.07);
        transition: .25s;
    }

    .dashboard-box:hover {
        transform: translateY(-3px);
        box-shadow: 0 9px 25px rgba(0,0,0,.12);
    }

    .dashboard-box .inner {
        padding: 20px;
        position: relative;
        z-index: 2;
    }

    .dashboard-box h3 {
        font-size: 27px;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .dashboard-box p {
        font-size: 14px;
        margin: 0;
    }

    .dashboard-box .icon {
        position: absolute;
        right: 15px;
        top: 15px;
        font-size: 55px;
        opacity: .15;
        z-index: 1;
    }


    /* SECTION */

    .dashboard-section-title {
        font-size: 18px;
        font-weight: 700;
        margin: 10px 0 15px;
        color: #343a40;
    }


    /* CARD */

    .dashboard-card {
        border: none;
        border-radius: 14px;
        box-shadow: 0 5px 18px rgba(0,0,0,.06);
        overflow: hidden;
    }

    .dashboard-card .card-header {
        background: #fff;
        border-bottom: 1px solid #eee;
        padding: 15px 18px;
    }

    .dashboard-card .card-title {
        font-weight: 700;
        font-size: 15px;
        margin: 0;
    }

    .dashboard-card .card-body {
        padding: 18px;
    }


    /* TABLE */

    .dashboard-table {
        margin-bottom: 0;
    }

    .dashboard-table th {
        font-size: 12px;
        text-transform: uppercase;
        color: #6c757d;
        border-top: none;
    }

    .dashboard-table td {
        vertical-align: middle;
        font-size: 13px;
    }


    /* QUICK ACTION */

    .quick-action {
        display: block;
        text-decoration: none !important;
        color: #343a40;
        border-radius: 12px;
        padding: 18px 12px;
        text-align: center;
        background: #fff;
        border: 1px solid #eee;
        transition: .25s;
        height: 100%;
    }

    .quick-action:hover {
        transform: translateY(-3px);
        box-shadow: 0 7px 20px rgba(0,0,0,.09);
        color: #343a40;
    }

    .quick-action i {
        font-size: 27px;
        margin-bottom: 10px;
    }

    .quick-action span {
        display: block;
        font-size: 13px;
        font-weight: 600;
    }


    /* STATUS */

    .stock-badge {
        border-radius: 20px;
        padding: 5px 10px;
        font-size: 11px;
        font-weight: 600;
    }

    .badge-stock-danger {
        background: #ffe3e6;
        color: #dc3545;
    }

    .badge-stock-warning {
        background: #fff3cd;
        color: #856404;
    }

    .badge-stock-success {
        background: #dff7e8;
        color: #198754;
    }

    .badge-stock-info {
        background: #dff3ff;
        color: #007bff;
    }


    /* CHART */

    .chart-container {
        position: relative;
        height: 330px;
    }


    /* RESPONSIVE */

    @media(max-width:768px) {

        .dashboard-welcome {
            padding: 20px;
        }

        .dashboard-welcome h2 {
            font-size: 21px;
        }

        .dashboard-welcome .welcome-icon {
            display: none;
        }

        .chart-container {
            height: 280px;
        }

    }

</style>

<div class="container-fluid">

{{-- =====================================================
     WELCOME
====================================================== --}}

<br>

<div class="dashboard-welcome">

    <div class="row align-items-center">

        <div class="col-md-9">

            <h2>

                Selamat Datang,
                {{ auth()->user()->name }} 👋

            </h2>

            <p>

                Selamat datang di
                <strong>Inventera</strong>.
                Kelola dan pantau persediaan barang
                dengan lebih mudah.

            </p>

        </div>

        <div class="col-md-3 text-right">

            <i class="fas fa-warehouse welcome-icon"></i>

        </div>

    </div>

</div>


{{-- =====================================================
     RINGKASAN INVENTORY
====================================================== --}}

<div class="dashboard-section-title">

    <i class="fas fa-chart-pie mr-1"></i>
    Ringkasan Inventory

</div>


<div class="row">


    {{-- TOTAL BARANG --}}

    @can('barang.view')

    <div class="col-lg-3 col-md-6">

        <div class="small-box bg-info dashboard-box">

            <div class="inner">

                <h3>
                    {{ number_format($totalBarang, 0, ',', '.') }}
                </h3>

                <p>
                    Total Barang
                </p>

            </div>

            <div class="icon">

                <i class="fas fa-boxes"></i>

            </div>

        </div>

    </div>

    @endcan


    {{-- TOTAL STOK --}}

    @can('barang.view')

    <div class="col-lg-3 col-md-6">

        <div class="small-box bg-primary dashboard-box">

            <div class="inner">

                <h3>
                    {{ number_format($totalStok, 2, ',', '.') }}
                </h3>

                <p>
                    Total Stok
                </p>

            </div>

            <div class="icon">

                <i class="fas fa-cubes"></i>

            </div>

        </div>

    </div>

    @endcan


    {{-- STOK HABIS --}}

    @can('barang.view')

    <div class="col-lg-3 col-md-6">

        <div class="small-box bg-danger dashboard-box">

            <div class="inner">

                <h3>
                    {{ number_format($stokHabis, 0, ',', '.') }}
                </h3>

                <p>
                    Stok Habis
                </p>

            </div>

            <div class="icon">

                <i class="fas fa-times-circle"></i>

            </div>

        </div>

    </div>

    @endcan


    {{-- STOK MENIPIS --}}

    @can('barang.view')

    <div class="col-lg-3 col-md-6">

        <div class="small-box bg-warning dashboard-box">

            <div class="inner">

                <h3>
                    {{ number_format($stokMenipis, 0, ',', '.') }}
                </h3>

                <p>
                    Stok Menipis
                </p>

            </div>

            <div class="icon">

                <i class="fas fa-exclamation-triangle"></i>

            </div>

        </div>

    </div>

    @endcan


</div>


{{-- =====================================================
     AKTIVITAS INVENTORY
====================================================== --}}

<div class="dashboard-section-title mt-2">

    <i class="fas fa-exchange-alt mr-1"></i>
    Aktivitas Inventory

</div>


<div class="row">


    {{-- BARANG MASUK --}}

    @can('barang-masuk.view')

    <div class="col-lg-3 col-md-6">

        <div class="small-box bg-success dashboard-box">

            <div class="inner">

                <h3>
                    {{ number_format($barangMasuk, 2, ',', '.') }}
                </h3>

                <p>
                    Total Barang Masuk
                </p>

            </div>

            <div class="icon">

                <i class="fas fa-arrow-down"></i>

            </div>

        </div>

    </div>

    @endcan


    {{-- BARANG KELUAR --}}

    @can('barang-keluar.view')

    <div class="col-lg-3 col-md-6">

        <div class="small-box bg-danger dashboard-box">

            <div class="inner">

                <h3>
                    {{ number_format($barangKeluar, 2, ',', '.') }}
                </h3>

                <p>
                    Total Barang Keluar
                </p>

            </div>

            <div class="icon">

                <i class="fas fa-arrow-up"></i>

            </div>

        </div>

    </div>

    @endcan


    {{-- STOCK OPNAME --}}

    @can('stock-opname.view')

    <div class="col-lg-3 col-md-6">

        <div class="small-box bg-secondary dashboard-box">

            <div class="inner">

                <h3>
                    {{ number_format($totalStockOpname, 0, ',', '.') }}
                </h3>

                <p>
                    Total Stock Opname
                </p>

            </div>

            <div class="icon">

                <i class="fas fa-clipboard-check"></i>

            </div>

        </div>

    </div>

    @endcan


    {{-- PERMINTAAN --}}

    @can('permintaan.view')

    <div class="col-lg-3 col-md-6">

        <div class="small-box bg-warning dashboard-box">

            <div class="inner">

                <h3>
                    {{ number_format($permintaanPending, 0, ',', '.') }}
                </h3>

                <p>
                    Permintaan Menunggu
                </p>

            </div>

            <div class="icon">

                <i class="fas fa-clock"></i>

            </div>

        </div>

    </div>

    @endcan


</div>


{{-- =====================================================
     QUICK ACTION
====================================================== --}}

<div class="dashboard-section-title mt-2">

    <i class="fas fa-bolt mr-1"></i>
    Akses Cepat

</div>


<div class="row mb-3">


    {{-- BARANG MASUK --}}

    @can('barang-masuk.create')

    <div class="col-lg-3 col-md-6 mb-3">

        <a
            href="{{ route('barang-masuk.create') }}"
            class="quick-action">

            <i class="fas fa-arrow-circle-down text-success"></i>

            <span>
                Input Barang Masuk
            </span>

        </a>

    </div>

    @endcan


    {{-- BARANG KELUAR --}}

    @can('barang-keluar.create')

    <div class="col-lg-3 col-md-6 mb-3">

        <a
            href="{{ route('barang-keluar.create') }}"
            class="quick-action">

            <i class="fas fa-arrow-circle-up text-danger"></i>

            <span>
                Input Barang Keluar
            </span>

        </a>

    </div>

    @endcan


    {{-- STOCK OPNAME --}}

    @can('stock-opname.create')

    <div class="col-lg-3 col-md-6 mb-3">

        <a
            href="{{ route('stock-opname.create') }}"
            class="quick-action">

            <i class="fas fa-clipboard-check text-primary"></i>

            <span>
                Stock Opname
            </span>

        </a>

    </div>

    @endcan


    {{-- MONITORING --}}

    @can('monitoring.view')

    <div class="col-lg-3 col-md-6 mb-3">

        <a
            href="{{ route('monitoring.index') }}"
            class="quick-action">

            <i class="fas fa-chart-line text-warning"></i>

            <span>
                Monitoring Stok
            </span>

        </a>

    </div>

    @endcan


</div>


{{-- =====================================================
     MONITORING STOK
====================================================== --}}

<div class="dashboard-section-title">

    <i class="fas fa-desktop mr-1"></i>
    Monitoring Stok

</div>


<div class="row">


    {{-- BARANG HAMPIR HABIS --}}

    @can('barang.view')

    <div class="col-lg-6 mb-4">

        <div class="card dashboard-card">

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-exclamation-triangle text-danger mr-2"></i>

                    Barang Hampir Habis

                </h3>

            </div>


            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table dashboard-table">

                        <thead>

                            <tr>

                                <th>
                                    Barang
                                </th>

                                <th class="text-center">
                                    Stok
                                </th>

                                <th class="text-center">
                                    Minimum
                                </th>

                                <th class="text-center">
                                    Status
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                        @forelse($barangHampirHabis as $item)

                            <tr>

                                <td>

                                    <strong>
                                        {{ $item->nama_barang }}
                                    </strong>

                                    <br>

                                    <small class="text-muted">
                                        {{ $item->kode_barang }}
                                    </small>

                                </td>

                                <td class="text-center">

                                    <span class="stock-badge badge-stock-danger">

                                        {{ number_format($item->stok, 2, ',', '.') }}

                                    </span>

                                </td>

                                <td class="text-center">

                                    {{ number_format($item->stok_minimum, 2, ',', '.') }}

                                </td>

                                <td class="text-center">

                                    @if($item->stok <= 0)

                                        <span class="stock-badge badge-stock-danger">
                                            Habis
                                        </span>

                                    @else

                                        <span class="stock-badge badge-stock-warning">
                                            Menipis
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="4"
                                    class="text-center text-muted py-4">

                                    <i class="fas fa-check-circle text-success mr-1"></i>

                                    Tidak ada barang yang menipis.

                                </td>

                            </tr>

                        @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

    @endcan


    {{-- BARANG MENDEKATI ED --}}

    @can('barang.view')

    <div class="col-lg-6 mb-4">

        <div class="card dashboard-card">

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-calendar-times text-warning mr-2"></i>

                    Barang Mendekati ED

                </h3>

            </div>


            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table dashboard-table">

                        <thead>

                            <tr>

                                <th>
                                    Barang
                                </th>

                                <th class="text-center">
                                    ED
                                </th>

                                <th class="text-center">
                                    Sisa
                                </th>

                                <th class="text-center">
                                    Status
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                        @forelse($barangMendekatiEd as $item)

                            @php

                                $tanggalEd = \Carbon\Carbon::parse(
                                    $item->expired_date
                                );

                                $sisaHari = now()
                                    ->startOfDay()
                                    ->diffInDays(
                                        $tanggalEd,
                                        false
                                    );

                            @endphp


                            <tr>

                                <td>

                                    <strong>
                                        {{ $item->barang->nama_barang ?? '-' }}
                                    </strong>

                                    <br>

                                    <small class="text-muted">

                                        {{ $item->barang->kode_barang ?? '-' }}

                                    </small>

                                </td>


                                <td class="text-center">

                                    {{ $tanggalEd->format('d-m-Y') }}

                                </td>


                                <td class="text-center">

                                    <strong>
                                        {{ $sisaHari }}
                                    </strong>

                                    hari

                                </td>


                                <td class="text-center">

                                    @if($sisaHari <= 7)

                                        <span class="stock-badge badge-stock-danger">
                                            Sangat Dekat
                                        </span>

                                    @elseif($sisaHari <= 14)

                                        <span class="stock-badge badge-stock-warning">
                                            Dekat
                                        </span>

                                    @else

                                        <span class="stock-badge badge-stock-info">
                                            Perlu Dipantau
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="4"
                                    class="text-center text-muted py-4">

                                    <i class="fas fa-check-circle text-success mr-1"></i>

                                    Tidak ada barang yang mendekati ED.

                                </td>

                            </tr>

                        @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

    @endcan


</div>


{{-- =====================================================
     STOK TERBANYAK
====================================================== --}}

@can('barang.view')

<div class="card dashboard-card mb-4">

    <div class="card-header">

        <h3 class="card-title">

            <i class="fas fa-layer-group text-primary mr-2"></i>

            Barang dengan Stok Terbanyak

        </h3>

    </div>


    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table dashboard-table">

                <thead>

                    <tr>

                        <th>
                            Barang
                        </th>

                        <th class="text-center">
                            Stok
                        </th>

                        <th class="text-center">
                            Satuan
                        </th>

                    </tr>

                </thead>


                <tbody>

                @forelse($barangStokTerbanyak as $item)

                    <tr>

                        <td>

                            <strong>
                                {{ $item->nama_barang }}
                            </strong>

                            <br>

                            <small class="text-muted">
                                {{ $item->kode_barang }}
                            </small>

                        </td>


                        <td class="text-center">

                            <span class="stock-badge badge-stock-success">

                                {{ number_format($item->stok, 2, ',', '.') }}

                            </span>

                        </td>


                        <td class="text-center">

                            {{ $item->satuan->nama_satuan ?? '-' }}

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="3"
                            class="text-center text-muted py-4">

                            <i class="fas fa-info-circle mr-1"></i>

                            Belum ada data barang.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endcan


{{-- =====================================================
     GRAFIK INVENTORY
====================================================== --}}

@can('monitoring.view')

<div class="row">

    <div class="col-12 mb-4">

        <div class="card dashboard-card">

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-chart-bar text-primary mr-2"></i>

                    Grafik Barang Masuk dan Barang Keluar

                </h3>

            </div>


            <div class="card-body">

                <div class="chart-container">

                    <canvas id="stokChart"></canvas>

                </div>

            </div>

        </div>

    </div>

</div>

@endcan


{{-- =====================================================
     AKTIVITAS TERBARU
====================================================== --}}

<div class="dashboard-section-title">

    <i class="fas fa-history mr-1"></i>
    Aktivitas Terbaru

</div>


<div class="row">


    {{-- BARANG MASUK --}}

    @can('barang-masuk.view')

    <div class="col-lg-4 mb-4">

        <div class="card dashboard-card">

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-arrow-down text-success mr-2"></i>

                    Barang Masuk Terbaru

                </h3>

            </div>


            <div class="card-body p-0">

                <ul class="list-group list-group-flush">

                @forelse($barangMasukTerbaru as $item)

                    <li class="list-group-item">

                        <strong>
                            {{ $item->barang->nama_barang ?? '-' }}
                        </strong>

                        <br>

                        <small class="text-muted">

                            {{ $item->kode_transaksi }}

                            ·

                            {{ \Carbon\Carbon::parse($item->tanggal_masuk)->format('d-m-Y') }}

                        </small>

                    </li>

                @empty

                    <li class="list-group-item text-center text-muted">

                        Belum ada aktivitas.

                    </li>

                @endforelse

                </ul>

            </div>

        </div>

    </div>

    @endcan


    {{-- BARANG KELUAR --}}

    @can('barang-keluar.view')

    <div class="col-lg-4 mb-4">

        <div class="card dashboard-card">

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-arrow-up text-danger mr-2"></i>

                    Barang Keluar Terbaru

                </h3>

            </div>


            <div class="card-body p-0">

                <ul class="list-group list-group-flush">

                @forelse($barangKeluarTerbaru as $item)

                    <li class="list-group-item">

                        <strong>
                            {{ $item->barang->nama_barang ?? '-' }}
                        </strong>

                        <br>

                        <small class="text-muted">

                            {{ $item->kode_transaksi }}

                            ·

                            {{ \Carbon\Carbon::parse($item->tanggal_keluar)->format('d-m-Y') }}

                        </small>

                    </li>

                @empty

                    <li class="list-group-item text-center text-muted">

                        Belum ada aktivitas.

                    </li>

                @endforelse

                </ul>

            </div>

        </div>

    </div>

    @endcan


    {{-- STOCK OPNAME --}}

    @can('stock-opname.view')

    <div class="col-lg-4 mb-4">

        <div class="card dashboard-card">

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-clipboard-check text-primary mr-2"></i>

                    Stock Opname Terbaru

                </h3>

            </div>


            <div class="card-body p-0">

                <ul class="list-group list-group-flush">

                @forelse($stockOpnameTerbaru as $item)

                    <li class="list-group-item">

                        <strong>
                            {{ $item->barang->nama_barang ?? '-' }}
                        </strong>

                        <br>

                        <small class="text-muted">

                            {{ $item->kode_transaksi }}

                            ·

                            {{ \Carbon\Carbon::parse($item->tanggal_opname)->format('d-m-Y') }}

                        </small>


                        <span class="float-right">

                            @if($item->status === 'Sesuai')

                                <span class="stock-badge badge-stock-success">
                                    Sesuai
                                </span>

                            @else

                                <span class="stock-badge badge-stock-danger">
                                    Selisih
                                </span>

                            @endif

                        </span>

                    </li>

                @empty

                    <li class="list-group-item text-center text-muted">

                        Belum ada aktivitas.

                    </li>

                @endforelse

                </ul>

            </div>

        </div>

    </div>

    @endcan


</div>


{{-- =====================================================
     PERMINTAAN TERBARU
====================================================== --}}

@can('permintaan.view')

<div class="card dashboard-card mb-4">

    <div class="card-header">

        <h3 class="card-title">

            <i class="fas fa-clipboard-list text-primary mr-2"></i>

            Permintaan Barang Terbaru

        </h3>

    </div>


    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table dashboard-table">

                <thead>

                    <tr>

                        <th>
                            Kode
                        </th>

                        <th>
                            Barang
                        </th>

                        <th class="text-center">
                            Jumlah
                        </th>

                        <th class="text-center">
                            Tanggal
                        </th>

                        <th class="text-center">
                            Status
                        </th>

                    </tr>

                </thead>


                <tbody>

                @forelse($permintaanTerbaru as $item)

                    <tr>

                        <td>
                            {{ $item->kode_permintaan }}
                        </td>

                        <td>
                            {{ $item->barang->nama_barang ?? '-' }}
                        </td>

                        <td class="text-center">

                            {{ number_format($item->jumlah, 2, ',', '.') }}

                        </td>

                        <td class="text-center">

                            {{ \Carbon\Carbon::parse($item->tanggal_permintaan)->format('d-m-Y') }}

                        </td>

                        <td class="text-center">


                            @if($item->status === 'Menunggu')

                                <span class="stock-badge badge-stock-warning">
                                    Menunggu
                                </span>

                            @elseif($item->status === 'Disetujui')

                                <span class="stock-badge badge-stock-success">
                                    Disetujui
                                </span>

                            @elseif($item->status === 'Ditolak')

                                <span class="stock-badge badge-stock-danger">
                                    Ditolak
                                </span>

                            @else

                                <span class="stock-badge badge-stock-info">
                                    {{ $item->status }}
                                </span>

                            @endif


                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="5"
                            class="text-center text-muted py-4">

                            <i class="fas fa-info-circle mr-1"></i>

                            Belum ada permintaan barang.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endcan

</div>

@endsection

@section('js')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const bulan = [

            'Jan',
            'Feb',
            'Mar',
            'Apr',
            'Mei',
            'Jun',
            'Jul',
            'Ags',
            'Sep',
            'Okt',
            'Nov',
            'Des'

        ];


        /* =====================================================
           GRAFIK BARANG MASUK / KELUAR
        ====================================================== */

        const stokCanvas =
            document.getElementById('stokChart');


        if (stokCanvas) {

            new Chart(
                stokCanvas,
                {

                    type: 'bar',

                    data: {

                        labels: bulan,

                        datasets: [

                            {

                                label:
                                    'Barang Masuk',

                                data:
                                    @json($grafikMasuk),

                                backgroundColor:
                                    '#17a2b8',

                                borderRadius: 5

                            },


                            {

                                label:
                                    'Barang Keluar',

                                data:
                                    @json($grafikKeluar),

                                backgroundColor:
                                    '#dc3545',

                                borderRadius: 5

                            }

                        ]

                    },


                    options: {

                        responsive: true,

                        maintainAspectRatio: false,

                        plugins: {

                            legend: {

                                position: 'top'

                            }

                        },

                        scales: {

                            y: {

                                beginAtZero: true,

                                title: {

                                    display: true,

                                    text: 'Jumlah Barang'

                                }

                            }

                        }

                    }

                }
            );

        }

    }

);

</script>

@stop
