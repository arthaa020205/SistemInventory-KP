@extends('adminlte::page')

@section('title', 'Data Satuan')

@section('content')

<br>

<x-shared.page-header>

    <x-slot:title>
        Data Satuan
    </x-slot:title>

    <x-slot:action>

        @can('satuan.create')

            <a href="{{ route('satuan.create') }}" class="btn btn-primary">
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
                    placeholder="Cari nama satuan">

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
                    <th>Nama Satuan</th>

                    @canany(['satuan.edit', 'satuan.delete'])
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
                            {{ $item->nama_satuan }}
                        </td>


                        {{-- AKSI --}}
                        @canany(['satuan.edit', 'satuan.delete'])

                            <td>

                                @can('satuan.edit')

                                    <a
                                        href="{{ route('satuan.edit', $item) }}"
                                        class="btn btn-warning btn-sm"
                                        title="Edit">

                                        <i class="fas fa-edit"></i>

                                    </a>

                                @endcan


                                @can('satuan.delete')

                                    <form
                                        action="{{ route('satuan.destroy', $item) }}"
                                        method="POST"
                                        class="d-inline">

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-danger btn-sm"
                                            title="Hapus"
                                            onclick="return confirm('Yakin ingin menghapus data?')">

                                            <i class="fas fa-trash"></i>

                                        </button>

                                    </form>

                                @endcan

                            </td>

                        @endcanany

                    </tr>

                @empty

                    <x-shared.empty
                        :colspan="auth()->user()->canany(['satuan.edit', 'satuan.delete']) ? 3 : 2"
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