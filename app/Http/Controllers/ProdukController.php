<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        return $this->beranda($request);
    }

    public function kategori(Request $request, int $id)
    {
        $request->merge(['kategori' => $id]);

        return $this->beranda($request);
    }

    public function beranda(Request $request)
    {
        $kategori = Kategori::orderBy('nama_kategori')->get();
        $search = trim((string) $request->query('search'));

        $query = Produk::with('kategori')->active();

        if ($request->filled('kategori')) {
            $query->where('id_kategori', $request->kategori);
        }

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('nama_produk', 'like', "%{$search}%")
                    ->orWhereHas('kategori', function ($kategoriQuery) use ($search) {
                        $kategoriQuery->where('nama_kategori', 'like', "%{$search}%");
                    });
            });
        }

        $produk = $query->orderByDesc('id_produk')->get();
        $selectedKategori = $request->filled('kategori')
            ? $kategori->firstWhere('id_kategori', (int) $request->kategori)
            : null;

        return view('produk', compact('produk', 'kategori', 'selectedKategori', 'search'));
    }
}
