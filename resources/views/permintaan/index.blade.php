@extends('adminlte::page')

@section('title', 'Permintaan Pengadaan')

@section('content')

<x-shared.page-header>

    <x-slot:title>

        Permintaan Pengadaan

    </x-slot:title>

    <x-slot:action>

        @role('Petugas')
            <a href="{{ route('permintaan.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Tambah Permintaan
            </a>
        @endrole

    </x-slot:action>

</x-shared.page-header>

<x-shared.alert />

<x-shared.card>

<form method="GET" class="mb-3">

    <div class="row">

        <div class="col-md-4">

            <input
                type="text"
                name="search"
                class="form-control"
                value="{{ $search }}"
                placeholder="Cari kode atau nama barang...">

        </div>

        <div class="col-md-3">

            <select
                name="status"
                class="form-select">

                <option value="">Semua Status</option>

                <option value="Menunggu"
                    @selected($status=='Menunggu')>

                    Menunggu

                </option>

                <option value="Disetujui"
                    @selected($status=='Disetujui')>

                    Disetujui

                </option>

                <option value="Selesai"
                    @selected($status=='Selesai')>

                    Selesai

                </option>

                <option value="Ditolak"
                    @selected($status=='Ditolak')>

                    Ditolak

                </option>

            </select>

        </div>

        <div class="col-md-2">

            <button class="btn btn-primary w-100">

                <i class="fas fa-search"></i>

                Cari

            </button>

        </div>

        <div class="col-md-2">

            <a href="{{ route('permintaan.index') }}"
               class="btn btn-secondary w-100">

                Reset

            </a>

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

            <th width="120">Jumlah</th>

            <th width="120">Status</th>

            <th>Pengaju</th>

            <th width="190">Aksi</th>

        </tr>

    </thead>

    <tbody>

    @forelse($data as $item)

        <tr>

            <td>

                {{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}

            </td>

            <td>

                {{ $item->kode_permintaan }}

            </td>

            <td>

                {{ $item->tanggal_permintaan->format('d-m-Y') }}

            </td>

            <td>

                {{ $item->barang->nama_barang }}

            </td>

            <td>

                {{ $item->jumlah }}
                {{ $item->barang->satuan->nama_satuan }}

            </td>

            <td>

                @switch($item->status)

                    @case('Menunggu')

                        <span class="badge bg-warning">

                            Menunggu

                        </span>

                        @break

                    @case('Disetujui')

                        <span class="badge bg-primary">

                            Disetujui

                        </span>

                        @break

                    @case('Selesai')

                        <span class="badge bg-success">

                            Selesai

                        </span>

                        @break

                    @case('Ditolak')

                        <span class="badge bg-danger">

                            Ditolak

                        </span>

                        @break

                @endswitch

            </td>

            <td>

                {{ $item->user->name }}

            </td>

            <td>

                <a href="{{ route('permintaan.show',$item) }}"
                class="btn btn-info btn-sm">
                    <i class="fas fa-eye"></i>
                </a>

                @role('Petugas')

                    @if($item->status == 'Menunggu')

                        <a href="{{ route('permintaan.edit',$item) }}"
                        class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>

                        <form action="{{ route('permintaan.destroy',$item) }}"
                            method="POST"
                            class="d-inline">

                            @csrf
                            @method('DELETE')

                            <button class="btn btn-danger btn-sm"
                                    onclick="return confirm('Yakin ingin menghapus?')">
                                <i class="fas fa-trash"></i>
                            </button>

                        </form>

                    @endif

                @endrole


                @role('Owner')

                    @if($item->status == 'Menunggu')

                        <a href="{{ route('permintaan.approval',$item) }}"
                        class="btn btn-success btn-sm">
                            <i class="fas fa-check-circle"></i>
                        </a>

                    @endif

                @endrole

            </td>

        </tr>

    @empty

        <x-shared.empty :colspan="8" />

    @endforelse

    </tbody>

</table>

</div>

<div class="mt-3">

    {{ $data->links() }}

</div>

</x-shared.card>

@endsection