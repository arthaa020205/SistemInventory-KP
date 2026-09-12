@extends('adminlte::page')

@section('title', 'Tambah Barang Keluar')

@section('content')

<x-shared.page-header>

    <x-slot:title>
        Tambah Barang Keluar
    </x-slot:title>

</x-shared.page-header>

<x-shared.card>

    <form action="{{ route('barang-keluar.store') }}" method="POST">

        @csrf

        @include('barang-keluar.form')

        {{-- Tombol Aksi --}}
        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">

            <a href="{{ route('barang-keluar.index') }}"
               class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Kembali
            </a>

            <button type="submit"
                    class="btn btn-primary">
                <i class="fas fa-save me-1"></i>
                Simpan
            </button>

        </div>

    </form>

</x-shared.card>

@stop