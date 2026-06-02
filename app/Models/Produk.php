<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Produk extends Model
{
    protected $table = 'ms_produk';

    protected $primaryKey = 'id_produk';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'nama_produk',
        'harga_produk',
        'stok',
        'gambar_produk',
        'status_produk',
        'id_kategori',
        'satuan',
    ];

    public const STATUS_AKTIF = 'aktif';
    public const STATUS_TIDAK_AKTIF = 'tidak aktif';
    public const SATUAN_KG = 'kg';
    public const SATUAN_PACK = 'pack';

    public const SATUAN_OPTIONS = [
        self::SATUAN_KG => 'Kg',
        self::SATUAN_PACK => 'Pack',
    ];

    public function scopeActive($query)
    {
        return $query->where('status_produk', self::STATUS_AKTIF);
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status_produk === self::STATUS_AKTIF;
    }

    public function hasStock(): bool
    {
        return (int) $this->stok > 0;
    }

    public function isOutOfStock(): bool
    {
        return ! $this->hasStock();
    }

    public function getSatuanLabelAttribute(): string
    {
        return self::SATUAN_OPTIONS[strtolower((string) $this->satuan)] ?? self::SATUAN_OPTIONS[self::SATUAN_KG];
    }

    public function getHargaSatuanLabelAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->harga_produk, 0, ',', '.') . ' / ' . strtolower($this->satuan_label);
    }

    public function getGambarProdukUrlAttribute(): string
    {
        $path = trim((string) $this->gambar_produk);

        if ($path === '') {
            return 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=900&q=80';
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (Str::startsWith($path, 'storage/')) {
            return asset($path);
        }

        return asset('storage/' . ltrim($path, '/'));
    }

    public function kategori()
    {
        return $this->belongsTo(
            Kategori::class,
            'id_kategori',
            'id_kategori'
        );
    }

    public function keranjangItems()
    {
        return $this->hasMany(KeranjangItem::class, 'id_produk', 'id_produk');
    }

    public function pesananDetail()
    {
        return $this->hasMany(PesananDetail::class, 'id_produk', 'id_produk');
    }
}
