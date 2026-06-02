<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentMethodController extends Controller
{
    public function index(): View
    {
        PaymentMethod::syncDefaults();

        return view('admin.payment-methods.index', [
            'paymentMethods' => PaymentMethod::query()
                ->orderBy('sort_order')
                ->get(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        PaymentMethod::syncDefaults();

        $validated = $request->validate([
            'methods' => ['nullable', 'array'],
            'methods.*' => ['string', 'exists:payment_methods,code'],
        ]);

        $activeMethods = $validated['methods'] ?? [];

        if ($activeMethods === []) {
            return back()
                ->withInput()
                ->with('error', 'Minimal satu metode pembayaran harus aktif.');
        }

        PaymentMethod::query()->update(['is_active' => false]);
        PaymentMethod::query()
            ->whereIn('code', $activeMethods)
            ->update(['is_active' => true]);

        return redirect()
            ->route('admin.payment-methods.index')
            ->with('success', 'Pengaturan metode pembayaran berhasil diperbarui.');
    }
}
