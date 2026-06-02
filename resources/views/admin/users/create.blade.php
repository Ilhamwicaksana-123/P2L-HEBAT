@extends('admin.layouts.app')

@section('title', 'Tambah Pengguna')
@section('kicker', 'Pengguna Baru')
@section('heading', 'Tambah Pengguna')
@section('subheading', 'Buat akun admin, super admin, atau pengguna baru agar akses pengelolaan dan penggunaan sistem P2L tetap tertata.')

@section('content')
<div class="admin-card form-card">
    <div class="form-intro">
        <h2>Buat akun baru</h2>
        <p>Tentukan role pengguna sejak awal agar hak akses admin dan user tetap terjaga dengan rapi.</p>
    </div>
    <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
        @php($mode = 'create')
        @include('admin.users._form')
    </form>
</div>
@endsection
