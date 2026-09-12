<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Laporan Barang Keluar</title>

@include('laporan.pdf._style')

</head>

<body>

{{-- HEADER PERUSAHAAN --}}

@include('laporan.pdf._header')


{{-- JUDUL --}}

<div class="report-title">

    LAPORAN BARANG KELUAR

</div>

<div class="report-subtitle">

    Riwayat pengeluaran barang dari persediaan

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
                Total Barang Keluar
            </div>

            <div class="summary-value">
                {{ number_format($totalQty, 2, ',', '.') }}
            </div>

        </td>

    </tr>

</table>


{{-- DATA BARANG KELUAR --}}

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
                Qty
            </th>

            <th>
                Jenis Keluar
            </th>

            <th>
                Tujuan
            </th>

            <th>
                Petugas
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

                    {{ $item->tanggal_keluar->format('d-m-Y') }}

                </td>


                <td>

                    {{ $item->barang->nama_barang }}

                </td>


                <td class="text-right">

                    {{ $item->jumlah }}

                </td>


                <td>

                    {{ $item->jenis_keluar }}

                </td>


                <td>

                    {{ $item->tujuan ?: '-' }}

                </td>


                <td>

                    {{ $item->user->name }}

                </td>

            </tr>


        @empty

            <tr>

                <td
                    colspan="8"
                    class="text-center">

                    Tidak ada data barang keluar.

                </td>

            </tr>

        @endforelse

    </tbody>

</table>


<div class="footer">

    CV Cahaya Khanza Plastik —
    Laporan Barang Keluar

</div>

</body>

</html>
