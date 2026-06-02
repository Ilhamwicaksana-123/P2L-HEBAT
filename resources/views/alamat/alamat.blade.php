<section class="profil-address-section app-fade-up app-delay-2" id="alamat-section">
    <div class="profil-address-head">
        <div>
            <h2>Daftar Alamat</h2>
        </div>
    </div>

    <div class="profil-address-layout">
        <div class="profil-address-card profil-address-card-list">
            <div class="profil-address-card-head">
                <h3>Alamat Tersimpan</h3>
                <p>{{ $addresses->count() }} alamat tersedia di akunmu.</p>
            </div>

            <div class="profil-address-list">
                @forelse($addresses as $address)
                    <article class="profil-address-item">
                        <div class="profil-address-item-top">
                            <div class="profil-address-item-person">
                                <div class="profil-address-item-icon">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>
                                <div>
                                    <strong>{{ $address->nama_penerima }}</strong>
                                    <p>{{ $address->no_hp }}</p>
                                </div>
                            </div>

                            <details class="profil-address-edit-box">
                                <summary>Edit alamat</summary>

                                <form action="{{ route('alamat.update', $address->id_alamat) }}" method="POST" class="profil-address-form profil-address-form-edit">
                                    @csrf
                                    @method('PATCH')

                                    <label class="profil-address-field">
                                        <span>Nama Penerima</span>
                                        <input type="text" name="nama_penerima" value="{{ $address->nama_penerima }}" required>
                                    </label>

                                    <label class="profil-address-field">
                                        <span>Nomor HP</span>
                                        <input type="text" name="no_hp" value="{{ $address->no_hp }}" required>
                                    </label>

                                    <label class="profil-address-field profil-address-field-wide">
                                        <span>Alamat Lengkap</span>
                                        <textarea name="alamat" rows="3" maxlength="70" required>{{ $address->alamat }}</textarea>
                                    </label>

                                    <label class="profil-address-field">
                                        <span>Kota</span>
                                        <input type="text" name="kota" value="{{ $address->kota }}" required>
                                    </label>

                                    <label class="profil-address-field">
                                        <span>Kode Pos</span>
                                        <input type="text" name="kode_pos" value="{{ $address->kode_pos }}" required>
                                    </label>

                                    <label class="profil-address-checkbox profil-address-field-wide">
                                        <input type="checkbox" name="is_default" value="1" {{ $address->is_default ? 'checked' : '' }}>
                                        <span>Jadikan alamat utama</span>
                                    </label>

                                    <button type="submit" class="btn-brand">Simpan Perubahan</button>
                                </form>
                            </details>
                        </div>
                        <div class="profil-address-item-body">
                            <p>{{ $address->alamat }}</p>
                            <p>{{ $address->kota }}, {{ $address->kode_pos }}</p>
                        </div>

                        <div class="profil-address-actions">
                            @if($address->is_default)
                                <span class="profil-address-default-badge">Alamat utama</span>
                            @endif

                            <form action="{{ route('alamat.destroy', $address->id_alamat) }}" method="POST" onsubmit="return confirm('Hapus alamat ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-ghost-red profil-address-delete">Hapus</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="profil-address-empty">
                        <i class="fa-solid fa-location-dot"></i>
                        <div>
                            <strong>Belum ada alamat</strong>
                            <p>Tambahkan alamat pertamamu lewat form di samping agar checkout bisa lebih cepat.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="profil-address-card profil-address-card-form">
            <div class="profil-address-card-head">
                <h3>Tambah Alamat Baru</h3>
                <p>Alamat yang ditambahkan di sini akan langsung tersimpan sebagai alamat utama.</p>
            </div>

            <form action="{{ route('alamat.store') }}" method="POST" class="profil-address-form">
                @csrf

                <label class="profil-address-field">
                    <span>Nama Penerima</span>
                    <input type="text" name="nama_penerima" value="{{ old('nama_penerima', $user->nama) }}" required>
                </label>

                <label class="profil-address-field">
                    <span>Nomor HP</span>
                    <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" required>
                </label>

                <label class="profil-address-field profil-address-field-wide">
                    <span>Alamat Lengkap</span>
                    <textarea name="alamat" rows="4" maxlength="70" required>{{ old('alamat') }}</textarea>
                </label>

                <label class="profil-address-field">
                    <span>Kota</span>
                    <input type="text" name="kota" value="{{ old('kota') }}" required>
                </label>

                <label class="profil-address-field">
                    <span>Kode Pos</span>
                    <input type="text" name="kode_pos" value="{{ old('kode_pos') }}" required>
                </label>

                <button type="submit" class="btn-brand">Simpan Alamat</button>
            </form>
        </div>
    </div>
</section>
