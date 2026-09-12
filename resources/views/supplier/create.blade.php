@extends('adminlte::page')

@section('title', 'Tambah Supplier')

@section('content')

<x-shared.page-header>

    <x-slot:title>
        Tambah Supplier
    </x-slot:title>

</x-shared.page-header>

<x-shared.card>

<form action="{{ route('supplier.store') }}" method="POST">

    @csrf

    @include('supplier.form')

</form>

</x-shared.card>

@stop