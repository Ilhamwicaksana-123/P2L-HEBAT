<?php

namespace App\Http\Controllers;

use App\Models\Kategori;

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = Kategori::withCount('produk')
            ->orderBy('nama_kategori')
            ->get();

        return view('kategori.index', compact('kategori'));
    }
}
