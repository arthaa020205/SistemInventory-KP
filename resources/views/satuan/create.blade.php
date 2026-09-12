@extends('adminlte::page')

@section('title', 'Tambah Satuan')

@section('content')

<x-shared.page-header>

    <x-slot:title>
        Tambah Satuan
    </x-slot:title>

</x-shared.page-header>

<x-shared.card>

<form action="{{ route('satuan.store') }}" method="POST">

    @csrf

    @include('satuan._form')

</form>

</x-shared.card>

@stop