@extends('admin.layouts.app')

@section('title', 'Data Pengguna')
@section('kicker', 'Manajemen Pengguna')
@section('heading', 'Data Pengguna')
@section('subheading', 'Kelola akun super admin, admin, dan pengguna agar akses pengelolaan operasional P2L tetap tertata sesuai peran masing-masing.')
@section('header_actions')
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Tambah pengguna</a>
@endsection

@section('content')
<div class="admin-card">
    <form method="GET" class="toolbar">
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, email, role, atau no HP">
        <button type="submit" class="btn btn-secondary">Cari</button>
    </form>

    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>No HP</th>
                    <th>Role</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <img src="{{ $user->photo_url }}" alt="{{ $user->nama }}" class="thumb-preview">
                        </td>
                        <td>{{ $user->nama }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->no_hp }}</td>
                        <td>
                            <span class="badge {{ $user->role === 'super_admin' ? 'badge-danger-soft' : ($user->role === 'admin' ? 'badge-success' : 'badge-role') }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td>{{ optional($user->created_at)->format('d M Y H:i') }}</td>
                        <td class="actions-cell">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-small btn-secondary">
                                <i class="fa-solid fa-pen-to-square"></i>
                                <span>Edit</span>
                            </a>
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" id="delete-user-{{ $user->id_user }}" class="inline-action-form">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="button"
                                    class="btn btn-small btn-danger"
                                    data-action-trigger
                                    data-action-target="delete-user-{{ $user->id_user }}"
                                    data-action-kicker="Hapus Pengguna"
                                    data-action-title="Hapus akun ini?"
                                    data-action-text="Akun pengguna akan dihapus dari sistem. Pastikan data ini memang sudah tidak dibutuhkan lagi."
                                    data-action-confirm="Ya, Hapus"
                                >
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-state">Belum ada data pengguna.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrap">
        {{ $users->links() }}
    </div>
</div>
@endsection
