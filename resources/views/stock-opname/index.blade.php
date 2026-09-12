@extends('adminlte::page')

@section('title', 'Stock Opname')

@section('content')

<br>

<x-shared.page-header>

    <x-slot:title>
        Data Stock Opname
    </x-slot:title>

    <x-slot:action>

        @can('stock-opname.create')
            <a
                href="{{ route('stock-opname.create') }}"
                class="btn btn-primary">

                <i class="fas fa-plus"></i>

                Tambah Stock Opname

            </a>
        @endcan

    </x-slot:action>

</x-shared.page-header>

<x-shared.alert />

<x-shared.card>

<form method="GET">

    <div class="row mb-3">

        <div class="col-md-3">

            <input
                type="text"
                name="search"
                class="form-control"
                placeholder="Cari kode transaksi..."
                value="{{ $search }}">

        </div>

        <div class="col-md-3">

            <select
                name="barang"
                id="barang"
                class="form-select">

                <option value="">
                    Semua Barang
                </option>

                @foreach($barangList as $item)

                    <option
                        value="{{ $item->id }}"
                        @selected($barang == $item->id)>

                        {{ $item->nama_barang }}

                    </option>

                @endforeach

            </select>

        </div>

        <div class="col-md-2">

            <input
                type="date"
                name="tanggal_awal"
                class="form-control"
                value="{{ $tanggalAwal }}">

        </div>

        <div class="col-md-2">

            <input
                type="date"
                name="tanggal_akhir"
                class="form-control"
                value="{{ $tanggalAkhir }}">

        </div>

        <div class="col-md-2">

            <button
                class="btn btn-primary w-100">

                <i class="fas fa-search"></i>

                Filter

            </button>

        </div>

    </div>

</form>

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead class="table-light">

<tr>

    <th width="60">No</th>

    <th>Kode</th>

    <th>Tanggal</th>

    <th>Barang</th>

    <th>Stok Sistem</th>

    <th>Stok Fisik</th>

    <th>Selisih</th>

    <th>Status</th>

    <th>Petugas</th>

    <th width="100" class="text-center">Aksi</th>

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

        @if($item->selisih > 0)

            <span class="text-success">
                +{{ $item->selisih }}
            </span>

        @elseif($item->selisih < 0)

            <span class="text-danger">
                {{ $item->selisih }}
            </span>

        @else

            0

        @endif

    </td>

    <td>

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

    <td class="text-center">

        @can('stock-opname.view')

            <a
                href="{{ route('stock-opname.show', $item) }}"
                class="btn btn-info btn-sm">

                <i class="fas fa-eye"></i>

            </a>

        @endcan

    </td>

</tr>

@empty

    <x-shared.empty :colspan="10"/>

@endforelse

</tbody>

</table>

</div>

<div class="mt-3">

    {{ $data->links() }}

</div>

</x-shared.card>

@stop


@push('js')

<script>

document
    .getElementById('barang')
    .addEventListener('change', function () {

        this.form.submit();

    });

</script>

@endpush