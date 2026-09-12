@extends('adminlte::page')

@section('title','Laporan Barang Keluar')

@section('content')

<x-shared.page-header>

    <x-slot:title>

        Laporan Barang Keluar

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
            name="pelanggan_id"
            class="form-select">

            <option value="">Semua Pelanggan</option>

            @foreach($pelanggan as $item)

                <option
                    value="{{ $item->id }}"
                    @selected($pelanggan_id == $item->id)>

                    {{ $item->nama_pelanggan }}

                </option>

            @endforeach

        </select>

    </div>

    <div class="col-md-3 mb-3">

        <select
            name="jenis_keluar"
            class="form-select">

            <option value="">Semua Jenis</option>

            <option value="Penjualan" @selected($jenis_keluar=='Penjualan')>
                Penjualan
            </option>

            <option value="Retur" @selected($jenis_keluar=='Retur')>
                Retur
            </option>

            <option value="Rusak" @selected($jenis_keluar=='Rusak')>
                Rusak
            </option>

            <option value="Pemakaian Internal" @selected($jenis_keluar=='Pemakaian Internal')>
                Pemakaian Internal
            </option>

            <option value="Lainnya" @selected($jenis_keluar=='Lainnya')>
                Lainnya
            </option>

        </select>

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

    <div class="col-md-6 d-flex align-items-end">

        <button class="btn btn-primary me-2">

            <i class="fas fa-search"></i>

            Filter

        </button>

        <a
            href="{{ route('laporan.barang-keluar') }}"
            class="btn btn-secondary">

            Reset

        </a>

    </div>

</div>

</form>

<hr>

<div class="row mb-3">

    <div class="col-md-3">

        <div class="small-box bg-danger">

            <div class="inner">

                <h3>{{ number_format($totalQty) }}</h3>

                <p>Total Barang Keluar</p>

            </div>

            <div class="icon">

                <i class="fas fa-arrow-up"></i>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="small-box bg-success">

            <div class="inner">

                <h3>

                    Rp {{ number_format($totalPenjualan,0,',','.') }}

                </h3>

                <p>Total Penjualan</p>

            </div>

            <div class="icon">

                <i class="fas fa-money-bill-wave"></i>

            </div>

        </div>

    </div>

</div>

<div class="d-flex justify-content-end mb-3">

    <a
        href="{{ route('laporan.barang-keluar.excel', request()->query()) }}"
        class="btn btn-success me-2">

        <i class="fas fa-file-excel"></i>

        Export Excel

    </a>

    <a
        href="{{ route('laporan.barang-keluar.pdf', request()->query()) }}"
        target="_blank"
        class="btn btn-danger">

        <i class="fas fa-file-pdf"></i>

        Export PDF

    </a>

</div>

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead class="table-light">

<tr>

<th>No</th>
<th>Kode</th>
<th>Tanggal</th>
<th>Barang</th>
<th>Pelanggan</th>
<th class="text-center">Qty</th>
<th class="text-end">Harga Jual</th>
<th class="text-end">Total</th>
<th>Jenis</th>
<th>Tujuan</th>
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

{{ $item->tanggal_keluar->format('d-m-Y') }}

</td>

<td>

{{ $item->barang->nama_barang }}

</td>

<td>

{{ $item->pelanggan->nama_pelanggan ?? '-' }}

</td>

<td class="text-center">

{{ $item->jumlah }}

</td>

<td class="text-end">

@if($item->jenis_keluar == 'Penjualan')

    Rp {{ number_format($item->harga_jual,0,',','.') }}

@else

    -

@endif

</td>

<td class="text-end">

@if($item->jenis_keluar == 'Penjualan')

    <strong class="text-success">

        Rp {{ number_format($item->total_harga,0,',','.') }}

    </strong>

@else

    -

@endif

</td>

<td>

@if($item->jenis_keluar=='Penjualan')

<span class="badge bg-primary">Penjualan</span>

@elseif($item->jenis_keluar=='Rusak')

<span class="badge bg-danger">Rusak</span>

@elseif($item->jenis_keluar=='Retur')

<span class="badge bg-warning">Retur</span>

@elseif($item->jenis_keluar=='Pemakaian Internal')

<span class="badge bg-info">Internal</span>

@else

<span class="badge bg-secondary">{{ $item->jenis_keluar }}</span>

@endif

</td>

<td>

{{ $item->tujuan ?: '-' }}

</td>

<td>

{{ $item->user->name }}

</td>

</tr>

@empty

<x-shared.empty :colspan="11"/>

@endforelse

</tbody>

</table>

</div>

<div class="mt-3">

{{ $data->links() }}

</div>

</x-shared.card>

@endsection