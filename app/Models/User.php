<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

/**
 * @property int $id_user
 * @property string $nama
 * @property string $email
 * @property string|null $no_hp
 * @property string|null $google_id
 * @property string|null $role
 * @property string|null $foto_profil
 * @property string $photo_url
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'ms_user';
    protected $primaryKey = 'id_user';

    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nama',
        'email',
        'password',
        'no_hp',
        'google_id',
        'role',
        'foto_profil',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    public function getPhotoUrlAttribute(): string
    {
        $path = trim((string) $this->foto_profil);

        if ($path !== '') {
            if (Str::startsWith($path, ['http://', 'https://'])) {
                return $path;
            }

            if (Str::startsWith($path, 'storage/')) {
                return asset($path);
            }

            return asset('storage/' . ltrim($path, '/'));
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->nama ?? 'User');
    }

    public function alamat(): HasMany
    {
        return $this->hasMany(Alamat::class, 'id_user', 'id_user');
    }

    public function keranjangItems(): HasMany
    {
        return $this->hasMany(KeranjangItem::class, 'id_user', 'id_user');
    }

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'id_user', 'id_user');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'id_user', 'id_user');
    }
}
