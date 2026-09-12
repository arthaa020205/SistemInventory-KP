@extends('adminlte::page')

@section('title', 'Barang Masuk')

@section('content')

<br>

<x-shared.page-header>

<x-slot:title>
    Data Barang Masuk
</x-slot:title>

<x-slot:action>

    @can('barang-masuk.create')

        <a href="{{ route('barang-masuk.create') }}" class="btn btn-primary">

            <i class="fas fa-plus"></i>

            Tambah Barang Masuk

        </a>

    @endcan

</x-slot:action>

</x-shared.page-header>

<x-shared.alert />

<x-shared.card>

{{-- FILTER --}}
<form method="GET" class="mb-3">

    <div class="row">

        <div class="col-md-3">

            <input
                type="text"
                name="search"
                class="form-control"
                placeholder="Cari transaksi..."
                value="{{ $search }}">

        </div>


        <div class="col-md-3">

            <select
                name="barang"
                class="form-select"
                onchange="this.form.submit()">

                <option value="">
                    Semua Barang
                </option>

                @foreach($barangs as $item)

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
                name="dari"
                class="form-control"
                value="{{ $dari }}">

        </div>


        <div class="col-md-2">

            <input
                type="date"
                name="sampai"
                class="form-control"
                value="{{ $sampai }}">

        </div>


        <div class="col-md-2 d-flex">

            <button
                type="submit"
                class="btn btn-primary me-2">

                <i class="fas fa-search"></i>

            </button>


            <a
                href="{{ route('barang-masuk.index') }}"
                class="btn btn-secondary">

                Reset

            </a>

        </div>

    </div>

</form>


{{-- TABLE --}}
<div class="table-responsive">

    <table class="table table-bordered table-hover">

        <thead class="table-light">

            <tr>

                <th width="60">No</th>
                <th>Kode Transaksi</th>
                <th>Tanggal</th>
                <th>Barang</th>
                <th>Supplier</th>
                <th class="text-center">Qty</th>
                <th class="text-end">Harga Beli</th>
                <th>Expired</th>
                <th>Petugas</th>

                {{-- DETAIL SELALU BOLEH --}}
                @can('barang-masuk.view')
                    <th width="150" class="text-center">
                        Aksi
                    </th>
                @endcan

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
                        Rp {{ number_format($item->harga_beli, 0, ',', '.') }}
                    </td>


                    <td>

                        @if($item->expired_date)

                            {{ $item->expired_date->format('d-m-Y') }}

                        @else

                            -

                        @endif

                    </td>


                    <td>
                        {{ $item->user->name }}
                    </td>


                    {{-- AKSI --}}
                    @can('barang-masuk.view')

                        <td class="text-center">

                            {{-- DETAIL --}}
                            <a
                                href="{{ route('barang-masuk.show', $item->id) }}"
                                class="btn btn-info btn-sm"
                                title="Detail">

                                <i class="fas fa-eye"></i>

                            </a>


                            {{-- EDIT --}}
                            @can('barang-masuk.edit')

                                <a
                                    href="{{ route('barang-masuk.edit', $item->id) }}"
                                    class="btn btn-warning btn-sm"
                                    title="Edit">

                                    <i class="fas fa-edit"></i>

                                </a>

                            @endcan


                            {{-- DELETE --}}
                            @can('barang-masuk.delete')

                                <form
                                    action="{{ route('barang-masuk.destroy', $item->id) }}"
                                    method="POST"
                                    class="d-inline">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-danger btn-sm"
                                        title="Hapus"
                                        onclick="return confirm('Yakin ingin menghapus transaksi ini?')">

                                        <i class="fas fa-trash"></i>

                                    </button>

                                </form>

                            @endcan

                        </td>

                    @endcan

                </tr>

            @empty

                <x-shared.empty
                    :colspan="auth()->user()->can('barang-masuk.view') ? 10 : 9"
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
