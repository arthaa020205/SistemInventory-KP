@extends('adminlte::page')

@section('title', 'Edit Satuan')

@section('content')

<x-shared.page-header>

    <x-slot:title>
        Edit Satuan
    </x-slot:title>

</x-shared.page-header>

<x-shared.card>

<form action="{{ route('satuan.update',$satuan) }}" method="POST">

    @csrf

    @method('PUT')

    @include('satuan._form')

</form>

</x-shared.card>

@stop