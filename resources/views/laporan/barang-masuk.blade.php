@extends('adminlte::page')

@section('title','Laporan Barang Masuk')

@section('content')

<x-shared.page-header>

    <x-slot:title>

        Laporan Barang Masuk

    </x-slot:title>

    <x-slot:action>

        <a
            href="{{ route('laporan.index') }}"
            class="btn btn-secondary">

            <i class="fas fa-arrow-left"></i>

            Kembali

        </a>

    </x-slot:action>

</x-shared.page-header>

<x-shared.alert />

<x-shared.card>

<form method="GET">

<div class="row">

    <div class="col-md-3 mb-3">

        <input
            type="text"
            name="search"
            class="form-control"
            placeholder="Cari kode transaksi..."
            value="{{ $search }}">

    </div>

    <div class="col-md-3 mb-3">

        <select
            name="barang_id"
            class="form-select">

            <option value="">Semua Barang</option>

            @foreach($barang as $item)

                <option
                    value="{{ $item->id }}"
                    @selected($barang_id == $item->id)>

                    {{ $item->nama_barang }}

                </option>

            @endforeach

        </select>

    </div>

    <div class="col-md-3 mb-3">

        <select
            name="supplier_id"
            class="form-select">

            <option value="">Semua Supplier</option>

            @foreach($supplier as $item)

                <option
                    value="{{ $item->id }}"
                    @selected($supplier_id == $item->id)>

                    {{ $item->nama_supplier }}

                </option>

            @endforeach

        </select>

    </div>

    <div class="col-md-3 mb-3">

        <button class="btn btn-primary">

            <i class="fas fa-search"></i>

            Filter

        </button>

        <a
            href="{{ route('laporan.barang-masuk') }}"
            class="btn btn-secondary">

            Reset

        </a>

    </div>

</div>

<div class="row">

    <div class="col-md-3">

        <label>Tanggal Awal</label>

        <input
            type="date"
            name="tanggal_awal"
            class="form-control"
            value="{{ $tanggal_awal }}">

    </div>

    <div class="col-md-3">

        <label>Tanggal Akhir</label>

        <input
            type="date"
            name="tanggal_akhir"
            class="form-control"
            value="{{ $tanggal_akhir }}">

    </div>

</div>

</form>

<hr>

<div class="row mb-3">

    <div class="col-md-3">

        <div class="small-box bg-info">

            <div class="inner">

                <h3>{{ $totalQty }}</h3>

                <p>Total Qty</p>

            </div>

            <div class="icon">

                <i class="fas fa-boxes"></i>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="small-box bg-success">

            <div class="inner">

                <h3>

                    Rp {{ number_format($totalPembelian,0,',','.') }}

                </h3>

                <p>Total Pembelian</p>

            </div>

            <div class="icon">

                <i class="fas fa-money-bill"></i>

            </div>

        </div>

    </div>

</div>

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead>
<div class="d-flex justify-content-end mb-3">

    <a
        href="{{ route('laporan.barang-masuk.excel', request()->query()) }}"
        class="btn btn-success me-2">

        <i class="fas fa-file-excel"></i>

        Export Excel

    </a>

    <a
        href="{{ route('laporan.barang-masuk.pdf', request()->query()) }}"
        target="_blank"
        class="btn btn-danger">

        <i class="fas fa-file-pdf"></i>

        Export PDF

    </a>

</div>
<tr>

<th>No</th>
<th>Kode</th>
<th>Tanggal</th>
<th>Barang</th>
<th>Supplier</th>
<th>Qty</th>
<th>Harga</th>
<th>Subtotal</th>
<th>Petugas</th>

</tr>

</thead>

<tbody>

@forelse($data as $item)

<tr>

<td>

{{ $loop->iteration + $data->firstItem()-1 }}

</td>

<td>

{{ $item->kode_transaksi }}

</td>

<td>

{{ $item->tanggal_masuk->format('d-m-Y') }}

</td>

<td>

{{ $item->barang->nama_barang }}

</td>

<td>

{{ $item->supplier->nama_supplier }}

</td>

<td class="text-center">

{{ $item->jumlah }}

</td>

<td class="text-end">

Rp {{ number_format($item->harga_beli,0,',','.') }}

</td>

<td class="text-end">

Rp {{ number_format($item->jumlah * $item->harga_beli,0,',','.') }}

</td>

<td>

{{ $item->user->name }}

</td>

</tr>

@empty

<x-shared.empty :colspan="9"/>

@endforelse

</tbody>

</table>

</div>

<div class="mt-3">

{{ $data->links() }}

</div>

</x-shared.card>

@stop