<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PesananController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));
        $status = trim((string) $request->query('status'));
        $dateFrom = trim((string) $request->query('date_from'));
        $dateTo = trim((string) $request->query('date_to'));

        $orders = Pesanan::with(['user', 'alamat', 'detail'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder->where('kode_pesanan', 'like', "%{$search}%")
                        ->orWhere('metode_pembayaran', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('nama', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->orWhereHas('alamat', function ($alamatQuery) use ($search) {
                            $alamatQuery->where('nama_penerima', 'like', "%{$search}%")
                                ->orWhere('kota', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status !== '', fn ($query) => $query->where('status_pesanan', $status))
            ->when($dateFrom !== '', fn ($query) => $query->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo !== '', fn ($query) => $query->whereDate('created_at', '<=', $dateTo))
            ->orderByDesc('id_pesanan')
            ->paginate(20)
            ->withQueryString();

        return view('admin.pesanan.index', [
            'orders' => $orders,
            'search' => $search,
            'status' => $status,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'statusOptions' => Pesanan::statusOptions(),
        ]);
    }

    public function show(Pesanan $pesanan)
    {
        $pesanan->load(['user', 'alamat', 'detail.produk']);

        return view('admin.pesanan.show', [
            'order' => $pesanan,
            'statusOptions' => Pesanan::statusOptions(),
        ]);
    }

    public function updateStatus(Request $request, Pesanan $pesanan): RedirectResponse
    {
        $validated = $request->validate([
            'status_pesanan' => ['required', 'string', Rule::in(array_keys(Pesanan::statusOptions()))],
        ]);

        $newStatus = $validated['status_pesanan'];

        if ($pesanan->status_pesanan === $newStatus) {
            return redirect()
                ->route('admin.pesanan.show', ['pesanan' => $pesanan])
                ->with('success', 'Status pesanan sudah sesuai dan tidak perlu diperbarui.');
        }

        $pesanan->update([
            'status_pesanan' => $newStatus,
        ]);

        return redirect()
            ->route('admin.pesanan.show', ['pesanan' => $pesanan])
            ->with('success', 'Status pesanan berhasil diperbarui menjadi '.$pesanan->status_label.'.');
    }
}
