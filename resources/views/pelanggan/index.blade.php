@extends('adminlte::page')

@section('title', 'Data Pelanggan')

@section('content')

<br> 

<x-shared.page-header>

<x-slot:title>
    Data Pelanggan
</x-slot:title>

<x-slot:action>

    @can('pelanggan.create')
        <a href="{{ route('pelanggan.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Tambah Pelanggan
        </a>
    @endcan

</x-slot:action>

</x-shared.page-header>

<x-shared.alert />

<x-shared.card>

{{-- SEARCH --}}
<form method="GET" class="mb-3">

    <div class="row">

        <div class="col-md-4">

            <input
                type="text"
                name="search"
                value="{{ $search }}"
                class="form-control"
                placeholder="Cari pelanggan...">

        </div>

        <div class="col-md-2">

            <button type="submit" class="btn btn-primary">

                <i class="fas fa-search"></i>
                Cari

            </button>

        </div>

    </div>

</form>


{{-- TABLE --}}
<div class="table-responsive">

    <table class="table table-bordered table-hover">

        <thead class="table-light">

            <tr>

                <th width="60">No</th>
                <th>Kode</th>
                <th>Nama Pelanggan</th>
                <th>No HP</th>
                <th>Alamat</th>
                <th>Status</th>

                @canany(['pelanggan.edit', 'pelanggan.delete'])
                    <th width="120">Aksi</th>
                @endcanany

            </tr>

        </thead>


        <tbody>

            @forelse($data as $item)

                <tr>

                    <td>
                        {{ $loop->iteration + ($data->firstItem() - 1) }}
                    </td>

                    <td>
                        {{ $item->kode_pelanggan }}
                    </td>

                    <td>
                        {{ $item->nama_pelanggan }}
                    </td>

                    <td>
                        {{ $item->no_hp ?: '-' }}
                    </td>

                    <td style="max-width:250px; white-space:normal;">
                        {{ $item->alamat ?: '-' }}
                    </td>

                    <td>

                        @if($item->status == 'Aktif')

                            <span class="badge bg-success">
                                Aktif
                            </span>

                        @else

                            <span class="badge bg-danger">
                                Tidak Aktif
                            </span>

                        @endif

                    </td>


                    {{-- AKSI --}}
                    @canany(['pelanggan.edit', 'pelanggan.delete'])

                        <td>

                            @can('pelanggan.edit')

                                <a
                                    href="{{ route('pelanggan.edit', $item->id) }}"
                                    class="btn btn-warning btn-sm"
                                    title="Edit">

                                    <i class="fas fa-edit"></i>

                                </a>

                            @endcan


                            @can('pelanggan.delete')

                                <form
                                    action="{{ route('pelanggan.destroy', $item->id) }}"
                                    method="POST"
                                    class="d-inline">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-danger btn-sm"
                                        title="Hapus"
                                        onclick="return confirm('Yakin ingin menghapus pelanggan ini?')">

                                        <i class="fas fa-trash"></i>

                                    </button>

                                </form>

                            @endcan

                        </td>

                    @endcanany

                </tr>

            @empty

                <x-shared.empty
                    :colspan="auth()->user()->canany(['pelanggan.edit', 'pelanggan.delete']) ? 7 : 6"
                />

            @endforelse

        </tbody>

    </table>

</div>


{{-- PAGINATION --}}
<div class="mt-3">

    {{ $data->links() }}

</div>

</x-shared.card>

@stop
