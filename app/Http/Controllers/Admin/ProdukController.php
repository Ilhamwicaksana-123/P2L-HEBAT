<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
    private const DEFAULT_STOK = 999999;

    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));

        $produks = Produk::with('kategori')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder->where('nama_produk', 'like', "%{$search}%")
                        ->orWhere('harga_produk', 'like', "%{$search}%")
                        ->orWhere('satuan', 'like', "%{$search}%")
                        ->orWhereHas('kategori', function ($kategoriQuery) use ($search) {
                            $kategoriQuery->where('nama_kategori', 'like', "%{$search}%");
                        });
                });
            })
            ->orderByDesc('id_produk')
            ->paginate(20)
            ->withQueryString();

        return view('admin.produks.index', compact('produks', 'search'));
    }

    public function create()
    {
        return view('admin.produks.create', [
            'produk' => new Produk(),
            'kategoris' => Kategori::orderBy('nama_kategori')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateProduk($request, true);
        $validated['stok'] = self::DEFAULT_STOK;
        $validated['status_produk'] = $request->boolean('status_produk')
            ? Produk::STATUS_AKTIF
            : Produk::STATUS_TIDAK_AKTIF;

        if ($request->hasFile('gambar_produk')) {
            $validated['gambar_produk'] = $request->file('gambar_produk')->store('produk', 'public');
        }

        $produk = Produk::create($validated);
        $this->recordActivity(
            'create',
            'produk',
            'Admin menambahkan produk ' . $produk->nama_produk . '.',
            produk: $produk
        );

        return redirect()
            ->route('admin.produks.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Produk $produk)
    {
        return view('admin.produks.edit', [
            'produk' => $produk,
            'kategoris' => Kategori::orderBy('nama_kategori')->get(),
        ]);
    }

    public function update(Request $request, Produk $produk)
    {
        $validated = $this->validateProduk($request, false);
        $validated['status_produk'] = $request->boolean('status_produk')
            ? Produk::STATUS_AKTIF
            : Produk::STATUS_TIDAK_AKTIF;

        if ($request->hasFile('gambar_produk')) {
            if ($produk->gambar_produk && Storage::disk('public')->exists($produk->gambar_produk)) {
                Storage::disk('public')->delete($produk->gambar_produk);
            }

            $validated['gambar_produk'] = $request->file('gambar_produk')->store('produk', 'public');
        }

        $produk->update($validated);
        $produk->refresh();
        $this->recordActivity(
            'update',
            'produk',
            'Admin memperbarui produk ' . $produk->nama_produk . '.',
            produk: $produk
        );

        return redirect()
            ->route('admin.produks.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Produk $produk)
    {
        $namaProduk = $produk->nama_produk;
        $produkId = $produk->id_produk;

        if ($produk->pesananDetail()->exists()) {
            return redirect()
                ->route('admin.produks.index')
                ->with('error', 'Produk tidak bisa dihapus karena sudah terhubung dengan riwayat pesanan.');
        }

        try {
            $produk->delete();
        } catch (QueryException $exception) {
            return redirect()
                ->route('admin.produks.index')
                ->with('error', 'Produk tidak bisa dihapus karena masih terhubung dengan data lain.');
        }

        if ($produk->gambar_produk && Storage::disk('public')->exists($produk->gambar_produk)) {
            Storage::disk('public')->delete($produk->gambar_produk);
        }

        $this->recordActivity(
            'delete',
            'produk',
            'Admin menghapus produk ' . $namaProduk . '.',
            produk: $produkId
        );

        return redirect()
            ->route('admin.produks.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    protected function validateProduk(Request $request, bool $isCreate): array
    {
        $imageRule = $isCreate ? 'required|image|mimes:jpg,jpeg,png,webp|max:2048' : 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048';

        return $request->validate([
            'nama_produk' => 'required|string|max:255',
            'harga_produk' => 'required|numeric|min:0',
            'satuan' => 'required|in:kg,pack',
            'gambar_produk' => $imageRule,
            'id_kategori' => 'required|exists:ms_kategori,id_kategori',
        ]);
    }
}
