@extends('adminlte::page')

@section('title','Edit User')

@section('content')

<x-shared.page-header>

    <x-slot:title>
        Edit User
    </x-slot:title>

</x-shared.page-header>

<x-shared.card>

<form action="{{ route('users.update',$user) }}" method="POST">

    @csrf
    @method('PUT')

    @include('users.form')

</form>

</x-shared.card>

@stop