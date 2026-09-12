@extends('adminlte::page')

@section('title', 'Data Supplier')

@section('content')

<br>

<x-shared.page-header>

<x-slot:title>
    Data Supplier
</x-slot:title>

<x-slot:action>

    @can('supplier.create')

        <a href="{{ route('supplier.create') }}" class="btn btn-primary">
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
                placeholder="Cari supplier...">

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
                <th>Supplier</th>
                <th>PIC</th>
                <th>Telepon</th>
                <th>Email</th>
                <th>Alamat</th>
                <th>Status</th>

                @canany(['supplier.edit', 'supplier.delete'])
                    <th width="170">Aksi</th>
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
                        {{ $item->nama_supplier }}
                    </td>

                    <td>
                        {{ $item->pic }}
                    </td>

                    <td>
                        {{ $item->telepon }}
                    </td>

                    <td>
                        {{ $item->email ?: '-' }}
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
                                Nonaktif
                            </span>

                        @endif

                    </td>


                    {{-- AKSI --}}
                    @canany(['supplier.edit', 'supplier.delete'])

                        <td>

                            @can('supplier.edit')

                                <a
                                    href="{{ route('supplier.edit', $item) }}"
                                    class="btn btn-warning btn-sm"
                                    title="Edit">

                                    <i class="fas fa-edit"></i>

                                </a>

                            @endcan


                            @can('supplier.delete')

                                <form
                                    action="{{ route('supplier.destroy', $item) }}"
                                    method="POST"
                                    class="d-inline">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-danger btn-sm"
                                        title="Hapus"
                                        onclick="return confirm('Yakin ingin menghapus supplier?')">

                                        <i class="fas fa-trash"></i>

                                    </button>

                                </form>

                            @endcan

                        </td>

                    @endcanany

                </tr>

            @empty

                <x-shared.empty
                    :colspan="auth()->user()->canany(['supplier.edit', 'supplier.delete']) ? 8 : 7"
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
