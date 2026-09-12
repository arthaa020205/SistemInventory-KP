@extends('adminlte::page')

@section('title', 'Barang Keluar')

@section('content')

<br>

<x-shared.page-header>

<x-slot:title>
    Data Barang Keluar
</x-slot:title>

<x-slot:action>

    @can('barang-keluar.create')

        <a
            href="{{ route('barang-keluar.create') }}"
            class="btn btn-primary">

            <i class="fas fa-plus"></i>

            Tambah Barang Keluar

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
                placeholder="Cari kode transaksi atau barang..."
                value="{{ $search }}">

        </div>


        <div class="col-md-3">

            <select
                name="jenis"
                class="form-select"
                onchange="this.form.submit()">

                <option value="">
                    Semua Jenis
                </option>

                <option
                    value="Transfer Ke Toko"
                    @selected($jenis == 'Transfer Ke Toko')>

                    Transfer Ke Toko

                </option>

                <option
                    value="Penjualan"
                    @selected($jenis == 'Penjualan')>

                    Penjualan

                </option>

                <option
                    value="Rusak"
                    @selected($jenis == 'Rusak')>

                    Rusak

                </option>

                <option
                    value="Retur"
                    @selected($jenis == 'Retur')>

                    Retur

                </option>

                <option
                    value="Pemakaian Internal"
                    @selected($jenis == 'Pemakaian Internal')>

                    Pemakaian Internal

                </option>

                <option
                    value="Kadaluarsa"
                    @selected($jenis == 'Kadaluarsa')>

                    Kadaluarsa

                </option>

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
                href="{{ route('barang-keluar.index') }}"
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

                <th>Kode</th>

                <th>Tanggal</th>

                <th>Barang</th>

                <th class="text-center">Qty</th>

                <th>Jenis</th>

                <th>Tujuan</th>

                <th>Petugas</th>


                @can('barang-keluar.view')

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
                        {{ $loop->iteration + $data->firstItem() - 1 }}
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


                    <td class="text-center">
                        {{ $item->jumlah }}
                    </td>


                    <td>

                        @if($item->jenis_keluar == 'Transfer Ke Toko')

                            <span class="badge bg-success">
                                Transfer Ke Toko
                            </span>

                        @elseif($item->jenis_keluar == 'Penjualan')

                            <span class="badge bg-primary">
                                Penjualan
                            </span>

                        @elseif($item->jenis_keluar == 'Rusak')

                            <span class="badge bg-danger">
                                Rusak
                            </span>

                        @elseif($item->jenis_keluar == 'Pemakaian Internal')

                            <span class="badge bg-info">
                                Pemakaian Internal
                            </span>

                        @elseif($item->jenis_keluar == 'Kadaluarsa')

                            <span class="badge bg-secondary">
                                Kadaluarsa
                            </span>

                        @elseif($item->jenis_keluar == 'Retur')

                            <span class="badge bg-warning text-dark">
                                Retur
                            </span>

                        @else

                            <span class="badge bg-dark">
                                {{ $item->jenis_keluar }}
                            </span>

                        @endif

                    </td>


                    <td>
                        {{ $item->tujuan ?? '-' }}
                    </td>


                    <td>
                        {{ $item->user->name }}
                    </td>


                    {{-- AKSI --}}
                    @can('barang-keluar.view')

                        <td class="text-center">

                            {{-- DETAIL --}}
                            <a
                                href="{{ route('barang-keluar.show', $item->id) }}"
                                class="btn btn-info btn-sm"
                                title="Detail">

                                <i class="fas fa-eye"></i>

                            </a>


                            {{-- EDIT --}}
                            @can('barang-keluar.edit')

                                <a
                                    href="{{ route('barang-keluar.edit', $item->id) }}"
                                    class="btn btn-warning btn-sm"
                                    title="Edit">

                                    <i class="fas fa-edit"></i>

                                </a>

                            @endcan


                            {{-- DELETE --}}
                            @can('barang-keluar.delete')

                                <form
                                    action="{{ route('barang-keluar.destroy', $item->id) }}"
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
                    :colspan="auth()->user()->can('barang-keluar.view') ? 9 : 8"
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
