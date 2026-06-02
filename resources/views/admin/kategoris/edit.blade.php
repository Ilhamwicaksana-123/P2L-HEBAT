@extends('admin.layouts.app')

@section('title', 'Edit Kategori')
@section('kicker', 'Manajemen Kategori')
@section('heading', 'Edit Kategori')
@section('subheading', 'Perbarui nama kategori agar pengelompokan produk hasil Pekarangan Pangan Lestari tetap jelas dan konsisten.')

@section('content')
<div class="admin-card form-card">
    <div class="form-intro">
        <h2>Sesuaikan kategori</h2>
        <p>Gunakan nama kategori yang singkat dan jelas agar admin maupun pengguna lebih mudah memahaminya.</p>
    </div>
    <form method="POST" action="{{ route('admin.kategoris.update', $kategori) }}">
        @csrf
        @method('PUT')
        @include('admin.kategoris._form')
    </form>
</div>
@endsection
