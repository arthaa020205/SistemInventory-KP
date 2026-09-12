@extends('adminlte::page')

@section('title', 'Tambah Permintaan Pengadaan')

@section('content')

<x-shared.page-header>

    <x-slot:title>

        Tambah Permintaan Pengadaan

    </x-slot:title>

</x-shared.page-header>

<x-shared.card>

    <form action="{{ route('permintaan.store') }}" method="POST">

        @csrf

        @include('permintaan.form')

    </form>

</x-shared.card>

@endsection