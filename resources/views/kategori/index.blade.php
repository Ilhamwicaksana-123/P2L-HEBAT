<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Produk</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-body">
    <main class="admin-main" style="max-width: 900px; margin: 0 auto;">
        <header class="page-header">
            <div>
                <h1>Kategori Produk</h1>
                <p>Daftar kategori yang tersedia pada aplikasi.</p>
            </div>
            <a href="{{ route('beranda') }}" class="btn btn-secondary">Kembali ke beranda</a>
        </header>

        <div class="admin-card">
            <div class="table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nama kategori</th>
                            <th>Jumlah produk</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kategori as $item)
                            <tr>
                                <td>{{ $item->nama_kategori }}</td>
                                <td>{{ $item->produk_count }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="empty-state">Belum ada kategori tersedia.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
