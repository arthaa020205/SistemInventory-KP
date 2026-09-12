<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Laporan Barang Masuk</title>

@include('laporan.pdf._style')

</head>

<body>

{{-- HEADER PERUSAHAAN --}}

@include('laporan.pdf._header')


{{-- JUDUL --}}

<div class="report-title">

    LAPORAN BARANG MASUK

</div>

<div class="report-subtitle">

    Riwayat penerimaan barang ke dalam persediaan

</div>


<div class="print-info">

    Dicetak :
    {{ now()->format('d-m-Y H:i') }}

</div>


{{-- RINGKASAN --}}

<table class="summary-table">

    <tr>

        <td>

            <div class="summary-label">
                Total Barang Masuk
            </div>

            <div class="summary-value">
                {{ number_format($totalQty, 2, ',', '.') }}
            </div>

        </td>


        <td>

            <div class="summary-label">
                Total Pembelian
            </div>

            <div class="summary-value">
                Rp {{ number_format($totalPembelian, 0, ',', '.') }}
            </div>

        </td>

    </tr>

</table>


{{-- DATA BARANG MASUK --}}

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
                Tanggal
            </th>

            <th>
                Barang
            </th>

            <th>
                Supplier
            </th>

            <th>
                Qty
            </th>

            <th>
                Harga
            </th>

            <th>
                Total
            </th>

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

                    {{ $item->tanggal_masuk->format('d-m-Y') }}

                </td>


                <td>

                    {{ $item->barang->nama_barang }}

                </td>


                <td>

                    {{ $item->supplier->nama_supplier }}

                </td>


                <td class="text-right">

                    {{ $item->jumlah }}

                </td>


                <td class="text-right">

                    Rp {{ number_format($item->harga_beli, 0, ',', '.') }}

                </td>


                <td class="text-right">

                    Rp
                    {{ number_format(
                        $item->jumlah * $item->harga_beli,
                        0,
                        ',',
                        '.'
                    ) }}

                </td>

            </tr>


        @empty

            <tr>

                <td
                    colspan="8"
                    class="text-center">

                    Tidak ada data barang masuk.

                </td>

            </tr>

        @endforelse

    </tbody>


    <tfoot>

        <tr>

            <th colspan="5" class="text-right">

                TOTAL

            </th>

            <th class="text-right">

                {{ number_format($totalQty, 2, ',', '.') }}

            </th>

            <th></th>

            <th class="text-right">

                Rp
                {{ number_format(
                    $totalPembelian,
                    0,
                    ',',
                    '.'
                ) }}

            </th>

        </tr>

    </tfoot>

</table>


<div class="footer">

    CV Cahaya Khanza Plastik —
    Laporan Barang Masuk

</div>

</body>

</html>
