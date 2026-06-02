<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));

        $kategoris = Kategori::query()
            ->withCount('produk')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('nama_kategori', 'like', "%{$search}%");
            })
            ->orderBy('nama_kategori')
            ->paginate(10)
            ->withQueryString();

        return view('admin.kategoris.index', compact('kategoris', 'search'));
    }

    public function create()
    {
        return view('admin.kategoris.create', [
            'kategori' => new Kategori(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        Kategori::create($validated);

        return redirect()
            ->route('admin.kategoris.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Kategori $kategori)
    {
        return view('admin.kategoris.edit', compact('kategori'));
    }

    public function update(Request $request, Kategori $kategori)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        $kategori->update($validated);

        return redirect()
            ->route('admin.kategoris.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Kategori $kategori)
    {
        if ($kategori->produk()->exists()) {
            return redirect()
                ->route('admin.kategoris.index')
                ->with('error', 'Kategori tidak bisa dihapus karena masih dipakai oleh produk. Pindahkan atau hapus produk terkait terlebih dahulu.');
        }

        try {
        $kategori->delete();
        } catch (QueryException $exception) {
            return redirect()
                ->route('admin.kategoris.index')
                ->with('error', 'Kategori tidak bisa dihapus karena masih terhubung dengan data lain.');
        }

        return redirect()
            ->route('admin.kategoris.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
