@extends('admin.layouts.app')

@section('title', 'Metode Pembayaran')
@section('heading', 'Metode Pembayaran')
@section('subheading', 'Aktifkan atau nonaktifkan pilihan pembayaran yang tersedia di halaman checkout pelanggan.')
@section('hide_admin_success_flash', true)

@section('content')
<div class="admin-card payment-settings-card">
    <div class="section-head">
        <div>
            <h2>Pengaturan Checkout</h2>
            <p>Metode yang nonaktif tidak akan muncul sebagai pilihan saat pelanggan membuat pesanan baru.</p>
        </div>
    </div>

    <form action="{{ route('admin.payment-methods.update') }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="payment-settings-grid">
            @foreach($paymentMethods as $method)
                <label class="payment-settings-option {{ $method->is_active ? 'is-active' : '' }}">
                    <input
                        type="checkbox"
                        name="methods[]"
                        value="{{ $method->code }}"
                        {{ $method->is_active ? 'checked' : '' }}
                    >
                    <span class="payment-settings-icon">
                        @if($method->code === \App\Models\Pesanan::METODE_TRANSFER)
                            <i class="fa-solid fa-building-columns"></i>
                        @elseif($method->code === \App\Models\Pesanan::METODE_E_WALLET)
                            <i class="fa-solid fa-wallet"></i>
                        @else
                            <i class="fa-solid fa-hand-holding-dollar"></i>
                        @endif
                    </span>
                    <span class="payment-settings-copy">
                        <strong>{{ $method->name }}</strong>
                        <small>{{ $method->description }}</small>
                        <em>{{ $method->is_active ? 'Aktif' : 'Nonaktif' }}</em>
                    </span>
                </label>
            @endforeach
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                Simpan
            </button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
</div>
@endsection
