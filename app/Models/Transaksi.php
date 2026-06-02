<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'tr_transaksi';
    protected $primaryKey = 'id_transaksi';

    protected $fillable = [
        'id_pesanan',
        'kode_order_gateway',
        'token_pembayaran',
        'snap_token',
        'snap_redirect_url',
        'payment_type',
        'payment_response',
        'total_tagihan',
        'status_pembayaran',
    ];

    protected $casts = [
        'payment_response' => 'array',
    ];

    public const STATUS_MENUNGGU = 'menunggu';
    public const STATUS_BERHASIL = 'berhasil';
    public const STATUS_KADALUWARSA = 'kadaluwarsa';
    public const STATUS_DIBATALKAN = 'dibatalkan';
    public const STATUS_DITOLAK = 'ditolak';
    public const STATUS_DIKEMBALIKAN = 'dikembalikan';

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }
}
