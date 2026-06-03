@csrf
<div class="form-grid">
    <label class="profile-photo-field">
        <span>Foto profil {{ $mode === 'edit' ? '(opsional)' : '' }}</span>
        <input
            type="file"
            name="photo"
            accept="image/*"
            data-image-input
            data-preview-target="adminUserPhotoPreview"
            data-file-label="adminUserPhotoLabel"
        >
        <div class="profile-photo-field-body">
            <img
                id="adminUserPhotoPreview"
                src="{{ $user->photo_url }}"
                alt="{{ $user->nama ?: 'Foto profil' }}"
                class="image-preview"
                data-default-src="{{ $user->photo_url }}"
            >
            <small id="adminUserPhotoLabel" class="upload-file-label">
                {{ $mode === 'edit' ? 'Pilih foto baru hanya jika ingin mengganti foto profil pengguna.' : 'Belum ada foto baru dipilih.' }}
            </small>
        </div>
    </label>
    <label>
        <span>Nama</span>
        <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" required>
    </label>
    <label>
        <span>Email</span>
        <input type="email" name="email" value="{{ old('email', $user->email) }}" maxlength="30" required>
    </label>
    <label>
        <span>No HP</span>
        <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" required>
    </label>
    <label>
        <span>Role</span>
        <select name="role" required>
            @foreach(['super_admin' => 'Super Admin', 'admin' => 'Admin', 'user' => 'User'] as $value => $label)
                <option value="{{ $value }}" @selected(old('role', $user->role ?: 'user') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </label>
    <label>
        <span>Password {{ $mode === 'edit' ? '(opsional)' : '' }}</span>
        <input type="password" name="password" {{ $mode === 'create' ? 'required' : '' }}>
        @if($mode === 'edit')
            <small class="upload-file-label">Kosongkan jika tidak ingin mengganti password pengguna.</small>
        @endif
    </label>
</div>
<div class="form-actions">
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Kembali</a>
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
            label.textContent = '{{ $mode === 'edit' ? 'Pilih foto baru hanya jika ingin mengganti foto profil pengguna.' : 'Belum ada foto baru dipilih.' }}';
            return;
        }

        preview.src = URL.createObjectURL(file);
        label.textContent = `Preview siap disimpan: ${file.name}`;
    });
});
</script>
