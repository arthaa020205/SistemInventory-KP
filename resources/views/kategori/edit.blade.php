@extends('adminlte::page')

@section('title', 'Edit Kategori')

@section('content')

<x-shared.page-header>

    <x-slot:title>
        Edit Kategori
    </x-slot:title>

</x-shared.page-header>

<x-shared.card>

<form action="{{ route('kategori.update', $kategori) }}" method="POST">

    @csrf
    @method('PUT')

    @include('kategori.form')

</form>

</x-shared.card>

@stop