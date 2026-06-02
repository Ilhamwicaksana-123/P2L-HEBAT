@extends('admin.layouts.app')

@section('title', 'Tambah Kategori')
@section('kicker', 'Manajemen Kategori')
@section('heading', 'Tambah Kategori')
@section('subheading', 'Tambahkan kategori baru agar produk hasil Pekarangan Pangan Lestari lebih mudah dikelompokkan dan dicari di katalog.')

@section('content')
<div class="admin-card form-card">
    <div class="form-intro">
        <h2>Buat kategori baru</h2>
        <p>Kategori membantu produk lebih mudah dikelompokkan dan dicari pada halaman katalog.</p>
    </div>
    <form method="POST" action="{{ route('admin.kategoris.store') }}">
        @include('admin.kategoris._form')
    </form>
</div>
@endsection
