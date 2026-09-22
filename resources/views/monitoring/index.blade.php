@extends('adminlte::page')

@section('title', 'Monitoring Inventory')

@section('content')

<div class="container-fluid">

{{-- =========================================================
    HEADER
========================================================== --}}
<div class="card border-0 shadow-sm mb-4">

    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center flex-wrap">

            <div>

                <div class="d-flex align-items-center">

                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                         style="width:50px;height:50px;">

                        <i class="fas fa-chart-line"></i>

                    </div>

                    <div>

                        <h3 class="font-weight-bold mb-1">
                            Monitoring Inventory
                        </h3>

                        <p class="text-muted mb-0">
                            Pemantauan kondisi dan aktivitas inventory
                            CV Cahaya Khanza Plastik
                        </p>

                    </div>

                </div>

            </div>


            {{-- FILTER PERIODE --}}
            <div class="mt-3 mt-md-0">

                <label class="small text-muted mb-1">
                    Periode Monitoring
                </label>

                <select
                    id="periode"
                    class="form-control"
                    onchange="ubahPeriode()">

                    <option value="hari"
                        {{ $periode == 'hari' ? 'selected' : '' }}>
                        Hari Ini
                    </option>

                    <option value="minggu"
                        {{ $periode == 'minggu' ? 'selected' : '' }}>
                        7 Hari Terakhir
                    </option>

                    <option value="bulan"
                        {{ $periode == 'bulan' ? 'selected' : '' }}>
                        Bulan Ini
                    </option>

                    <option value="tahun"
                        {{ $periode == 'tahun' ? 'selected' : '' }}>
                        Tahun Ini
                    </option>

                </select>

            </div>

        </div>

    </div>

</div>



{{-- =========================================================
    KPI UTAMA
========================================================== --}}

<div class="row">

    {{-- TOTAL BARANG --}}
    <div class="col-xl-3 col-md-6 mb-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body">

                <div class="d-flex justify-content-between">

                    <div>

                        <small class="text-muted">
                            Total Barang
                        </small>

                        <h2 class="font-weight-bold mt-2 mb-1">
                            {{ number_format($totalBarang) }}
                        </h2>

                        <small class="text-muted">
                            Barang terdaftar
                        </small>

                    </div>

                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                         style="width:55px;height:55px;">

                        <i class="fas fa-box"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- STOK AMAN --}}
    <div class="col-xl-3 col-md-6 mb-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body">

                <div class="d-flex justify-content-between">

                    <div>

                        <small class="text-muted">
                            Stok Aman
                        </small>

                        <h2 class="font-weight-bold text-success mt-2 mb-1">
                            {{ number_format($stokAman) }}
                        </h2>

                        <small class="text-success">
                            <i class="fas fa-check-circle mr-1"></i>
                            Di atas minimum
                        </small>

                    </div>

                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center"
                         style="width:55px;height:55px;">

                        <i class="fas fa-check"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- STOK MENIPIS --}}
    <div class="col-xl-3 col-md-6 mb-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body">

                <div class="d-flex justify-content-between">

                    <div>

                        <small class="text-muted">
                            Stok Menipis
                        </small>

                        <h2 class="font-weight-bold text-warning mt-2 mb-1">
                            {{ number_format($stokMenipis) }}
                        </h2>

                        <small class="text-warning">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Perlu dipantau
                        </small>

                    </div>

                    <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center"
                         style="width:55px;height:55px;">

                        <i class="fas fa-exclamation"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- STOK HABIS --}}
    <div class="col-xl-3 col-md-6 mb-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body">

                <div class="d-flex justify-content-between">

                    <div>

                        <small class="text-muted">
                            Stok Habis
                        </small>

                        <h2 class="font-weight-bold text-danger mt-2 mb-1">
                            {{ number_format($stokHabis) }}
                        </h2>

                        <small class="text-danger">
                            <i class="fas fa-times-circle mr-1"></i>
                            Segera ditindaklanjuti
                        </small>

                    </div>

                    <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center"
                         style="width:55px;height:55px;">

                        <i class="fas fa-times"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>



{{-- =========================================================
    STATUS INVENTORY
========================================================== --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white border-0">

        <div class="d-flex justify-content-between align-items-center">

            <div>

                <h5 class="font-weight-bold mb-1">
                    <i class="fas fa-heartbeat text-danger mr-2"></i>
                    Status Kesehatan Inventory
                </h5>

                <small class="text-muted">
                    Ringkasan kondisi stok saat ini.
                </small>

            </div>

            <span class="badge badge-{{ $statusInventoryClass }} px-3 py-2">

                @if($statusInventoryClass == 'success')

                    <i class="fas fa-check-circle mr-1"></i>

                @elseif($statusInventoryClass == 'warning')

                    <i class="fas fa-exclamation-triangle mr-1"></i>

                @else

                    <i class="fas fa-times-circle mr-1"></i>

                @endif

                {{ $statusInventory }}

            </span>

        </div>

    </div>


    <div class="card-body">

        <div class="row">

            {{-- AMAN --}}
            <div class="col-md-3 mb-3 mb-md-0">

                <div class="border rounded p-3">

                    <div class="d-flex justify-content-between">

                        <span class="text-muted">
                            Stok Aman
                        </span>

                        <i class="fas fa-check-circle text-success"></i>

                    </div>

                    <h4 class="font-weight-bold mt-2 mb-0">
                        {{ number_format($stokAman) }}
                    </h4>

                    <small class="text-muted">
                        Barang
                    </small>

                </div>

            </div>


            {{-- MENIPIS --}}
            <div class="col-md-3 mb-3 mb-md-0">

                <div class="border rounded p-3">

                    <div class="d-flex justify-content-between">

                        <span class="text-muted">
                            Stok Menipis
                        </span>

                        <i class="fas fa-exclamation-triangle text-warning"></i>

                    </div>

                    <h4 class="font-weight-bold text-warning mt-2 mb-0">
                        {{ number_format($stokMenipis) }}
                    </h4>

                    <small class="text-muted">
                        Barang
                    </small>

                </div>

            </div>


            {{-- HABIS --}}
            <div class="col-md-3 mb-3 mb-md-0">

                <div class="border rounded p-3">

                    <div class="d-flex justify-content-between">

                        <span class="text-muted">
                            Stok Habis
                        </span>

                        <i class="fas fa-times-circle text-danger"></i>

                    </div>

                    <h4 class="font-weight-bold text-danger mt-2 mb-0">
                        {{ number_format($stokHabis) }}
                    </h4>

                    <small class="text-muted">
                        Barang
                    </small>

                </div>

            </div>


            {{-- BERLEBIH --}}
            <div class="col-md-3">

                <div class="border rounded p-3">

                    <div class="d-flex justify-content-between">

                        <span class="text-muted">
                            Stok Berlebih
                        </span>

                        <i class="fas fa-boxes text-info"></i>

                    </div>

                    <h4 class="font-weight-bold text-info mt-2 mb-0">
                        {{ number_format($stokBerlebih) }}
                    </h4>

                    <small class="text-muted">
                        Barang
                    </small>

                </div>

            </div>

        </div>

    </div>

</div>



{{-- =========================================================
    FEEDBACK & REKOMENDASI
========================================================== --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white border-0">

        <h5 class="font-weight-bold mb-1">

            <i class="fas fa-lightbulb text-warning mr-2"></i>

            Feedback & Rekomendasi Sistem

        </h5>

        <small class="text-muted">
            Informasi dan rekomendasi berdasarkan kondisi inventory.
        </small>

    </div>


    <div class="card-body">

        @forelse($feedback as $item)

            <div class="alert alert-{{ $item['type'] }} d-flex align-items-start mb-3">

                <div class="mr-3">

                    <i class="fas {{ $item['icon'] }} fa-lg"></i>

                </div>

                <div>

                    <strong>
                        {{ $item['title'] }}
                    </strong>

                    <div class="small mt-1">
                        {{ $item['message'] }}
                    </div>

                </div>

            </div>

        @empty

            <div class="alert alert-success mb-0">

                <i class="fas fa-check-circle mr-2"></i>

                <strong>
                    Inventory dalam kondisi baik.
                </strong>

                <div class="small mt-1">

                    Tidak terdapat kondisi khusus yang membutuhkan
                    perhatian berdasarkan data monitoring saat ini.

                </div>

            </div>

        @endforelse

    </div>

</div>



{{-- =========================================================
    AKTIVITAS PERIODE
========================================================== --}}

<div class="row">

    {{-- BARANG MASUK --}}
    <div class="col-lg-4 mb-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body">

                <div class="d-flex justify-content-between">

                    <div>

                        <small class="text-muted">
                            Barang Masuk
                        </small>

                        <h2 class="font-weight-bold text-success mt-2 mb-1">

                            {{ number_format($barangMasukPeriode) }}

                        </h2>

                        <small class="text-muted">
                            Pada periode terpilih
                        </small>

                    </div>

                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center"
                         style="width:50px;height:50px;">

                        <i class="fas fa-arrow-down"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- BARANG KELUAR --}}
    <div class="col-lg-4 mb-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body">

                <div class="d-flex justify-content-between">

                    <div>

                        <small class="text-muted">
                            Barang Keluar
                        </small>

                        <h2 class="font-weight-bold text-danger mt-2 mb-1">

                            {{ number_format($barangKeluarPeriode) }}

                        </h2>

                        <small class="text-muted">
                            Non-penjualan + penjualan
                        </small>

                    </div>

                    <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center"
                         style="width:50px;height:50px;">

                        <i class="fas fa-arrow-up"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- STOCK OPNAME --}}
    <div class="col-lg-4 mb-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body">

                <div class="d-flex justify-content-between">

                    <div>

                        <small class="text-muted">
                            Stock Opname
                        </small>

                        <h2 class="font-weight-bold text-primary mt-2 mb-1">

                            {{ number_format($stockOpnamePeriode) }}

                        </h2>

                        <small class="text-muted">
                            Pada periode terpilih
                        </small>

                    </div>

                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                         style="width:50px;height:50px;">

                        <i class="fas fa-clipboard-check"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>



{{-- =========================================================
    GRAFIK
========================================================== --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white border-0">

        <div>

            <h5 class="font-weight-bold mb-1">

                <i class="fas fa-chart-area text-primary mr-2"></i>

                Grafik Aktivitas Inventory

            </h5>

            <small class="text-muted">
                Perbandingan jumlah barang masuk dan barang keluar.
            </small>

        </div>

    </div>


    <div class="card-body">

        <div style="height:350px;">

            <canvas id="inventoryChart"></canvas>

        </div>

    </div>

</div>



{{-- =========================================================
    ANALISIS PERGERAKAN
========================================================== --}}

<div class="d-flex align-items-center mb-3">

    <i class="fas fa-chart-bar text-primary mr-2"></i>

    <h5 class="font-weight-bold mb-0">
        Analisis Pergerakan Barang
    </h5>

</div>


<div class="row">

    {{-- =====================================================
        FAST MOVING
    ====================================================== --}}
    <div class="col-lg-4 mb-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-header bg-white border-0">

                <h5 class="font-weight-bold mb-1">

                    <i class="fas fa-fire text-danger mr-2"></i>

                    Fast Moving

                </h5>

                <small class="text-muted">

                    Barang dengan jumlah pengeluaran tertinggi
                    dari transaksi non-penjualan dan penjualan.

                </small>

            </div>


            <div class="card-body">

                @forelse($fastMoving as $item)

                    <div class="mb-3">

                        <div class="d-flex justify-content-between align-items-center">

                            <span class="font-weight-bold">

                                {{ $item->nama_barang ?? '-' }}

                            </span>


                            <span class="badge badge-danger">

                                {{ number_format((float) $item->total_keluar, 0, ',', '.') }}

                            </span>

                        </div>


                        <small class="text-muted">

                            Total barang keluar

                        </small>

                    </div>

                @empty

                    <div class="text-center py-4 text-muted">

                        <i class="fas fa-chart-line fa-2x mb-2"></i>

                        <div>
                            Belum ada data pergerakan.
                        </div>

                    </div>

                @endforelse

            </div>

        </div>

    </div>



    {{-- =====================================================
        SLOW MOVING
    ====================================================== --}}
    <div class="col-lg-4 mb-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-header bg-white border-0">

                <h5 class="font-weight-bold mb-1">

                    <i class="fas fa-hourglass-half text-warning mr-2"></i>

                    Slow Moving

                </h5>

                <small class="text-muted">

                    Barang dengan jumlah pengeluaran terendah
                    dari transaksi non-penjualan dan penjualan.

                </small>

            </div>


            <div class="card-body">

                @forelse($slowMoving as $item)

                    <div class="mb-3">

                        <div class="d-flex justify-content-between align-items-center">

                            <span class="font-weight-bold">

                                {{ $item->nama_barang ?? '-' }}

                            </span>


                            <span class="badge badge-warning">

                                {{ number_format((float) $item->total_keluar, 0, ',', '.') }}

                            </span>

                        </div>


                        <small class="text-muted">

                            Total barang keluar

                        </small>

                    </div>

                @empty

                    <div class="text-center py-4 text-muted">

                        <i class="fas fa-chart-line fa-2x mb-2"></i>

                        <div>
                            Belum ada data pergerakan.
                        </div>

                    </div>

                @endforelse

            </div>

        </div>

    </div>



    {{-- =====================================================
        STOK TERBANYAK
    ====================================================== --}}
    <div class="col-lg-4 mb-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-header bg-white border-0">

                <h5 class="font-weight-bold mb-1">

                    <i class="fas fa-boxes text-primary mr-2"></i>

                    Stok Terbanyak

                </h5>

                <small class="text-muted">

                    Barang dengan jumlah stok terbesar.

                </small>

            </div>


            <div class="card-body">

                @forelse($stokTerbanyak as $item)

                    <div class="mb-3">

                        <div class="d-flex justify-content-between align-items-center">

                            <span class="font-weight-bold">

                                {{ $item->nama_barang }}

                            </span>


                            <span class="badge badge-primary">

                                {{ number_format($item->stok, 0, ',', '.') }}

                            </span>

                        </div>


                        <small class="text-muted">

                            Stok tersedia

                        </small>

                    </div>

                @empty

                    <div class="text-center py-4 text-muted">

                        <i class="fas fa-box-open fa-2x mb-2"></i>

                        <div>
                            Belum ada data stok.
                        </div>

                    </div>

                @endforelse

            </div>

        </div>

    </div>

</div>



{{-- =========================================================
    KONDISI STOK DETAIL
========================================================== --}}

<div class="d-flex align-items-center mb-3">

    <i class="fas fa-warehouse text-primary mr-2"></i>

    <h5 class="font-weight-bold mb-0">

        Kondisi Stok yang Perlu Diperhatikan

    </h5>

</div>


<div class="row">

    {{-- STOK HABIS --}}
    <div class="col-lg-6 mb-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-header bg-white border-0">

                <h5 class="font-weight-bold mb-1">

                    <i class="fas fa-times-circle text-danger mr-2"></i>

                    Barang Stok Habis

                </h5>

                <small class="text-muted">

                    Barang yang saat ini tidak memiliki stok.

                </small>

            </div>


            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover mb-0">

                        <thead class="bg-light">

                            <tr>

                                <th>
                                    Barang
                                </th>

                                <th>
                                    Stok
                                </th>

                                <th>
                                    Status
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                        @forelse($barangHabis as $item)

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

                                <td>

                                    <strong class="text-danger">
                                        {{ $item->stok }}
                                    </strong>

                                </td>

                                <td>

                                    <span class="badge badge-danger">
                                        Habis
                                    </span>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="3"
                                    class="text-center py-4">

                                    <i class="fas fa-check-circle text-success fa-2x mb-2"></i>

                                    <div>
                                        Tidak ada barang stok habis.
                                    </div>

                                </td>

                            </tr>

                        @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>



    {{-- STOK BERLEBIH --}}
    <div class="col-lg-6 mb-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-header bg-white border-0">

                <h5 class="font-weight-bold mb-1">

                    <i class="fas fa-boxes text-info mr-2"></i>

                    Barang Stok Berlebih

                </h5>

                <small class="text-muted">

                    Barang dengan stok relatif tinggi dibandingkan
                    batas minimumnya.

                </small>

            </div>


            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover mb-0">

                        <thead class="bg-light">

                            <tr>

                                <th>
                                    Barang
                                </th>

                                <th>
                                    Stok
                                </th>

                                <th>
                                    Status
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                        @forelse($barangStokBerlebih as $item)

                            <tr>

                                <td>

                                    <strong>
                                        {{ $item->nama_barang }}
                                    </strong>

                                    <br>

                                    <small class="text-muted">

                                        Minimum:
                                        {{ $item->stok_minimum }}

                                    </small>

                                </td>

                                <td>

                                    <strong class="text-info">
                                        {{ $item->stok }}
                                    </strong>

                                </td>

                                <td>

                                    <span class="badge badge-info">
                                        Berlebih
                                    </span>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="3"
                                    class="text-center py-4">

                                    <i class="fas fa-check-circle text-success fa-2x mb-2"></i>

                                    <div>
                                        Tidak ada barang stok berlebih.
                                    </div>

                                </td>

                            </tr>

                        @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>



{{-- =========================================================
    BARANG HAMPIR HABIS
========================================================== --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white border-0">

        <h5 class="font-weight-bold mb-1">

            <i class="fas fa-exclamation-triangle text-warning mr-2"></i>

            Barang Hampir Habis

        </h5>

        <small class="text-muted">

            Barang yang berada pada atau di bawah batas minimum stok.

        </small>

    </div>


    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover mb-0">

                <thead class="bg-light">

                    <tr>

                        <th>
                            Barang
                        </th>

                        <th>
                            Stok
                        </th>

                        <th>
                            Minimum
                        </th>

                        <th>
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

                        <td>

                            <strong class="text-danger">
                                {{ $item->stok }}
                            </strong>

                        </td>

                        <td>

                            {{ $item->stok_minimum }}

                        </td>

                        <td>

                            <span class="badge badge-warning">

                                <i class="fas fa-exclamation-circle mr-1"></i>

                                Menipis

                            </span>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="4"
                            class="text-center py-5">

                            <i class="fas fa-check-circle text-success fa-2x mb-2"></i>

                            <div class="font-weight-bold">

                                Tidak ada barang yang menipis.

                            </div>

                            <small class="text-muted">

                                Kondisi stok relatif aman.

                            </small>

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>



{{-- =========================================================
    AKTIVITAS TERBARU
========================================================== --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white border-0">

        <h5 class="font-weight-bold mb-1">

            <i class="fas fa-history text-primary mr-2"></i>

            Aktivitas Terbaru

        </h5>

        <small class="text-muted">

            Aktivitas barang masuk dan keluar pada periode terpilih.

        </small>

    </div>


    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover mb-0">

                <thead class="bg-light">

                    <tr>

                        <th>
                            Tanggal
                        </th>

                        <th>
                            Kode Transaksi
                        </th>

                        <th>
                            Barang
                        </th>

                        <th>
                            Jenis
                        </th>

                    </tr>

                </thead>


                <tbody>
                    @forelse ($aktivitasTerbaru as $aktivitas)
                        <tr>
                            <td>
                                {{ $aktivitas['tanggal']->timezone('Asia/Jakarta')->format('d-m-Y H:i') }}
                            </td>

                            <td>
                                <strong>{{ $aktivitas['kode'] }}</strong>
                            </td>

                            <td>
                                {{ $aktivitas['barang'] }}
                            </td>

                            <td>
                                <span class="badge bg-{{ $aktivitas['badge'] }}">
                                    {{ $aktivitas['jenis'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">
                                Belum ada aktivitas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

        </div>

    </div>

</div>



{{-- =========================================================
    INFORMASI
========================================================== --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-body">

        <div class="d-flex align-items-start">

            <i class="fas fa-info-circle text-primary fa-lg mr-3"></i>

            <div>

                <h6 class="font-weight-bold mb-1">
                    Tentang Monitoring
                </h6>

                <p class="text-muted small mb-0">

                    Modul monitoring digunakan untuk membantu pengguna
                    dalam memantau kondisi persediaan secara berkala.
                    Sistem memberikan informasi mengenai stok aman,
                    stok menipis, stok habis, stok berlebih, pergerakan
                    barang, serta memberikan feedback dan rekomendasi
                    berdasarkan kondisi inventory.

                </p>

            </div>

        </div>

    </div>

</div>

</div>

@stop



{{-- =============================================================
JAVASCRIPT
============================================================= --}}

@section('js')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

let inventoryChart = null;


/*
|--------------------------------------------------------------------------
| UBAH PERIODE
|--------------------------------------------------------------------------
*/

function ubahPeriode()
{
    const periode =
        document.getElementById('periode').value;

    const url =
        new URL(window.location.href);

    url.searchParams.set(
        'periode',
        periode
    );

    window.location.href =
        url.toString();
}


/*
|--------------------------------------------------------------------------
| LOAD CHART
|--------------------------------------------------------------------------
*/

function loadChart(periode)
{

    fetch(
        "/monitoring/chart?periode=" +
        encodeURIComponent(periode)
    )

    .then(response => {

        if (!response.ok) {

            throw new Error(
                'Gagal mengambil data grafik.'
            );

        }

        return response.json();

    })

    .then(result => {

        const canvas =
            document.getElementById(
                'inventoryChart'
            );

        if (!canvas) {
            return;
        }


        if (inventoryChart) {

            inventoryChart.destroy();

        }


        const ctx =
            canvas.getContext('2d');


        inventoryChart =
            new Chart(
                ctx,
                {

                    type: 'line',

                    data: {

                        labels:
                            result.labels,

                        datasets: [

                            {

                                label:
                                    'Barang Masuk',

                                data:
                                    result.masuk,

                                borderColor:
                                    '#198754',

                                backgroundColor:
                                    'rgba(25,135,84,0.10)',

                                borderWidth: 2,

                                fill: true,

                                tension: 0.35,

                                pointRadius: 3,

                                pointHoverRadius: 6

                            },


                            {

                                label:
                                    'Barang Keluar',

                                data:
                                    result.keluar,

                                borderColor:
                                    '#dc3545',

                                backgroundColor:
                                    'rgba(220,53,69,0.10)',

                                borderWidth: 2,

                                fill: true,

                                tension: 0.35,

                                pointRadius: 3,

                                pointHoverRadius: 6

                            }

                        ]

                    },


                    options: {

                        responsive: true,

                        maintainAspectRatio: false,


                        interaction: {

                            intersect: false,

                            mode: 'index'

                        },


                        plugins: {

                            legend: {

                                position: 'top',

                                labels: {

                                    usePointStyle: true,

                                    padding: 20

                                }

                            },


                            tooltip: {

                                padding: 12

                            }

                        },


                        scales: {

                            y: {

                                beginAtZero: true,

                                ticks: {

                                    precision: 0

                                }

                            },


                            x: {

                                grid: {

                                    display: false

                                }

                            }

                        }

                    }

                }
            );

    })


    .catch(error => {

        console.error(
            'Monitoring Chart Error:',
            error
        );

    });

}


/*
|--------------------------------------------------------------------------
| INITIALIZE
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'DOMContentLoaded',
    function() {

        const periode =
            "{{ $periode }}";

        loadChart(periode);

    }
);

</script>

@stop