@extends('admin.layouts.app')

@section('title', 'Tambah Produk')
@section('kicker', 'Produk Baru')
@section('heading', 'Tambah Produk')
@section('subheading', 'Tambahkan produk hasil Pekarangan Pangan Lestari lengkap dengan harga, satuan, kategori, dan gambar agar siap tampil di katalog.')

@section('content')
<div class="admin-card form-card">
    <div class="form-intro">
        <h2>Informasi produk</h2>
        <p>Lengkapi nama, harga, satuan, kategori, dan gambar agar produk langsung siap tampil di katalog.</p>
    </div>
    <form method="POST" action="{{ route('admin.produks.store') }}" enctype="multipart/form-data">
        @php($mode = 'create')
        @include('admin.produks._form')
    </form>
</div>
@endsection
