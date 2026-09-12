@extends('adminlte::page')

@section('title', 'Detail Permintaan Pengadaan')

@section('content')

<x-shared.page-header>

    <x-slot:title>

        Detail Permintaan Pengadaan

    </x-slot:title>

    <x-slot:action>

        <a href="{{ route('permintaan.index') }}"
           class="btn btn-secondary">

            <i class="fas fa-arrow-left"></i>

            Kembali

        </a>

    </x-slot:action>

</x-shared.page-header>

<x-shared.card>

<div class="row">

    <div class="col-md-6">

        <table class="table table-bordered">

            <tr>

                <th width="180">Kode Permintaan</th>

                <td>{{ $permintaan->kode_permintaan }}</td>

            </tr>

            <tr>

                <th>Tanggal Permintaan</th>

                <td>

                    {{ $permintaan->tanggal_permintaan->format('d-m-Y') }}

                </td>

            </tr>

            <tr>

                <th>Barang</th>

                <td>

                    {{ $permintaan->barang->nama_barang }}

                </td>

            </tr>

            <tr>

                <th>Jumlah</th>

                <td>

                    {{ $permintaan->jumlah }}

                    {{ $permintaan->barang->satuan->nama_satuan }}

                </td>

            </tr>

        </table>

    </div>

    <div class="col-md-6">

        <table class="table table-bordered">

            <tr>

                <th width="180">Pengaju</th>

                <td>

                    {{ $permintaan->user->name }}

                </td>

            </tr>

            <tr>

                <th>Status</th>

                <td>

                    @switch($permintaan->status)

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

            </tr>

            <tr>

                <th>Alasan</th>

                <td>

                    {{ $permintaan->alasan }}

                </td>

            </tr>

            <tr>

                <th>Catatan Owner</th>

                <td>

                    {{ $permintaan->catatan_owner ?: '-' }}

                </td>

            </tr>

            <tr>

                <th>Disetujui Oleh</th>

                <td>

                    {{ optional($permintaan->approvedBy)->name ?? '-' }}

                </td>

            </tr>

            <tr>

                <th>Tanggal Approval</th>

                <td>

                    {{ $permintaan->approved_at ? $permintaan->approved_at->format('d-m-Y H:i') : '-' }}

                </td>

            </tr>

        </table>

    </div>

</div>

@if($permintaan->status == 'Selesai')

<hr>

<div class="alert alert-success mb-0">

    <i class="fas fa-check-circle"></i>

    Permintaan ini telah direalisasikan melalui transaksi
    <strong>Barang Masuk</strong>,
    sehingga status permintaan berubah menjadi
    <strong>Selesai</strong>.

</div>

@endif

</x-shared.card>

@endsection