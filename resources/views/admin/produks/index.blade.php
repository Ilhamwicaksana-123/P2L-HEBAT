@extends('admin.layouts.app')

@section('title', 'Data Produk')
@section('heading', 'Data Produk')
@section('subheading', 'Kelola produk hasil Pekarangan Pangan Lestari, gambar produk, harga jual, satuan, kategori, dan status tampil di katalog pengguna.')
@section('header_actions')
    <a href="{{ route('admin.produks.create') }}" class="btn btn-primary">Tambah produk</a>
@endsection

@section('content')
<div class="admin-card">
    <form method="GET" class="toolbar">
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari produk, harga, satuan, atau kategori">
        <button type="submit" class="btn btn-secondary">Cari</button>
    </form>

    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Satuan</th>
                    <th>Gambar</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produks as $produk)
                    <tr>
                        <td>{{ $produk->nama_produk }}</td>
                        <td>{{ $produk->kategori?->nama_kategori ?? '-' }}</td>
                        <td>Rp {{ number_format($produk->harga_produk, 0, ',', '.') }}</td>
                        <td>{{ $produk->satuan_label }}</td>
                        <td>
                            @if($produk->gambar_produk)
                                <img src="{{ $produk->gambar_produk_url }}" alt="{{ $produk->nama_produk }}" class="thumb-preview">
                            @else
                                <span class="text-muted">Tidak ada</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $produk->is_active ? 'badge-success' : 'badge-muted' }}">
                                {{ $produk->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="actions-cell">
                            <a href="{{ route('admin.produks.edit', $produk) }}" class="btn btn-small btn-secondary">Edit</a>
                            <form method="POST" action="{{ route('admin.produks.destroy', $produk) }}" id="delete-produk-{{ $produk->id_produk }}">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="button"
                                    class="btn btn-small btn-danger"
                                    data-action-trigger
                                    data-action-target="delete-produk-{{ $produk->id_produk }}"
                                    data-action-kicker="Hapus Produk"
                                    data-action-title="Hapus produk ini?"
                                    data-action-text="Produk akan dihapus dari data admin dan tidak lagi tampil di katalog jika sebelumnya aktif."
                                    data-action-confirm="Ya, Hapus"
                                >
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-state">Belum ada data produk.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrap">
        {{ $produks->links('admin.partials.pagination') }}
    </div>
</div>
@endsection
