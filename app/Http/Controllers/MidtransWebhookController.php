<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Transaksi;
use App\Services\MidtransSnapService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MidtransWebhookController extends Controller
{
    public function __invoke(Request $request, MidtransSnapService $midtrans): JsonResponse
    {
        $payload = $request->all();

        if (! $midtrans->verifySignature($payload)) {
            return response()->json(['message' => 'Invalid signature.'], 403);
        }

        $transaction = Transaksi::where('kode_order_gateway', $payload['order_id'] ?? null)->first();

        if (! $transaction) {
            return response()->json(['message' => 'Transaction not found.'], 404);
        }

        $transactionStatus = (string) ($payload['transaction_status'] ?? '');
        $fraudStatus = (string) ($payload['fraud_status'] ?? '');
        $paymentStatus = $this->mapPaymentStatus($transactionStatus, $fraudStatus);

        $transaction->update([
            'status_pembayaran' => $paymentStatus,
            'payment_type' => $payload['payment_type'] ?? null,
            'payment_response' => $payload,
        ]);

        if ($paymentStatus === Transaksi::STATUS_BERHASIL) {
            $transaction->pesanan?->update([
                'status_pesanan' => Pesanan::STATUS_DIPROSES,
            ]);
        }

        if (in_array($paymentStatus, [
            Transaksi::STATUS_KADALUWARSA,
            Transaksi::STATUS_DIBATALKAN,
            Transaksi::STATUS_DITOLAK,
        ], true)) {
            $transaction->pesanan?->update([
                'status_pesanan' => Pesanan::STATUS_BATAL,
            ]);
        }

        return response()->json(['message' => 'Notification processed.']);
    }

    private function mapPaymentStatus(string $transactionStatus, string $fraudStatus): string
    {
        if ($transactionStatus === 'settlement') {
            return Transaksi::STATUS_BERHASIL;
        }

        if ($transactionStatus === 'capture') {
            return $fraudStatus === 'accept'
                ? Transaksi::STATUS_BERHASIL
                : Transaksi::STATUS_DITOLAK;
        }

        return match ($transactionStatus) {
            'pending' => Transaksi::STATUS_MENUNGGU,
            'expire' => Transaksi::STATUS_KADALUWARSA,
            'cancel' => Transaksi::STATUS_DIBATALKAN,
            'deny' => Transaksi::STATUS_DITOLAK,
            'refund', 'partial_refund' => Transaksi::STATUS_DIKEMBALIKAN,
            default => Transaksi::STATUS_MENUNGGU,
        };
    }
}
