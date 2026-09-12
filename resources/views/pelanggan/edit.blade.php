@extends('adminlte::page')

@section('title','Edit Pelanggan')

@section('content')

<x-shared.page-header>

    <x-slot:title>

        Edit Pelanggan

    </x-slot:title>

</x-shared.page-header>

<x-shared.card>

<form action="{{ route('pelanggan.update',$pelanggan) }}" method="POST">

    @csrf

    @method('PUT')

    @include('pelanggan.form')

</form>

</x-shared.card>

@stop