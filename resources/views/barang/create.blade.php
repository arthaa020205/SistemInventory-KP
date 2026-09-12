@extends('adminlte::page')

@section('title', 'Tambah Barang')

@section('content')

<x-shared.page-header>

    <x-slot:title>
        Tambah Barang
    </x-slot:title>

</x-shared.page-header>

<x-shared.card>

<form action="{{ route('barang.store') }}" method="POST">

    @csrf

    @include('barang.form')

</form>

</x-shared.card>

@stop