@extends('adminlte::page')

@section('title','Edit Barang Masuk')

@section('content')

<x-shared.page-header>

    <x-slot:title>

        Edit Barang Masuk

    </x-slot:title>

</x-shared.page-header>

<x-shared.card>

<form action="{{ route('barang-masuk.update',$barangMasuk) }}" method="POST">

    @csrf

    @method('PUT')

    @include('barang-masuk.form')

</form>

</x-shared.card>

@stop