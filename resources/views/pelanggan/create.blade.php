@extends('adminlte::page')

@section('title','Tambah Pelanggan')

@section('content')

<x-shared.page-header>

    <x-slot:title>

        Tambah Pelanggan

    </x-slot:title>

</x-shared.page-header>

<x-shared.card>

<form action="{{ route('pelanggan.store') }}" method="POST">

    @include('pelanggan.form')

</form>

</x-shared.card>

@stop