@extends('adminlte::page')

@section('title', 'Edit Supplier')

@section('content')

<x-shared.page-header>

    <x-slot:title>
        Edit Supplier
    </x-slot:title>

</x-shared.page-header>

<x-shared.card>

<form action="{{ route('supplier.update', $supplier) }}" method="POST">

    @csrf
    @method('PUT')

    @include('supplier.form')

</form>

</x-shared.card>

@stop