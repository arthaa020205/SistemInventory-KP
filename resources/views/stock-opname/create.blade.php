@extends('adminlte::page')

@section('title','Tambah Stock Opname')

@section('content')

<x-shared.page-header>

    <x-slot:title>

        Tambah Stock Opname

    </x-slot:title>

</x-shared.page-header>

<x-shared.alert/>

<x-shared.card>

<form
    action="{{ route('stock-opname.store') }}"
    method="POST">

    @csrf

    @include('stock-opname.form')

</form>

</x-shared.card>

@stop