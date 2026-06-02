<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Pesanan extends Model
{
    protected $table = 'ms_pesanan';
    protected $primaryKey = 'id_pesanan';

    protected $fillable = [
        'id_user',
        'id_alamat',
        'kode_pesanan',
        'status_pesanan',
        'metode_pembayaran',
        'total_harga',
    ];

    public const STATUS_MENUNGGU_PEMBAYARAN = 'menunggu pembayaran';
    public const STATUS_DIPROSES = 'diproses';
    public const STATUS_DIKIRIM = 'dikirim';
    public const STATUS_SELESAI = 'selesai';
    public const STATUS_BATAL = 'batal';

    public const METODE_TRANSFER = 'transfer';
    public const METODE_E_WALLET = 'e_wallet';
    public const METODE_COD = 'cod';

    public static function statusOptions(): array
    {
        return [
            self::STATUS_MENUNGGU_PEMBAYARAN => 'Menunggu Pembayaran',
            self::STATUS_DIPROSES => 'Diproses',
            self::STATUS_DIKIRIM => 'Dikirim',
            self::STATUS_SELESAI => 'Selesai',
            self::STATUS_BATAL => 'Batal',
        ];
    }

    public function hasReachedStatus(string $status): bool
    {
        $progress = [
            self::STATUS_MENUNGGU_PEMBAYARAN => 1,
            self::STATUS_DIPROSES => 2,
            self::STATUS_DIKIRIM => 3,
            self::STATUS_SELESAI => 4,
        ];

        if ($this->status_pesanan === self::STATUS_BATAL) {
            return false;
        }

        return ($progress[$this->status_pesanan] ?? 0) >= ($progress[$status] ?? PHP_INT_MAX);
    }

    public function getIsCancelledAttribute(): bool
    {
        return $this->status_pesanan === self::STATUS_BATAL;
    }

    public function getRouteKeyName(): string
    {
        return $this->primaryKey;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusOptions()[$this->status_pesanan]
            ?? ucfirst(str_replace('_', ' ', (string) $this->status_pesanan));
    }

    public function getPaymentInstructionsAttribute(): array
    {
        $configs = [
            self::METODE_TRANSFER => [
                'label' => 'Transfer ke rekening P2L Hebat',
                'account_name' => 'P2L Hebat',
                'account_number' => '1234567890',
                'provider' => 'Bank BRI',
                'steps' => [
                    'Transfer sesuai total belanja sampai 3 digit terakhir.',
                    'Simpan bukti transfer untuk pengecekan manual admin.',
                    'Klik tombol konfirmasi pembayaran setelah transfer selesai.',
                ],
            ],
            self::METODE_E_WALLET => [
                'label' => 'Bayar ke akun e-wallet P2L Hebat',
                'account_name' => 'P2L Hebat',
                'account_number' => '0812-3456-7890',
                'provider' => 'DANA / OVO / GoPay',
                'steps' => [
                    'Kirim saldo ke nomor e-wallet yang tertera.',
                    'Pastikan nominal sama dengan total pesanan.',
                    'Setelah berhasil, lanjutkan dengan konfirmasi pembayaran.',
                ],
            ],
            self::METODE_COD => [
                'label' => 'Bayar saat pesanan diterima',
                'account_name' => 'Pembayaran di tempat',
                'account_number' => '-',
                'provider' => 'Kurir P2L Hebat',
                'steps' => [
                    'Pesanan akan diproses tanpa pembayaran online.',
                    'Siapkan uang pas saat pesanan diterima.',
                    'Bayar langsung ke kurir ketika barang sampai.',
                ],
            ],
        ];

        return Arr::get($configs, $this->metode_pembayaran, $configs[self::METODE_TRANSFER]);
    }

    public function getCanBePaidAttribute(): bool
    {
        return in_array($this->metode_pembayaran, [self::METODE_TRANSFER, self::METODE_E_WALLET], true)
            && $this->status_pesanan === self::STATUS_MENUNGGU_PEMBAYARAN;
    }

    public function getMetodePembayaranLabelAttribute(): string
    {
        return [
            self::METODE_TRANSFER => 'Transfer Bank',
            self::METODE_E_WALLET => 'E-Wallet',
            self::METODE_COD => 'COD',
        ][$this->metode_pembayaran] ?? ucfirst(str_replace('_', ' ', (string) $this->metode_pembayaran));
    }

    public function getStatusCssAttribute(): string
    {
        return str_replace(' ', '_', (string) $this->status_pesanan);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function alamat()
    {
        return $this->belongsTo(Alamat::class, 'id_alamat', 'id_alamat');
    }

    public function detail()
    {
        return $this->hasMany(PesananDetail::class, 'id_pesanan', 'id_pesanan');
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'id_pesanan', 'id_pesanan');
    }
}
