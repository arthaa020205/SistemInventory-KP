@extends('adminlte::page')

@section('title', 'Edit Barang')

@section('content')

<x-shared.page-header>

    <x-slot:title>
        Edit Barang
    </x-slot:title>

</x-shared.page-header>


<x-shared.card>

    <form
        action="{{ route('barang.update', $barang) }}"
        method="POST">

        @csrf
        @method('PUT')

        @include('barang.form')

    </form>

</x-shared.card>

@stop