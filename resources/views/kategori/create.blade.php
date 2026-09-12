@extends('adminlte::page')

@section('title', 'Tambah Kategori')

@section('content')

<x-shared.page-header>

    <x-slot:title>
        Tambah Kategori
    </x-slot:title>

</x-shared.page-header>

<x-shared.card>

<form action="{{ route('kategori.store') }}" method="POST">

    @csrf

    @include('kategori.form')

</form>

</x-shared.card>

@stop