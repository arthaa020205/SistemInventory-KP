@extends('adminlte::page')

@section('title', 'Edit Permintaan Pengadaan')

@section('content')

<x-shared.page-header>

    <x-slot:title>

        Edit Permintaan Pengadaan

    </x-slot:title>

</x-shared.page-header>

<x-shared.card>

    <form action="{{ route('permintaan.update', $permintaan) }}" method="POST">

        @csrf
        @method('PUT')

        @include('permintaan.form')

    </form>

</x-shared.card>

@endsection