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

            <i class="fas fa-plus mr-1"></i>

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
                class="form-control"
                onchange="this.form.submit()">

                <option value="">
                    Semua Jenis
                </option>

                <option
                    value="Transfer ke Toko"
                    @selected($jenis == 'Transfer ke Toko')>

                    Transfer ke Toko

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
                    value="Pemakaian Internal"
                    @selected($jenis == 'Pemakaian Internal')>

                    Pemakaian Internal

                </option>

                <option
                    value="Kadaluarsa"
                    @selected($jenis == 'Kadaluarsa')>

                    Kadaluarsa

                </option>

                <option
                    value="Return"
                    @selected($jenis == 'Return')>

                    Return

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
                class="btn btn-primary mr-2">

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

        <thead class="thead-light">

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

                @php
                    /*
                    |--------------------------------------------------------------------------
                    | PENJUALAN MULTI ITEM
                    |--------------------------------------------------------------------------
                    */

                    $isPenjualan = $item->jenis_keluar === 'Penjualan';

                    $details = $isPenjualan
                        ? $item->details
                        : collect();

                    $jumlahDetail = $details->count();

                    $totalQtyPenjualan = $details->sum('jumlah');

                    /*
                    |--------------------------------------------------------------------------
                    | BARANG NON PENJUALAN
                    |--------------------------------------------------------------------------
                    */

                    $namaBarang = $item->barang?->nama_barang ?? '-';

                    $jumlahBarang = $item->jumlah ?? 0;
                @endphp


                <tr>

                    {{-- NO --}}
                    <td>
                        {{ $loop->iteration + $data->firstItem() - 1 }}
                    </td>


                    {{-- KODE --}}
                    <td>

                        <strong>
                            {{ $item->kode_transaksi }}
                        </strong>

                    </td>


                    {{-- TANGGAL --}}
                    <td>

                        {{ $item->tanggal_keluar?->format('d-m-Y') ?? '-' }}

                    </td>


                    {{-- BARANG --}}
                    <td>

                        @if($isPenjualan && $jumlahDetail > 0)

                            {{-- PENJUALAN MULTI ITEM --}}

                            <div>

                                <strong>
                                    {{ $jumlahDetail }} barang
                                </strong>

                            </div>

                            <div class="mt-1">

                                @foreach($details->take(3) as $detail)

                                    <div class="small text-muted">

                                        <i class="fas fa-box mr-1"></i>

                                        {{ $detail->barang?->nama_barang ?? '-' }}

                                        <span class="text-dark">
                                            ({{ rtrim(rtrim(number_format($detail->jumlah, 2, ',', '.'), '0'), ',') }}
                                            {{ $detail->satuan?->nama_satuan ?? '' }})
                                        </span>

                                    </div>

                                @endforeach


                                @if($jumlahDetail > 3)

                                    <div class="small text-primary mt-1">

                                        <i class="fas fa-ellipsis-h mr-1"></i>

                                        +{{ $jumlahDetail - 3 }} barang lainnya

                                    </div>

                                @endif

                            </div>

                        @else

                            {{-- BARANG BIASA --}}

                            <strong>
                                {{ $namaBarang }}
                            </strong>

                        @endif

                    </td>


                    {{-- QTY --}}
                    <td class="text-center">

                        @if($isPenjualan && $jumlahDetail > 0)

                            <span class="badge badge-primary">

                                {{ rtrim(rtrim(number_format($totalQtyPenjualan, 2, ',', '.'), '0'), ',') }}

                            </span>

                            <div class="small text-muted mt-1">

                                {{ $jumlahDetail }} jenis

                            </div>

                        @else

                            <span class="badge badge-secondary">

                                {{ rtrim(rtrim(number_format($jumlahBarang, 2, ',', '.'), '0'), ',') }}

                            </span>

                        @endif

                    </td>


                    {{-- JENIS --}}
                    <td>

                        @if($item->jenis_keluar === 'Transfer ke Toko')

                            <span class="badge badge-success">

                                <i class="fas fa-store mr-1"></i>

                                Transfer ke Toko

                            </span>

                        @elseif($item->jenis_keluar === 'Penjualan')

                            <span class="badge badge-primary">

                                <i class="fas fa-shopping-cart mr-1"></i>

                                Penjualan

                            </span>

                        @elseif($item->jenis_keluar === 'Rusak')

                            <span class="badge badge-danger">

                                <i class="fas fa-exclamation-triangle mr-1"></i>

                                Rusak

                            </span>

                        @elseif($item->jenis_keluar === 'Pemakaian Internal')

                            <span class="badge badge-info">

                                <i class="fas fa-tools mr-1"></i>

                                Pemakaian Internal

                            </span>

                        @elseif($item->jenis_keluar === 'Kadaluarsa')

                            <span class="badge badge-secondary">

                                <i class="fas fa-calendar-times mr-1"></i>

                                Kadaluarsa

                            </span>

                        @elseif($item->jenis_keluar === 'Return')

                            <span class="badge badge-warning">

                                <i class="fas fa-undo mr-1"></i>

                                Return

                            </span>

                        @else

                            <span class="badge badge-dark">

                                {{ $item->jenis_keluar }}

                            </span>

                        @endif

                    </td>


                    {{-- TUJUAN --}}
                    <td>

                        {{ $item->tujuan ?? '-' }}

                    </td>


                    {{-- PETUGAS --}}
                    <td>

                        {{ $item->user?->name ?? '-' }}

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
