<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Produk;

class Kategori extends Model
{
    protected $table = 'ms_kategori';

    protected $primaryKey = 'id_kategori';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'nama_kategori'
    ];

    // Relasi: 1 kategori punya banyak produk
    public function produk()
    {
        return $this->hasMany(Produk::class, 'id_kategori', 'id_kategori');
    }
}
