@extends('admin.layouts.app')

@section('title', 'Laporan Transaksi')
@section('heading', 'Laporan Transaksi')
@section('subheading', 'Pantau rekap transaksi pesanan secara otomatis per bulan agar admin dan super admin lebih mudah memonitor omset dan progres penjualan.')
@section('header_actions')
    <a href="{{ route('admin.laporan.print', ['tahun' => $selectedYear, 'bulan' => $selectedMonth]) }}" class="btn btn-secondary" target="_blank" rel="noopener">Cetak</a>
    <a href="{{ route('admin.laporan.export-pdf', ['tahun' => $selectedYear, 'bulan' => $selectedMonth]) }}" class="btn btn-primary">Download PDF</a>
@endsection

@section('content')
<div class="admin-grid admin-grid-cards">
    <div class="admin-card stat-card">
        <span class="stat-label">Tahun laporan</span>
        <strong class="stat-value">{{ $selectedYear }}</strong>
    </div>
    <div class="admin-card stat-card">
        <span class="stat-label">Total transaksi</span>
        <strong class="stat-value">{{ number_format($summary['total_transaksi'], 0, ',', '.') }}</strong>
    </div>
    <div class="admin-card stat-card">
        <span class="stat-label">Total omset</span>
        <strong class="stat-value">Rp {{ number_format($summary['total_omzet'], 0, ',', '.') }}</strong>
    </div>
    <div class="admin-card stat-card">
        <span class="stat-label">Pesanan selesai</span>
        <strong class="stat-value">{{ number_format($summary['selesai_count'], 0, ',', '.') }}</strong>
    </div>
    <div class="admin-card stat-card">
        <span class="stat-label">Pesanan batal</span>
        <strong class="stat-value">{{ number_format($summary['batal_count'], 0, ',', '.') }}</strong>
    </div>
</div>

<div class="admin-card">
    <div class="section-head">
        <div>
            <h2>Rekap Bulanan</h2>
            <p>Data di bawah ini direkap otomatis dari transaksi pesanan setiap bulan.</p>
        </div>
    </div>

    <form method="GET" class="toolbar">
        <select name="tahun">
            @forelse($availableYears as $year)
                <option value="{{ $year }}" @selected($selectedYear === $year)>{{ $year }}</option>
            @empty
                <option value="{{ $selectedYear }}">{{ $selectedYear }}</option>
            @endforelse
        </select>
        <select name="bulan">
            <option value="">Semua bulan</option>
            @foreach($monthLabels as $month => $label)
                <option value="{{ $month }}" @selected($selectedMonth === $month)>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-secondary">Tampilkan</button>
    </form>

    <div class="table-wrap">
        <table class="admin-table">
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
                @forelse($monthlyReports as $report)
                    <tr>
                        <td><strong>{{ $report->month_label }}</strong></td>
                        <td>{{ number_format($report->total_transaksi, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($report->total_omzet, 0, ',', '.') }}</td>
                        <td>{{ number_format($report->menunggu_count, 0, ',', '.') }}</td>
                        <td>{{ number_format($report->diproses_count, 0, ',', '.') }}</td>
                        <td>{{ number_format($report->dikirim_count, 0, ',', '.') }}</td>
                        <td>{{ number_format($report->selesai_count, 0, ',', '.') }}</td>
                        <td>{{ number_format($report->batal_count, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="empty-state">Belum ada data laporan transaksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($selectedMonth !== null)
    <div class="admin-card">
        <div class="section-head">
            <div>
                <h2>Detail Transaksi {{ $monthLabels[$selectedMonth] }} {{ $selectedYear }}</h2>
                <p>Daftar pesanan yang terjadi pada bulan yang dipilih.</p>
            </div>
        </div>

        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Kode Pesanan</th>
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
                            <td><strong>{{ $order->kode_pesanan }}</strong></td>
                            <td>{{ optional($order->created_at)->format('d M Y, H:i') }}</td>
                            <td>
                                <strong>{{ $order->user?->nama ?? '-' }}</strong>
                                <div class="table-meta-text">{{ $order->user?->email ?? '-' }}</div>
                            </td>
                            <td>
                                <strong>{{ $order->alamat?->nama_penerima ?? '-' }}</strong>
                                <div class="table-meta-text">{{ $order->alamat?->kota ?? '-' }}</div>
                            </td>
                            <td>{{ number_format($order->detail->sum('jumlah_barang'), 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</td>
                            <td>{{ $order->metode_pembayaran_label }}</td>
                            <td>
                                <span class="badge badge-status-{{ $order->status_css }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty-state">Belum ada transaksi pada bulan ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif

<div class="admin-card">
    <div class="section-head">
        <div>
            <h2>Ringkasan Status</h2>
            <p>Lihat distribusi status transaksi untuk tahun {{ $selectedYear }}.</p>
        </div>
    </div>

    <div class="admin-report-status-grid">
        <div class="admin-order-summary-card">
            <span>Menunggu pembayaran</span>
            <strong>{{ number_format($summary['menunggu_count'], 0, ',', '.') }} transaksi</strong>
        </div>
        <div class="admin-order-summary-card">
            <span>Diproses</span>
            <strong>{{ number_format($summary['diproses_count'], 0, ',', '.') }} transaksi</strong>
        </div>
        <div class="admin-order-summary-card">
            <span>Dikirim</span>
            <strong>{{ number_format($summary['dikirim_count'], 0, ',', '.') }} transaksi</strong>
        </div>
        <div class="admin-order-summary-card">
            <span>Selesai</span>
            <strong>{{ number_format($summary['selesai_count'], 0, ',', '.') }} transaksi</strong>
        </div>
        <div class="admin-order-summary-card">
            <span>Batal</span>
            <strong>{{ number_format($summary['batal_count'], 0, ',', '.') }} transaksi</strong>
        </div>
    </div>
</div>
@endsection
