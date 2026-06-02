<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesananDetail extends Model
{
    protected $table = 'ms_pesanan_detail';
    protected $primaryKey = 'id_pesanan_detail';

    protected $fillable = [
        'id_pesanan',
        'id_produk',
        'nama_produk',
        'harga_produk',
        'jumlah_barang',
        'subtotal',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    public function getQtyAttribute(): int
    {
        return (int) $this->jumlah_barang;
    }

    public function setQtyAttribute(int $value): void
    {
        $this->attributes['jumlah_barang'] = $value;
    }

    public function getSatuanLabelAttribute(): string
    {
        return $this->produk?->satuan_label ?? 'Satuan';
    }
}
