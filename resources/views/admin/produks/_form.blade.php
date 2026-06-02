@csrf
<div class="product-form-layout">
    <label class="product-image-field">
        <span>Gambar produk {{ $mode === 'edit' ? '(opsional)' : '' }}</span>
        <img
            id="adminProdukPreview"
            src="{{ $produk->gambar_produk_url }}"
            alt="{{ $produk->nama_produk ?: 'Gambar produk' }}"
            class="image-preview product-image-preview"
            data-default-src="{{ $produk->gambar_produk_url }}"
        >
        <input
            type="file"
            name="gambar_produk"
            accept="image/*"
            {{ $mode === 'create' ? 'required' : '' }}
            data-image-input
            data-preview-target="adminProdukPreview"
            data-file-label="adminProdukPhotoLabel"
        >
        <small id="adminProdukPhotoLabel" class="upload-file-label">Belum ada gambar baru dipilih.</small>
    </label>

    <div class="product-fields-grid">
        <label>
            <span>Nama produk</span>
            <input type="text" name="nama_produk" value="{{ old('nama_produk', $produk->nama_produk) }}" required>
        </label>
        <label>
            <span>Harga produk</span>
            <input type="number" min="0" step="0.01" name="harga_produk" value="{{ old('harga_produk', $produk->harga_produk) }}" required>
        </label>
        <label>
            <span>Satuan</span>
            <select name="satuan" required>
                @foreach(\App\Models\Produk::SATUAN_OPTIONS as $value => $label)
                    <option value="{{ $value }}" @selected((string) old('satuan', $produk->satuan ?: \App\Models\Produk::SATUAN_KG) === (string) $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label>
            <span>Kategori</span>
            <select name="id_kategori" required>
                <option value="">Pilih kategori</option>
                @foreach($kategoris as $kategori)
                    <option value="{{ $kategori->id_kategori }}" @selected((string) old('id_kategori', $produk->id_kategori) === (string) $kategori->id_kategori)>{{ $kategori->nama_kategori }}</option>
                @endforeach
            </select>
        </label>
        <label class="checkbox-label product-status-field">
            <input type="checkbox" name="status_produk" value="1" @checked(old('status_produk', ($produk->status_produk ?? \App\Models\Produk::STATUS_AKTIF) === \App\Models\Produk::STATUS_AKTIF))>
            <span>Produk aktif</span>
        </label>
    </div>
</div>
<div class="form-actions">
    <a href="{{ route('admin.produks.index') }}" class="btn btn-secondary">Kembali</a>
    <button type="submit" class="btn btn-primary">Simpan</button>
</div>
<script>
document.querySelectorAll('[data-image-input]').forEach((input) => {
    input.addEventListener('change', (event) => {
        const preview = document.getElementById(input.dataset.previewTarget);
        const label = document.getElementById(input.dataset.fileLabel);
        const [file] = event.target.files || [];

        if (!preview || !label) {
            return;
        }

        if (!file) {
            preview.src = preview.dataset.defaultSrc;
            label.textContent = 'Belum ada gambar baru dipilih.';
            return;
        }

        preview.src = URL.createObjectURL(file);
        label.textContent = `Preview siap disimpan: ${file.name}`;
    });
});
</script>
