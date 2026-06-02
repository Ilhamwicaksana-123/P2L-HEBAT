@extends('admin.layouts.app')

@section('title', 'Detail Pesanan')
@section('hide_admin_header', true)

@section('content')
<div class="admin-grid">
    <div class="admin-card">
        <div class="section-head">
            <div>
                <h2>Ringkasan Pesanan</h2>
                <p>Informasi utama pesanan dan status transaksi pelanggan.</p>
            </div>
            <div style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center; justify-content: flex-end;">
                <span class="badge badge-status-{{ $order->status_css }}">{{ $order->status_label }}</span>
                <a href="{{ route('admin.pesanan.index') }}" class="btn btn-secondary">Kembali ke Pesanan</a>
            </div>
        </div>

        <div class="admin-order-summary">
            <div class="admin-order-summary-card">
                <span>Kode Pesanan</span>
                <strong>{{ $order->kode_pesanan }}</strong>
            </div>
            <div class="admin-order-summary-card">
                <span>Metode Pembayaran</span>
                <strong>{{ $order->metode_pembayaran_label }}</strong>
            </div>
            <div class="admin-order-summary-card">
                <span>Total Bayar</span>
                <strong>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</strong>
            </div>
            <div class="admin-order-summary-card">
                <span>Waktu Pesanan</span>
                <strong>{{ optional($order->created_at)->format('d M Y, H:i') }}</strong>
            </div>
        </div>

        <div class="admin-order-detail-list">
            @foreach($order->detail as $detail)
                <div class="admin-order-detail-item">
                    <div>
                        <h3>{{ $detail->nama_produk }}</h3>
                        <p>{{ $detail->qty }} {{ strtolower($detail->satuan_label) }} x Rp {{ number_format($detail->harga_produk, 0, ',', '.') }} / {{ strtolower($detail->satuan_label) }}</p>
                    </div>
                    <strong>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</strong>
                </div>
            @endforeach
        </div>
    </div>

    <div class="admin-card">
        <div class="section-head">
            <div>
                <h2>Konfirmasi Status Pesanan</h2>
                <p>Admin dan super admin dapat memperbarui progres pesanan pelanggan dari panel ini.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.pesanan.update-status', ['pesanan' => $order]) }}">
            @csrf
            @method('PATCH')

            <div class="form-grid">
                <label class="admin-order-status-field">
                    Status Pesanan
                    <select name="status_pesanan" required>
                        @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('status_pesanan', $order->status_pesanan) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
            </div>

            <div style="margin-top: 18px; display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
                <button type="submit" class="btn btn-primary">Simpan Status</button>
                <span class="badge badge-status-{{ $order->status_css }}">Status saat ini: {{ $order->status_label }}</span>
            </div>
        </form>
    </div>
</div>
@endsection
