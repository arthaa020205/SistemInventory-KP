@extends('adminlte::page')

@section('title', 'Activity Log')

@section('content')

<x-shared.page-header>

    <x-slot:title>
        Activity Log
    </x-slot:title>

    <x-slot:action>
    </x-slot:action>

</x-shared.page-header>

<x-shared.card>

    <form method="GET" class="mb-3">

        <div class="row">

            <div class="col-md-4">

                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    class="form-control"
                    placeholder="Cari aktivitas, modul, atau deskripsi...">

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

                    <th>Waktu</th>

                    <th>User</th>

                    <th>Aktivitas</th>

                    <th>Modul</th>

                    <th>Deskripsi</th>

                    <th>IP Address</th>

                </tr>

            </thead>

            <tbody>

                @forelse($data as $item)

                    <tr>

                        <td>
                            {{ $loop->iteration + ($data->firstItem() - 1) }}
                        </td>

                        <td>
                            @if($item->created_at)
                                {{ $item->created_at
                                    ->timezone('Asia/Jakarta')
                                    ->format('d-m-Y H:i:s') }}
                            @else
                                -
                            @endif
                        </td>

                        <td>
                            {{ $item->user->name ?? 'System' }}
                        </td>

                        <td>

                            @if($item->aktivitas === 'CREATE')

                                <span class="badge bg-success">
                                    CREATE
                                </span>

                            @elseif($item->aktivitas === 'UPDATE')

                                <span class="badge bg-warning text-dark">
                                    UPDATE
                                </span>

                            @elseif($item->aktivitas === 'DELETE')

                                <span class="badge bg-danger">
                                    DELETE
                                </span>

                            @else

                                <span class="badge bg-secondary">
                                    {{ $item->aktivitas }}
                                </span>

                            @endif

                        </td>

                        <td>
                            {{ $item->modul }}
                        </td>

                        <td>
                            {{ $item->deskripsi ?? '-' }}
                        </td>

                        <td>
                            {{ $item->ip_address ?? '-' }}
                        </td>

                    </tr>

                @empty

                    <x-shared.empty :colspan="7"/>

                @endforelse

            </tbody>

        </table>

    </div>

    <div class="mt-3">

        {{ $data->links() }}

    </div>

</x-shared.card>

@stop