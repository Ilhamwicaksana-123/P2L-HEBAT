@extends('admin.layouts.app')

@section('title', 'Data Pesanan')
@section('heading', 'Data Pesanan')
@section('subheading', 'Pantau pesanan pelanggan untuk produk hasil Pekarangan Pangan Lestari, total transaksi, metode pembayaran, dan status proses pemenuhan pesanan dari satu halaman admin.')

@section('content')
<div class="admin-card">
    <form method="GET" class="toolbar" data-auto-filter-form>
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari kode pesanan, pelanggan, email, penerima, atau kota">
        <select name="status" data-auto-submit>
            <option value="">Semua status</option>
            @foreach($statusOptions as $value => $label)
                <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <input type="date" name="date_from" value="{{ $dateFrom }}" aria-label="Dari tanggal" data-auto-submit>
        <input type="date" name="date_to" value="{{ $dateTo }}" aria-label="Sampai tanggal" data-auto-submit>
        <button type="submit" class="btn btn-secondary">Filter</button>
        @if($search !== '' || $status !== '' || $dateFrom !== '' || $dateTo !== '')
            <a href="{{ route('admin.pesanan.index') }}" class="btn btn-secondary">Reset</a>
        @endif
    </form>

    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Kode Pesanan</th>
                    <th>Pelanggan</th>
                    <th>Penerima</th>
                    <th>Total</th>
                    <th>Metode</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>
                            <strong>{{ $order->kode_pesanan }}</strong>
                            <div class="table-meta-text">{{ optional($order->created_at)->format('d M Y, H:i') }}</div>
                        </td>
                        <td>
                            <strong>{{ $order->user?->nama ?? '-' }}</strong>
                            <div class="table-meta-text">{{ $order->user?->email ?? '-' }}</div>
                        </td>
                        <td>
                            <strong>{{ $order->alamat?->nama_penerima ?? '-' }}</strong>
                            <div class="table-meta-text">{{ $order->alamat?->kota ?? '-' }}</div>
                        </td>
                        <td>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</td>
                        <td>{{ $order->metode_pembayaran_label }}</td>
                        <td>
                            <span class="badge badge-status-{{ $order->status_css }}">
                                {{ $order->status_label }}
                            </span>
                        </td>
                        <td class="actions-cell">
                            <a href="{{ route('admin.pesanan.show', $order) }}" class="btn btn-small btn-secondary">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-state">Belum ada data pesanan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrap">
        {{ $orders->links() }}
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('[data-auto-filter-form]');

    if (!form) {
        return;
    }

    form.querySelectorAll('[data-auto-submit]').forEach((field) => {
        field.addEventListener('change', () => {
            form.requestSubmit();
        });
    });
});
</script>
@endsection
