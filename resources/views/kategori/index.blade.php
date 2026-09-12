@extends('adminlte::page')

@section('title', 'Data Kategori')

@section('content')

<br>

<x-shared.page-header>

<x-slot:title>
    Data Kategori
</x-slot:title>

<x-slot:action>

    @can('kategori.create')

        <a href="{{ route('kategori.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Tambah
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
                placeholder="Cari nama kategori atau deskripsi">

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

                <th>Nama Kategori</th>

                <th>Deskripsi</th>

                @canany(['kategori.edit', 'kategori.delete'])
                    <th width="180">Aksi</th>
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
                        {{ $item->nama_kategori }}
                    </td>

                    <td>
                        {{ $item->deskripsi ?: '-' }}
                    </td>


                    {{-- AKSI --}}
                    @canany(['kategori.edit', 'kategori.delete'])

                        <td>

                            @can('kategori.edit')

                                <a
                                    href="{{ route('kategori.edit', $item) }}"
                                    class="btn btn-warning btn-sm"
                                    title="Edit">

                                    <i class="fas fa-edit"></i>

                                </a>

                            @endcan


                            @can('kategori.delete')

                                <form
                                    action="{{ route('kategori.destroy', $item) }}"
                                    method="POST"
                                    class="d-inline">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-danger btn-sm"
                                        title="Hapus"
                                        onclick="return confirm('Yakin ingin menghapus kategori ini?')">

                                        <i class="fas fa-trash"></i>

                                    </button>

                                </form>

                            @endcan

                        </td>

                    @endcanany

                </tr>

            @empty

                <x-shared.empty
                    :colspan="auth()->user()->canany(['kategori.edit', 'kategori.delete']) ? 4 : 3"
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
