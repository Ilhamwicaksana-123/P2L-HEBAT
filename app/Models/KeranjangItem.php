<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeranjangItem extends Model
{
    protected $table = 'ms_keranjang_item';
    protected $primaryKey = 'id_keranjang_item';

    protected $fillable = [
        'id_user',
        'id_produk',
        'jumlah',
        'qty',
        'harga_satuan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    public function getQtyAttribute(): int
    {
        return (int) ($this->attributes['jumlah'] ?? 0);
    }

    public function setQtyAttribute(int $value): void
    {
        $this->attributes['jumlah'] = $value;
    }

    public function getSubtotalAttribute(): float
    {
        return (float) ($this->attributes['jumlah'] ?? 0) * (float) $this->harga_satuan;
    }
}
