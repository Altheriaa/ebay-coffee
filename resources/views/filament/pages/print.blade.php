<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bay Coffee - Laporan Keuangan ({{ $periodLabel }})</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.4;
            padding: 40px;
            max-width: 900px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #7d562d;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            color: #271310;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 14px;
            color: #666;
        }
        .meta-info {
            margin-bottom: 25px;
            font-size: 14px;
        }
        .meta-info table {
            width: 100%;
        }
        .meta-info td {
            padding: 3px 0;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #271310;
            border-bottom: 1px solid #7d562d;
            padding-bottom: 5px;
            margin-top: 30px;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        table.report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-bottom: 20px;
        }
        table.report-table th, table.report-table td {
            border: 1px solid #ddd;
            padding: 8px 12px;
            text-align: left;
        }
        table.report-table th {
            background-color: #f9f6f3;
            color: #271310;
            font-weight: bold;
        }
        .text-right {
            text-align: right !important;
        }
        .font-bold {
            font-weight: bold;
        }
        .total-row {
            background-color: #fafafa;
        }
        .laba-bersih {
            font-size: 15px;
            background-color: #f0f7f4;
        }
        .footer-sig {
            margin-top: 50px;
            float: right;
            width: 250px;
            text-align: center;
            font-size: 14px;
        }
        .footer-sig .space {
            height: 70px;
        }
        .footer-sig .line {
            border-bottom: 1px solid #333;
            margin-bottom: 5px;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        @media print {
            body {
                padding: 0;
                color: #000;
            }
            .no-print {
                display: none;
            }
            table.report-table th {
                background-color: #f0f0f0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .laba-bersih {
                background-color: #e8f5e9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <h1>Bay Coffee</h1>
        <p>Laporan Pemasukan & Pendapatan Penjualan</p>
    </div>

    {{-- Meta Info --}}
    <div class="meta-info">
        <table>
            <tr>
                <td width="15%" class="font-bold">Periode Laporan</td>
                <td width="35%">: {{ $periodLabel }}</td>
                <td width="15%" class="font-bold">Tanggal Cetak</td>
                <td width="35%">: {{ date('d M Y H:i') }}</td>
            </tr>
            <tr>
                <td class="font-bold">Dicetak Oleh</td>
                <td>: {{ auth()->user()->name }}</td>
                <td class="font-bold">Status Toko</td>
                <td>: Aktif</td>
            </tr>
        </table>
    </div>

    {{-- Ringkasan Laba Rugi --}}
    <div class="section-title">Ringkasan Keuangan Penjualan</div>
    <table class="report-table">
        <thead>
            <tr>
                <th>Uraian Keuangan</th>
                <th class="text-right" width="30%">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total Transaksi Penjualan Lunas</td>
                <td class="text-right font-bold">
                    {{ $orders->count() }} Transaksi
                </td>
            </tr>
            <tr class="laba-bersih font-bold">
                <td>Total Pendapatan / Pemasukan Bersih</td>
                <td class="text-right" style="color: #2e7d32;">
                    Rp. {{ number_format($totalPemasukan, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    {{-- Detail Pemasukan --}}
    <div class="section-title">Rincian Transaksi Pemasukan</div>
    <table class="report-table">
        <thead>
            <tr>
                <th width="15%">No. Invoice</th>
                <th width="15%">Tanggal</th>
                <th>Nama Pelanggan</th>
                <th>No. Telepon</th>
                <th width="15%">Metode</th>
                <th class="text-right" width="18%">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                <tr>
                    <td class="font-bold">{{ $order->invoice_number }}</td>
                    <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                    <td>{{ $order->customer->nama ?? '-' }}</td>
                    <td>{{ $order->phone }}</td>
                    <td>{{ $order->payment_method ?? 'Midtrans' }}</td>
                    <td class="text-right font-bold">Rp. {{ number_format($order->total_price, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #888;">Tidak ada data transaksi pemasukan pada periode ini.</td>
                </tr>
            @endforelse
            @if ($orders->count() > 0)
                <tr class="total-row font-bold">
                    <td colspan="5" class="text-right">Total Pendapatan Bersih:</td>
                    <td class="text-right">Rp. {{ number_format($totalPemasukan, 0, ',', '.') }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- Signature Section --}}
    <div class="clearfix">
        <div class="footer-sig">
            <p>Banda Aceh, {{ date('d M Y') }}</p>
            <p>Mengetahui,</p>
            <p class="font-bold">Administrator</p>
            <div class="space"></div>
            <div class="line"></div>
            <p class="font-bold">{{ auth()->user()->name }}</p>
        </div>
    </div>

    {{-- Print Script --}}
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>
