<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <title>
        Laporan Stok
    </title>

    @include('laporan.pdf._style')

</head>


<body>

    @include('laporan.pdf._header')


    <div class="report-title">
        LAPORAN STOK BARANG
    </div>

    <div class="report-subtitle">
        Informasi kondisi persediaan barang
    </div>


    <div class="print-info">

        Dicetak :
        {{ now()->format('d-m-Y H:i') }}

    </div>


    {{-- SUMMARY --}}

    <table class="summary-table">

        <tr>

            <td>

                <div class="summary-label">
                    Total Barang
                </div>

                <div class="summary-value">
                    {{ $totalBarang }}
                </div>

            </td>


            <td>

                <div class="summary-label">
                    Total Stok
                </div>

                <div class="summary-value">
                    {{ number_format($totalStok, 2, ',', '.') }}
                </div>

            </td>


            <td>

                <div class="summary-label">
                    Stok Habis
                </div>

                <div class="summary-value">
                    {{ $totalHabis }}
                </div>

            </td>


            <td>

                <div class="summary-label">
                    Stok Menipis
                </div>

                <div class="summary-value">
                    {{ $totalMenipis }}
                </div>

            </td>

        </tr>

    </table>


    {{-- DATA --}}

    <table class="data-table">

        <thead>

            <tr>

                <th width="4%">
                    No
                </th>

                <th>
                    Kode
                </th>

                <th>
                    Nama Barang
                </th>

                <th>
                    Kategori
                </th>

                <th>
                    Satuan
                </th>

                <th>
                    Stok
                </th>

                <th>
                    Stok Minimum
                </th>

                <th>
                    Lokasi Rak
                </th>

                <th>
                    Status
                </th>

            </tr>

        </thead>


        <tbody>

            @forelse($data as $item)

                @php

                    if ($item->stok == 0) {

                        $statusStok = 'Habis';

                    } elseif (
                        $item->stok <= $item->stok_minimum
                    ) {

                        $statusStok = 'Menipis';

                    } else {

                        $statusStok = 'Aman';

                    }

                @endphp


                <tr>

                    <td class="text-center">
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        {{ $item->kode_barang }}
                    </td>

                    <td>
                        {{ $item->nama_barang }}
                    </td>

                    <td>
                        {{ $item->kategori?->nama_kategori ?? '-' }}
                    </td>

                    <td>
                        {{ $item->satuan?->nama_satuan ?? '-' }}
                    </td>

                    <td class="text-right">
                        {{ number_format($item->stok, 2, ',', '.') }}
                    </td>

                    <td class="text-right">
                        {{ number_format($item->stok_minimum, 2, ',', '.') }}
                    </td>

                    <td>
                        {{ $item->lokasi_rak ?? '-' }}
                    </td>

                    <td class="status">
                        {{ $statusStok }}
                    </td>

                </tr>


            @empty

                <tr>

                    <td
                        colspan="9"
                        class="text-center">

                        Tidak ada data stok.

                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>


    <div class="footer">

        CV Cahaya Khanza Plastik —
        Laporan Stok Barang

    </div>


</body>

</html>