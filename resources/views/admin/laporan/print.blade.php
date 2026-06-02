<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Transaksi {{ $selectedYear }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #1f2937;
            margin: 32px;
        }

        h1, h2 {
            margin: 0 0 8px;
        }

        p {
            margin: 0 0 16px;
            color: #4b5563;
        }

        .summary {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin: 24px 0;
        }

        .summary-card {
            border: 1px solid #d1d5db;
            border-radius: 12px;
            padding: 14px;
        }

        .summary-card span {
            display: block;
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 6px;
        }

        .summary-card strong {
            font-size: 20px;
            color: #111827;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th, td {
            border: 1px solid #d1d5db;
            padding: 10px 12px;
            text-align: left;
            font-size: 14px;
        }

        th {
            background: #f3f4f6;
        }

        @media print {
            body {
                margin: 16px;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <h1>Laporan Transaksi</h1>
    <p>
        Periode:
        @if($selectedMonth !== null)
            {{ $monthLabels[$selectedMonth] }} {{ $selectedYear }}
        @else
            Tahun {{ $selectedYear }}
        @endif
    </p>

    <div class="summary">
        <div class="summary-card">
            <span>Total transaksi</span>
            <strong>{{ number_format($summary['total_transaksi'], 0, ',', '.') }}</strong>
        </div>
        <div class="summary-card">
            <span>Total omset</span>
            <strong>Rp {{ number_format($summary['total_omzet'], 0, ',', '.') }}</strong>
        </div>
    </div>

    @if($selectedMonth !== null)
        <h2>Detail Transaksi {{ $monthLabels[$selectedMonth] }} {{ $selectedYear }}</h2>
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Tanggal</th>
                    <th>Pelanggan</th>
                    <th>Penerima</th>
                    <th>Item</th>
                    <th>Total</th>
                    <th>Metode</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($monthlyOrders as $order)
                    <tr>
                        <td>{{ $order->kode_pesanan }}</td>
                        <td>{{ optional($order->created_at)->format('d M Y, H:i') }}</td>
                        <td>{{ $order->user?->nama ?? '-' }}</td>
                        <td>{{ $order->alamat?->nama_penerima ?? '-' }}</td>
                        <td>{{ number_format($order->detail->sum('jumlah_barang'), 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</td>
                        <td>{{ $order->metode_pembayaran_label }}</td>
                        <td>{{ $order->status_label }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">Belum ada transaksi pada bulan ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @else
        <h2>Rekap Bulanan</h2>
        <table>
            <thead>
                <tr>
                    <th>Bulan</th>
                    <th>Total transaksi</th>
                    <th>Omset</th>
                    <th>Menunggu</th>
                    <th>Diproses</th>
                    <th>Dikirim</th>
                    <th>Selesai</th>
                    <th>Batal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlyReports as $report)
                    <tr>
                        <td>{{ $report->month_label }}</td>
                        <td>{{ number_format($report->total_transaksi, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($report->total_omzet, 0, ',', '.') }}</td>
                        <td>{{ number_format($report->menunggu_count, 0, ',', '.') }}</td>
                        <td>{{ number_format($report->diproses_count, 0, ',', '.') }}</td>
                        <td>{{ number_format($report->dikirim_count, 0, ',', '.') }}</td>
                        <td>{{ number_format($report->selesai_count, 0, ',', '.') }}</td>
                        <td>{{ number_format($report->batal_count, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
