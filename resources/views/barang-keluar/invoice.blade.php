<!DOCTYPE html>

<html lang="id">

<head>

<meta charset="UTF-8">

<title>
    Faktur {{ $barangKeluar->kode_transaksi }}
</title>

<style>

    * {
        box-sizing: border-box;
    }

    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
        color: #222;
        margin: 0;
        padding: 25px 35px;
        background: #fff;
    }

    .invoice-container {
        width: 100%;
        max-width: 900px;
        margin: 0 auto;
    }

    /* =========================================================
       HEADER
    ========================================================= */

    .header {
        width: 100%;
        border-bottom: 3px solid #222;
        padding-bottom: 15px;
        margin-bottom: 18px;
    }

    .header-table {
        width: 100%;
        border-collapse: collapse;
    }

    .logo-cell {
        width: 110px;
        vertical-align: middle;
    }

    .logo {
        width: 90px;
        height: 90px;
        object-fit: contain;
    }

    .company-cell {
        vertical-align: middle;
        padding-left: 10px;
    }

    .company-name {
        font-size: 21px;
        font-weight: bold;
        margin-bottom: 5px;
        letter-spacing: 0.3px;
    }

    .company-description {
        font-size: 12px;
        margin-bottom: 5px;
    }

    .company-address {
        font-size: 11px;
        color: #555;
        line-height: 1.5;
    }

    .invoice-cell {
        width: 250px;
        vertical-align: middle;
        text-align: right;
    }

    .invoice-label {
        font-size: 24px;
        font-weight: bold;
        letter-spacing: 2px;
        margin-bottom: 10px;
    }

    .invoice-number-box {
        display: inline-block;
        border: 1px solid #555;
        padding: 7px 12px;
        min-width: 190px;
        text-align: left;
    }

    .invoice-number-label {
        font-size: 10px;
        color: #666;
        margin-bottom: 2px;
    }

    .invoice-number {
        font-size: 14px;
        font-weight: bold;
    }

    /* =========================================================
       CUSTOMER INFO
    ========================================================= */

    .info-section {
        margin-bottom: 18px;
    }

    .info-table {
        width: 100%;
        border-collapse: collapse;
    }

    .info-table td {
        padding: 4px 3px;
        vertical-align: top;
    }

    .info-label {
        width: 105px;
        font-weight: bold;
    }

    .info-separator {
        width: 10px;
    }

    /* =========================================================
       ITEM TABLE
    ========================================================= */

    .items {
        width: 100%;
        border-collapse: collapse;
        margin-top: 5px;
    }

    .items thead th {
        background: #eeeeee;
        border: 1px solid #888;
        padding: 9px 7px;
        text-align: center;
        font-size: 11px;
    }

    .items tbody td {
        border: 1px solid #999;
        padding: 8px 7px;
        vertical-align: middle;
    }

    .items tbody tr:nth-child(even) {
        background: #fafafa;
    }

    .text-center {
        text-align: center;
    }

    .text-right {
        text-align: right;
    }

    /* =========================================================
       TOTAL
    ========================================================= */

    .total-wrapper {
        width: 100%;
        margin-top: 15px;
    }

    .total-table {
        width: 330px;
        margin-left: auto;
        border-collapse: collapse;
    }

    .total-table td {
        padding: 7px 8px;
    }

    .total-label {
        font-weight: bold;
        text-align: right;
    }

    .total-value {
        text-align: right;
        font-size: 15px;
        font-weight: bold;
    }

    .grand-total {
        border-top: 2px solid #222;
        border-bottom: 2px solid #222;
    }

    /* =========================================================
       NOTE
    ========================================================= */

    .note {
        margin-top: 20px;
        padding: 10px 12px;
        border: 1px solid #ddd;
        background: #fafafa;
    }

    .note-title {
        font-weight: bold;
        margin-bottom: 4px;
    }

    /* =========================================================
       SIGNATURE
    ========================================================= */

    .signature-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 45px;
    }

    .signature-left {
        width: 65%;
    }

    .signature-right {
        width: 35%;
        text-align: center;
    }

    .signature-space {
        height: 55px;
    }

    .signature-name {
        font-weight: bold;
        border-top: 1px solid #555;
        padding-top: 5px;
    }

    /* =========================================================
       FOOTER
    ========================================================= */

    .footer {
        margin-top: 35px;
        padding-top: 10px;
        border-top: 1px solid #ddd;
        text-align: center;
        font-size: 10px;
        color: #777;
    }

    /* =========================================================
       PRINT
    ========================================================= */

    .no-print {
        text-align: center;
        margin-top: 25px;
    }

    .print-button {
        padding: 9px 18px;
        border: 0;
        background: #28a745;
        color: #fff;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
    }

    @media print {

        body {
            padding: 10px 15px;
        }

        .no-print {
            display: none;
        }

        .invoice-container {
            max-width: none;
        }

    }

</style>

</head>

<body>

<div class="invoice-container">

{{-- ========================================================= --}}
{{-- HEADER --}}
{{-- ========================================================= --}}

<div class="header">

    <table class="header-table">

        <tr>

            {{-- LOGO --}}

            <td class="logo-cell">

                <img
                    src="{{ asset('images/logo.png') }}"
                    class="logo"
                    alt="Logo CV Cahaya Khanza Plastik"
                >

            </td>


            {{-- IDENTITAS PERUSAHAAN --}}

            <td class="company-cell">

                <div class="company-name">
                    CV CAHAYA KHANZA PLASTIK
                </div>

                <div class="company-description">
                    Penjualan Produk Plastik &amp; Bahan Kue
                </div>

                <div class="company-address">
                    JL Raya Cipanas No. 192, Cianjur, Jawa Barat
                </div>

            </td>


            {{-- FAKTUR --}}

            <td class="invoice-cell">

                <div class="invoice-label">
                    FAKTUR
                </div>

                <div class="invoice-number-box">

                    <div class="invoice-number-label">
                        NO. FAKTUR
                    </div>

                    <div class="invoice-number">
                        {{ $barangKeluar->kode_transaksi }}
                    </div>

                </div>

            </td>

        </tr>

    </table>

</div>



{{-- ========================================================= --}}
{{-- INFORMASI TRANSAKSI --}}
{{-- ========================================================= --}}

<div class="info-section">

    <table class="info-table">

        <tr>

            <td class="info-label">
                Pelanggan
            </td>

            <td class="info-separator">
                :
            </td>

            <td>
                {{ $barangKeluar->pelanggan->nama_pelanggan ?? '-' }}
            </td>


            <td class="info-label">
                Tanggal
            </td>

            <td class="info-separator">
                :
            </td>

            <td>
                {{ \Carbon\Carbon::parse($barangKeluar->tanggal_keluar)->format('d-m-Y') }}
            </td>

        </tr>


        <tr>

            <td class="info-label">
                Tujuan
            </td>

            <td class="info-separator">
                :
            </td>

            <td>
                {{ $barangKeluar->tujuan ?: '-' }}
            </td>


            <td class="info-label">
                Petugas
            </td>

            <td class="info-separator">
                :
            </td>

            <td>
                {{ $barangKeluar->user->name ?? '-' }}
            </td>

        </tr>

    </table>

</div>



{{-- ========================================================= --}}
{{-- DETAIL BARANG --}}
{{-- ========================================================= --}}

<table class="items">

    <thead>

        <tr>

            <th width="40">
                No
            </th>

            <th width="95">
                Kode Barang
            </th>

            <th>
                Nama Barang
            </th>

            <th width="100">
                Jumlah
            </th>

            <th width="125">
                Harga / Satuan
            </th>

            <th width="135">
                Subtotal
            </th>

        </tr>

    </thead>


    <tbody>

        @forelse($barangKeluar->details as $index => $detail)

            <tr>

                <td class="text-center">
                    {{ $index + 1 }}
                </td>


                <td>
                    {{ $detail->barang->kode_barang ?? '-' }}
                </td>


                <td>
                    {{ $detail->barang->nama_barang ?? '-' }}
                </td>


                <td class="text-center">

                    {{ rtrim(rtrim(number_format($detail->jumlah ?? 0, 2, ',', '.'), '0'), ',') }}

                    {{ $detail->satuan->nama_satuan ?? '-' }}

                </td>


                <td class="text-right">

                    Rp
                    {{ number_format($detail->harga_jual ?? 0, 0, ',', '.') }}

                </td>


                <td class="text-right">

                    <strong>

                        Rp
                        {{ number_format($detail->subtotal ?? 0, 0, ',', '.') }}

                    </strong>

                </td>

            </tr>

        @empty

            <tr>

                <td colspan="6"
                    class="text-center">

                    Tidak ada detail barang.

                </td>

            </tr>

        @endforelse

    </tbody>

</table>



{{-- ========================================================= --}}
{{-- TOTAL --}}
{{-- ========================================================= --}}

<div class="total-wrapper">

    <table class="total-table">

        <tr class="grand-total">

            <td class="total-label">

                TOTAL

            </td>

            <td class="total-value">

                Rp
                {{ number_format($barangKeluar->details->sum('subtotal'), 0, ',', '.') }}

            </td>

        </tr>

    </table>

</div>



{{-- ========================================================= --}}
{{-- KETERANGAN --}}
{{-- ========================================================= --}}

@if($barangKeluar->keterangan)

    <div class="note">

        <div class="note-title">
            Keterangan
        </div>

        <div>
            {{ $barangKeluar->keterangan }}
        </div>

    </div>

@endif



{{-- ========================================================= --}}
{{-- TANDA TANGAN --}}
{{-- ========================================================= --}}

<table class="signature-table">

    <tr>

        <td class="signature-left">
        </td>


        <td class="signature-right">

            Cianjur,
            {{ \Carbon\Carbon::parse($barangKeluar->tanggal_keluar)->format('d-m-Y') }}


            <div class="signature-space"></div>


            <div class="signature-name">

                {{ $barangKeluar->user->name ?? 'Petugas Gudang' }}

            </div>

        </td>

    </tr>

</table>



{{-- ========================================================= --}}
{{-- FOOTER --}}
{{-- ========================================================= --}}

<div class="footer">

    Dokumen ini merupakan bukti transaksi penjualan
    CV Cahaya Khanza Plastik.

</div>



{{-- ========================================================= --}}
{{-- PRINT --}}
{{-- ========================================================= --}}

<div class="no-print">

    <button
        type="button"
        class="print-button"
        onclick="window.print()"
    >

        Cetak Faktur

    </button>

</div>
```

</div>

</body>

</html>
