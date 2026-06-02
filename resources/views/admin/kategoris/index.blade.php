@extends('admin.layouts.app')

@section('title', 'Data Kategori')
@section('heading', 'Data Kategori')
@section('subheading', 'Kelola kategori Pekarangan Pangan Lestari agar produk lebih mudah dikelompokkan dan ditampilkan dengan rapi di katalog.')
@section('hide_admin_error_flash', true)
@section('header_actions')
    <a href="{{ route('admin.kategoris.create') }}" class="btn btn-primary">Tambah kategori</a>
@endsection

@section('content')
<div class="admin-card">
    <form method="GET" class="toolbar">
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama kategori">
        <button type="submit" class="btn btn-secondary">Cari</button>
    </form>

    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Nama kategori</th>
                    <th>Jumlah produk</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kategoris as $kategori)
                    <tr>
                        <td>{{ $kategori->nama_kategori }}</td>
                        <td>{{ $kategori->produk_count }}</td>
                        <td>{{ optional($kategori->created_at)->format('d M Y H:i') }}</td>
                        <td class="actions-cell">
                            <a href="{{ route('admin.kategoris.edit', $kategori) }}" class="btn btn-small btn-secondary">Edit</a>
                            <form method="POST" action="{{ route('admin.kategoris.destroy', $kategori) }}" id="delete-kategori-{{ $kategori->id_kategori }}">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="button"
                                    class="btn btn-small btn-danger"
                                    data-action-trigger
                                    data-action-target="delete-kategori-{{ $kategori->id_kategori }}"
                                    data-action-kicker="Hapus Kategori"
                                    data-action-title="Hapus kategori ini?"
                                    data-action-text="Kategori akan dihapus dari daftar. Pastikan tidak ada data penting yang masih bergantung pada kategori ini."
                                    data-action-confirm="Ya, Hapus"
                                >
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="empty-state">Belum ada data kategori.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrap">
        {{ $kategoris->links() }}
    </div>
</div>

@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.dispatchEvent(new CustomEvent('action-modal:open', {
                detail: {
                    kicker: 'Kategori Tidak Dihapus',
                    title: 'Kategori ini masih dipakai produk',
                    text: 'Kategori tidak bisa dihapus karena masih ada produk yang terhubung. Pindahkan atau hapus produk terkait terlebih dahulu.',
                    confirmLabel: 'Tutup',
                    hideCancel: true,
                },
            }));
        });
    </script>
@endif
@endsection
