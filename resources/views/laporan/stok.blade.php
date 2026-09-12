@extends('adminlte::page')

@section('title', 'Laporan Stok')

@section('content')

<x-shared.page-header>

    <x-slot:title>
        Laporan Stok
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


{{-- FILTER --}}

<x-shared.card>

    <form method="GET">

        <div class="row">

            {{-- SEARCH --}}

            <div class="col-md-4 mb-3">

                <label>
                    Cari Barang
                </label>

                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="Kode atau nama barang..."
                    value="{{ $search }}">

            </div>


            {{-- KATEGORI --}}

            <div class="col-md-3 mb-3">

                <label>
                    Kategori
                </label>

                <select
                    name="kategori_id"
                    class="form-select">

                    <option value="">
                        Semua Kategori
                    </option>

                    @foreach($kategori as $item)

                        <option
                            value="{{ $item->id }}"
                            @selected($kategori_id == $item->id)>

                            {{ $item->nama_kategori }}

                        </option>

                    @endforeach

                </select>

            </div>


            {{-- STATUS --}}

            <div class="col-md-3 mb-3">

                <label>
                    Status Stok
                </label>

                <select
                    name="status"
                    class="form-select">

                    <option value="">
                        Semua Status
                    </option>

                    <option
                        value="Habis"
                        @selected($status == 'Habis')>

                        Habis

                    </option>

                    <option
                        value="Menipis"
                        @selected($status == 'Menipis')>

                        Menipis

                    </option>

                    <option
                        value="Aman"
                        @selected($status == 'Aman')>

                        Aman

                    </option>

                </select>

            </div>


            {{-- BUTTON --}}

            <div class="col-md-2 mb-3">

                <label class="d-block">
                    &nbsp;
                </label>

                <button
                    type="submit"
                    class="btn btn-primary">

                    <i class="fas fa-search"></i>
                    Filter

                </button>

                <a
                    href="{{ route('laporan.stok') }}"
                    class="btn btn-secondary">

                    Reset

                </a>

            </div>

        </div>

    </form>

</x-shared.card>


{{-- RINGKASAN --}}

<div class="row mb-3">

    <div class="col-md-3">

        <div class="small-box bg-info">

            <div class="inner">

                <h3>
                    {{ $totalBarang }}
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


    <div class="col-md-3">

        <div class="small-box bg-primary">

            <div class="inner">

                <h3>
                    {{ number_format($totalStok, 2, ',', '.') }}
                </h3>

                <p>
                    Total Stok
                </p>

            </div>

            <div class="icon">

                <i class="fas fa-warehouse"></i>

            </div>

        </div>

    </div>


    <div class="col-md-3">

        <div class="small-box bg-danger">

            <div class="inner">

                <h3>
                    {{ $totalHabis }}
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


    <div class="col-md-3">

        <div class="small-box bg-warning">

            <div class="inner">

                <h3>
                    {{ $totalMenipis }}
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

</div>


{{-- TABLE --}}

<x-shared.card>

    <div class="d-flex justify-content-end mb-3">

        <a
            href="{{ route('laporan.stok.excel', request()->query()) }}"
            class="btn btn-success me-2">

            <i class="fas fa-file-excel"></i>
            Export Excel

        </a>

        <a
            href="{{ route('laporan.stok.pdf', request()->query()) }}"
            target="_blank"
            class="btn btn-danger">

            <i class="fas fa-file-pdf"></i>
            Export PDF

        </a>

    </div>

    <div class="table-responsive">

        <table class="table table-bordered table-hover">

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

                    <th>
                        Kategori
                    </th>

                    <th>
                        Satuan
                    </th>

                    <th>
                        Stok
                    </th>

                    <th>
                        Stok Minimum
                    </th>

                    <th>
                        Lokasi Rak
                    </th>

                    <th>
                        Status
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($data as $item)

                    @php

                        if ($item->stok == 0) {

                            $statusStok = 'Habis';

                            $badge = 'danger';

                        } elseif (
                            $item->stok <= $item->stok_minimum
                        ) {

                            $statusStok = 'Menipis';

                            $badge = 'warning';

                        } else {

                            $statusStok = 'Aman';

                            $badge = 'success';

                        }

                    @endphp


                    <tr>

                        <td>
                            {{ $loop->iteration + $data->firstItem() - 1 }}
                        </td>

                        <td>
                            {{ $item->kode_barang }}
                        </td>

                        <td>
                            {{ $item->nama_barang }}
                        </td>

                        <td>
                            {{ $item->kategori?->nama_kategori ?? '-' }}
                        </td>

                        <td>
                            {{ $item->satuan?->nama_satuan ?? '-' }}
                        </td>

                        <td class="text-center">

                            <strong>
                                {{ number_format($item->stok, 2, ',', '.') }}
                            </strong>

                        </td>

                        <td class="text-center">

                            {{ number_format($item->stok_minimum, 2, ',', '.') }}

                        </td>

                        <td>

                            {{ $item->lokasi_rak ?? '-' }}

                        </td>

                        <td class="text-center">

                            <span
                                class="badge bg-{{ $badge }}">

                                {{ $statusStok }}

                            </span>

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