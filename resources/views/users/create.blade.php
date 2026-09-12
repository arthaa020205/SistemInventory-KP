@extends('adminlte::page')

@section('title','Tambah User')

@section('content')

<x-shared.page-header>

    <x-slot:title>
        Tambah User
    </x-slot:title>

</x-shared.page-header>

<x-shared.card>

<form action="{{ route('users.store') }}" method="POST">

    @csrf

    @include('users.form')

</form>

</x-shared.card>

@stop