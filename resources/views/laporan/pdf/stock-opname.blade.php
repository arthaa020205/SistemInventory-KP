<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <title>
        Laporan Stock Opname
    </title>

    @include('laporan.pdf._style')

</head>


<body>

    @include('laporan.pdf._header')


    <div class="report-title">
        LAPORAN STOCK OPNAME
    </div>

    <div class="report-subtitle">
        Hasil pemeriksaan dan penyesuaian persediaan barang
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
                    Total Stock Opname
                </div>

                <div class="summary-value">
                    {{ $totalOpname }}
                </div>

            </td>


            <td>

                <div class="summary-label">
                    Total Selisih
                </div>

                <div class="summary-value">
                    {{ $totalSelisih }}
                </div>

            </td>

        </tr>

    </table>


    {{-- DATA --}}

    <table class="data-table">

        <thead>

            <tr>

                <th>No</th>

                <th>Kode</th>

                <th>Tanggal</th>

                <th>Barang</th>

                <th>Stok Sistem</th>

                <th>Stok Fisik</th>

                <th>Selisih</th>

                <th>Status</th>

                <th>Petugas</th>

            </tr>

        </thead>


        <tbody>

            @forelse($data as $item)

                <tr>

                    <td class="text-center">
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        {{ $item->kode_transaksi }}
                    </td>

                    <td class="text-center">
                        {{ $item->tanggal_opname->format('d-m-Y') }}
                    </td>

                    <td>
                        {{ $item->barang->nama_barang }}
                    </td>

                    <td class="text-right">
                        {{ $item->stok_sistem }}
                    </td>

                    <td class="text-right">
                        {{ $item->stok_fisik }}
                    </td>

                    <td class="text-right">
                        {{ $item->selisih }}
                    </td>

                    <td class="status">
                        {{ $item->status }}
                    </td>

                    <td>
                        {{ $item->user->name }}
                    </td>

                </tr>


            @empty

                <tr>

                    <td
                        colspan="9"
                        class="text-center">

                        Tidak ada data.

                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>


    <div class="footer">

        CV Cahaya Khanza Plastik —
        Laporan Stock Opname

    </div>


</body>

</html>