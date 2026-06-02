@extends('admin.layouts.app')

@section('title', 'Edit Produk')
@section('kicker', 'Ubah Produk')
@section('heading', 'Edit Produk')
@section('subheading', 'Perbarui informasi produk hasil Pekarangan Pangan Lestari agar data katalog, harga, satuan, kategori, dan status tampil tetap akurat.')

@section('content')
<div class="admin-card form-card">
    <div class="form-intro">
        <h2>Perbarui data produk</h2>
        <p>Sesuaikan informasi produk, ganti gambar bila perlu, lalu simpan perubahan agar katalog tetap akurat.</p>
    </div>
    <form method="POST" action="{{ route('admin.produks.update', $produk) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @php($mode = 'edit')
        @include('admin.produks._form')
    </form>
</div>
@endsection
