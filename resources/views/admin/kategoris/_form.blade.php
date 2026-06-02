@csrf
<div class="form-grid">
    <label>
        <span>Nama kategori</span>
        <input type="text" name="nama_kategori" value="{{ old('nama_kategori', $kategori->nama_kategori) }}" required>
    </label>
</div>
<div class="form-actions">
    <a href="{{ route('admin.kategoris.index') }}" class="btn btn-secondary">Kembali</a>
    <button type="submit" class="btn btn-primary">Simpan</button>
</div>
