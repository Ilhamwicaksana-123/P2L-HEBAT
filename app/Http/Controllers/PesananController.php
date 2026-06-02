<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\PaymentMethod;
use App\Services\MidtransSnapService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class PesananController extends Controller
{
    public function index(): View
    {
        $orders = $this->authUser()
            ->pesanan()
            ->with(['detail.produk', 'alamat'])
            ->orderByDesc('id_pesanan')
            ->get();

        return view('pesanan.index', [
            'orders' => $orders,
        ]);
    }

    public function show(Pesanan $order): View
    {
        abort_unless((int) $order->id_user === (int) Auth::id(), 403);

        $order->load(['detail.produk', 'alamat', 'transaksi']);

        return view('pesanan.show', [
            'order' => $order,
        ]);
    }

    public function pay(Pesanan $order, MidtransSnapService $midtrans): RedirectResponse
    {
        abort_unless((int) $order->id_user === (int) Auth::id(), 403);

        if (! $order->can_be_paid) {
            return back()->with('error', 'Pesanan ini tidak bisa dibayar lagi.');
        }

        if (! in_array($order->metode_pembayaran, PaymentMethod::activeCodes(), true)) {
            return back()->with('error', 'Metode pembayaran untuk pesanan ini sedang nonaktif.');
        }

        try {
            $transaction = $midtrans->createOrReuseTransaction($order);
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()->away($transaction->snap_redirect_url);
    }
}
