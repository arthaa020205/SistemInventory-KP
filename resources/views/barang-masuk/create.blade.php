@extends('adminlte::page')

@section('title','Tambah Barang Masuk')

@section('content')

<x-shared.page-header>

    <x-slot:title>

        Tambah Barang Masuk

    </x-slot:title>

</x-shared.page-header>

<x-shared.card>

<form action="{{ route('barang-masuk.store') }}" method="POST">

    @csrf

    @include('barang-masuk.form')

</form>

</x-shared.card>

@stop