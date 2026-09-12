@extends('adminlte::page')

@section('title', 'Data User')

@section('content')

<br>

<x-shared.page-header>

    <x-slot:title>
        Data User
    </x-slot:title>

    <x-slot:action>

        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Tambah
        </a>

    </x-slot:action>

</x-shared.page-header>

<x-shared.alert />

<x-shared.card>

<form method="GET" class="mb-3">

    <div class="row">

        <div class="col-md-4">

            <input
                type="text"
                name="search"
                value="{{ $search }}"
                class="form-control"
                placeholder="Cari nama atau email">

        </div>

        <div class="col-md-2">

            <button class="btn btn-primary">

                <i class="fas fa-search"></i>

                Cari

            </button>

        </div>

    </div>

</form>

<div class="table-responsive">

<table class="table table-bordered table-hover">

    <thead class="table-light">

        <tr>

            <th width="60">No</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th width="170">Aksi</th>

        </tr>

    </thead>

    <tbody>

    @forelse($data as $item)

        <tr>

            <td>{{ $loop->iteration + ($data->firstItem() - 1) }}</td>

            <td>{{ $item->name }}</td>

            <td>{{ $item->email }}</td>

            <td>

                @foreach($item->roles as $role)

                    @if($role->name == 'Owner')

                        <span class="badge bg-primary">
                            {{ $role->name }}
                        </span>

                    @else

                        <span class="badge bg-success">
                            {{ $role->name }}
                        </span>

                    @endif

                @endforeach

            </td>

            <td>

                @if($item->status == 'Aktif')

                    <span class="badge bg-success">
                        Aktif
                    </span>

                @else

                    <span class="badge bg-danger">
                        Nonaktif
                    </span>

                @endif

            </td>

            <td>

                <a href="{{ route('users.edit', $item) }}"
                    class="btn btn-warning btn-sm">

                    <i class="fas fa-edit"></i>

                </a>

                @if(auth()->id() != $item->id)

                <form
                    action="{{ route('users.destroy', $item) }}"
                    method="POST"
                    class="d-inline">

                    @csrf
                    @method('DELETE')

                    <button
                        class="btn btn-danger btn-sm"
                        onclick="return confirm('Yakin ingin menghapus user ini?')">

                        <i class="fas fa-trash"></i>

                    </button>

                </form>

                @endif

            </td>

        </tr>

    @empty

        <x-shared.empty :colspan="6"/>

    @endforelse

    </tbody>

</table>

</div>

<div class="mt-3">

    {{ $data->links() }}

</div>

</x-shared.card>

@stop