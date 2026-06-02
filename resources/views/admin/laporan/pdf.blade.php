<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi {{ $selectedYear }}</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #111827;
            font-size: 12px;
            margin: 24px;
        }

        h1 {
            font-size: 22px;
            margin: 0 0 4px;
        }

        p {
            margin: 0 0 16px;
            color: #4b5563;
        }

        .summary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        .summary td {
            border: 1px solid #d1d5db;
            padding: 10px;
            width: 50%;
        }

        .summary span {
            display: block;
            color: #6b7280;
            font-size: 11px;
            margin-bottom: 4px;
        }

        .summary strong {
            font-size: 16px;
        }

        table.report {
            width: 100%;
            border-collapse: collapse;
        }

        .report th,
        .report td {
            border: 1px solid #d1d5db;
            padding: 8px;
            text-align: left;
        }

        .report th {
            background: #f3f4f6;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <h1>Laporan Transaksi</h1>
    <p>
        Periode:
        @if($selectedMonth !== null)
            {{ $monthLabels[$selectedMonth] }} {{ $selectedYear }}
        @else
            Tahun {{ $selectedYear }}
        @endif
    </p>

    <table class="summary">
        <tr>
            <td>
                <span>Total transaksi</span>
                <strong>{{ number_format($summary['total_transaksi'], 0, ',', '.') }}</strong>
            </td>
            <td>
                <span>Total omset</span>
                <strong>Rp {{ number_format($summary['total_omzet'], 0, ',', '.') }}</strong>
            </td>
        </tr>
    </table>

    @if($selectedMonth !== null)
        <h2>Detail Transaksi {{ $monthLabels[$selectedMonth] }} {{ $selectedYear }}</h2>
        <table class="report">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Tanggal</th>
                    <th>Pelanggan</th>
                    <th>Penerima</th>
                    <th class="text-right">Item</th>
                    <th class="text-right">Total</th>
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
                        <td class="text-right">{{ number_format($order->detail->sum('jumlah_barang'), 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</td>
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
        <table class="report">
            <thead>
                <tr>
                    <th>Bulan</th>
                    <th class="text-right">Total transaksi</th>
                    <th class="text-right">Omset</th>
                    <th class="text-right">Menunggu</th>
                    <th class="text-right">Diproses</th>
                    <th class="text-right">Dikirim</th>
                    <th class="text-right">Selesai</th>
                    <th class="text-right">Batal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlyReports as $report)
                    <tr>
                        <td>{{ $report->month_label }}</td>
                        <td class="text-right">{{ number_format($report->total_transaksi, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($report->total_omzet, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($report->menunggu_count, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($report->diproses_count, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($report->dikirim_count, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($report->selesai_count, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($report->batal_count, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
