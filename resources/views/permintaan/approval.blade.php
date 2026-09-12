@extends('adminlte::page')

@section('title', 'Approval Permintaan Pengadaan')

@section('content')

<x-shared.page-header>

    <x-slot:title>

        Approval Permintaan Pengadaan

    </x-slot:title>

    <x-slot:action>

        <a href="{{ route('permintaan.index') }}" class="btn btn-secondary">

            <i class="fas fa-arrow-left"></i>

            Kembali

        </a>

    </x-slot:action>

</x-shared.page-header>

<x-shared.alert />

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
                <td>{{ $permintaan->tanggal_permintaan->format('d-m-Y') }}</td>
            </tr>

            <tr>
                <th>Barang</th>
                <td>{{ $permintaan->barang->nama_barang }}</td>
            </tr>

            <tr>
                <th>Stok Saat Ini</th>
                <td>
                    {{ $permintaan->barang->stok }}
                    {{ $permintaan->barang->satuan->nama_satuan }}
                </td>
            </tr>

            <tr>
                <th>Stok Minimum</th>
                <td>
                    {{ $permintaan->barang->stok_minimum }}
                    {{ $permintaan->barang->satuan->nama_satuan }}
                </td>
            </tr>

            <tr>
                <th>Jumlah Diminta</th>
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

                    @if($permintaan->status=='Menunggu')

                        <span class="badge bg-warning">

                            Menunggu

                        </span>

                    @elseif($permintaan->status=='Disetujui')

                        <span class="badge bg-success">

                            Disetujui

                        </span>

                    @else

                        <span class="badge bg-danger">

                            Ditolak

                        </span>

                    @endif

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

            @if($permintaan->approver)

            <tr>

                <th>Diproses Oleh</th>

                <td>

                    {{ $permintaan->approver->name }}

                </td>

            </tr>

            <tr>

                <th>Tanggal Approval</th>

                <td>

                    {{ $permintaan->approved_at->format('d-m-Y H:i') }}

                </td>

            </tr>

            @endif

        </table>

    </div>

</div>

@if($permintaan->status=='Menunggu')

<hr>

<div class="row">

    <div class="col-md-6">

        <form
            action="{{ route('permintaan.setujui',$permintaan) }}"
            method="POST">

            @csrf
            @method('PUT')

            <button
                class="btn btn-success btn-lg w-100">

                <i class="fas fa-check-circle"></i>

                SETUJUI PERMINTAAN

            </button>

        </form>

    </div>

    <div class="col-md-6">

        <form
            action="{{ route('permintaan.tolak',$permintaan) }}"
            method="POST">

            @csrf
            @method('PUT')

            <div class="mb-3">

                <textarea
                    name="catatan_owner"
                    class="form-control @error('catatan_owner') is-invalid @enderror"
                    rows="3"
                    placeholder="Masukkan alasan penolakan..."></textarea>

                @error('catatan_owner')

                    <div class="invalid-feedback">

                        {{ $message }}

                    </div>

                @enderror

            </div>

            <button
                class="btn btn-danger btn-lg w-100">

                <i class="fas fa-times-circle"></i>

                TOLAK PERMINTAAN

            </button>

        </form>

    </div>

</div>

@endif

</x-shared.card>

@endsection