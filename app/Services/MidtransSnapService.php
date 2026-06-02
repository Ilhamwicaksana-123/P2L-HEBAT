<?php

namespace App\Services;

use App\Models\Pesanan;
use App\Models\Transaksi;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class MidtransSnapService
{
    public function isConfigured(): bool
    {
        return filled($this->serverKey());
    }

    public function createOrReuseTransaction(Pesanan $order): Transaksi
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('MIDTRANS_SERVER_KEY belum diisi.');
        }

        $existing = $order->transaksi()
            ->where('status_pembayaran', Transaksi::STATUS_MENUNGGU)
            ->latest('id_transaksi')
            ->first();

        if ($existing && filled($existing->snap_redirect_url)) {
            return $existing;
        }

        $order->loadMissing(['detail.produk', 'alamat', 'user']);

        $gatewayOrderCode = $order->kode_pesanan . '-' . $order->id_pesanan;
        $payload = $this->payload($order, $gatewayOrderCode);

        try {
            $response = Http::withBasicAuth($this->serverKey(), '')
                ->acceptJson()
                ->asJson()
                ->post($this->snapUrl(), $payload)
                ->throw()
                ->json();
        } catch (RequestException $exception) {
            $message = $exception->response?->json('error_messages.0')
                ?? $exception->response?->body()
                ?? $exception->getMessage();

            throw new RuntimeException('Gagal membuat transaksi Midtrans: ' . $message, 0, $exception);
        }

        $snapToken = (string) ($response['token'] ?? '');
        $redirectUrl = (string) ($response['redirect_url'] ?? '');

        if ($snapToken === '' || $redirectUrl === '') {
            throw new RuntimeException('Response Midtrans tidak berisi token pembayaran.');
        }

        return Transaksi::create([
            'id_pesanan' => $order->id_pesanan,
            'kode_order_gateway' => $gatewayOrderCode,
            'token_pembayaran' => Str::limit($snapToken, 30, ''),
            'snap_token' => $snapToken,
            'snap_redirect_url' => $redirectUrl,
            'total_tagihan' => (int) $order->total_harga,
            'status_pembayaran' => Transaksi::STATUS_MENUNGGU,
        ]);
    }

    public function verifySignature(array $payload): bool
    {
        $signature = (string) ($payload['signature_key'] ?? '');

        if ($signature === '' || ! $this->isConfigured()) {
            return false;
        }

        $expected = hash('sha512', implode('', [
            (string) ($payload['order_id'] ?? ''),
            (string) ($payload['status_code'] ?? ''),
            (string) ($payload['gross_amount'] ?? ''),
            $this->serverKey(),
        ]));

        return hash_equals($expected, $signature);
    }

    private function payload(Pesanan $order, string $gatewayOrderCode): array
    {
        return [
            'transaction_details' => [
                'order_id' => $gatewayOrderCode,
                'gross_amount' => (int) $order->total_harga,
            ],
            'customer_details' => [
                'first_name' => $order->user?->nama ?? 'Pelanggan P2L',
                'email' => $order->user?->email,
                'phone' => $order->user?->no_hp,
                'shipping_address' => [
                    'first_name' => $order->alamat?->nama_penerima,
                    'phone' => $order->alamat?->no_hp,
                    'address' => $order->alamat?->alamat,
                    'city' => $order->alamat?->kota,
                    'postal_code' => $order->alamat?->kode_pos,
                    'country_code' => 'IDN',
                ],
            ],
            'item_details' => $order->detail->map(fn ($detail) => [
                'id' => (string) $detail->id_produk,
                'price' => (int) $detail->harga_produk,
                'quantity' => (int) $detail->jumlah_barang,
                'name' => Str::limit($detail->nama_produk, 50, ''),
            ])->values()->all(),
            'callbacks' => [
                'finish' => route('pesanan.show', $order),
            ],
        ];
    }

    private function serverKey(): ?string
    {
        return config('services.midtrans.server_key');
    }

    private function snapUrl(): string
    {
        return config('services.midtrans.is_production')
            ? config('services.midtrans.snap_production_url')
            : config('services.midtrans.snap_sandbox_url');
    }
}
