<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Alamat extends Model
{
    use SoftDeletes;

    protected $table = 'ms_alamat';
    protected $primaryKey = 'id_alamat';

    protected $fillable = [
        'id_user',
        'nama_penerima',
        'no_hp',
        'alamat',
        'kota',
        'kode_pos',
        'is_default',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'id_alamat', 'id_alamat');
    }
}
