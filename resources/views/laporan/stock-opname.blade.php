@extends('adminlte::page')

@section('title', 'Laporan Stock Opname')

@section('content')

<x-shared.page-header>

    <x-slot:title>

        Laporan Stock Opname

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
                name="status"
                class="form-select">

                <option value="">Semua Status</option>

                <option
                    value="Sesuai"
                    @selected($status == 'Sesuai')>

                    Sesuai

                </option>

                <option
                    value="Selisih"
                    @selected($status == 'Selisih')>

                    Selisih

                </option>

            </select>

        </div>

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

    <div class="mt-3">

        <button class="btn btn-primary">

            <i class="fas fa-search"></i>

            Filter

        </button>

        <a
            href="{{ route('laporan.stock-opname') }}"
            class="btn btn-secondary">

            Reset

        </a>

    </div>

</form>

<hr>

<div class="row mb-3">

    <div class="col-md-3">

        <div class="small-box bg-primary">

            <div class="inner">

                <h3>{{ $totalOpname }}</h3>

                <p>Total Stock Opname</p>

            </div>

            <div class="icon">

                <i class="fas fa-clipboard-check"></i>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="small-box bg-warning">

            <div class="inner">

                <h3>{{ $totalSelisih }}</h3>

                <p>Total Selisih</p>

            </div>

            <div class="icon">

                <i class="fas fa-exclamation-triangle"></i>

            </div>

        </div>

    </div>

</div>

<div class="d-flex justify-content-end mb-3">

    <a
        href="{{ route('laporan.stock-opname.excel', request()->query()) }}"
        class="btn btn-success me-2">

        <i class="fas fa-file-excel"></i>

        Export Excel

    </a>

    <a
        href="{{ route('laporan.stock-opname.pdf', request()->query()) }}"
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
                <th>Stok Sistem</th>
                <th>Stok Fisik</th>
                <th>Selisih</th>
                <th>Status</th>
                <th>Petugas</th>

            </tr>

        </thead>

        <tbody>

            @forelse($data as $item)

                <tr>

                    <td>

                        {{ $loop->iteration + ($data->firstItem() - 1) }}

                    </td>

                    <td>

                        {{ $item->kode_transaksi }}

                    </td>

                    <td>

                        {{ $item->tanggal_opname->format('d-m-Y') }}

                    </td>

                    <td>

                        {{ $item->barang->nama_barang }}

                    </td>

                    <td class="text-center">

                        {{ $item->stok_sistem }}

                    </td>

                    <td class="text-center">

                        {{ $item->stok_fisik }}

                    </td>

                    <td class="text-center">

                        {{ $item->selisih }}

                    </td>

                    <td class="text-center">

                        @if($item->status == 'Sesuai')

                            <span class="badge bg-success">

                                Sesuai

                            </span>

                        @else

                            <span class="badge bg-warning text-dark">

                                Selisih

                            </span>

                        @endif

                    </td>

                    <td>

                        {{ $item->user->name }}

                    </td>

                </tr>

            @empty

                <x-shared.empty :colspan="9" />

            @endforelse

        </tbody>

    </table>

</div>

<div class="mt-3">

    {{ $data->links() }}

</div>

</x-shared.card>

@stop