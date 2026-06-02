<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function defaults(): array
    {
        return [
            Pesanan::METODE_TRANSFER => [
                'name' => 'Transfer Bank',
                'description' => 'Pembayaran melalui simulasi bank di Midtrans.',
                'sort_order' => 1,
            ],
            Pesanan::METODE_E_WALLET => [
                'name' => 'E-Wallet',
                'description' => 'Pembayaran digital melalui simulasi Midtrans.',
                'sort_order' => 2,
            ],
            Pesanan::METODE_COD => [
                'name' => 'COD',
                'description' => 'Bayar langsung saat pesanan diterima.',
                'sort_order' => 3,
            ],
        ];
    }

    public static function syncDefaults(): void
    {
        foreach (self::defaults() as $code => $data) {
            self::firstOrCreate(
                ['code' => $code],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'is_active' => true,
                    'sort_order' => $data['sort_order'],
                ]
            );
        }
    }

    public static function activeCodes(): array
    {
        self::syncDefaults();

        return self::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('code')
            ->all();
    }
}
