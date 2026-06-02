@extends('admin.layouts.app')

@section('title', 'Edit Pengguna')
@section('hide_admin_header', true)
@section('kicker', 'Kelola Pengguna')
@section('heading', 'Edit Profil Pengguna')
@section('subheading', 'Super admin dapat memperbarui profil, foto, password, dan hak akses pengguna dari satu halaman pengelolaan.')

@section('content')
<div class="admin-card form-card">
    <div class="form-intro">
        <h2>Perbarui data akun pengguna</h2>
        <p>Sesuaikan nama, email, nomor HP, foto profil, password, dan role pengguna sesuai kebutuhan pengelolaan akun.</p>
    </div>
    <form method="POST" action="{{ route('admin.users.update', $user) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @php($mode = 'edit')
        @include('admin.users._form')
    </form>
</div>
@endsection
