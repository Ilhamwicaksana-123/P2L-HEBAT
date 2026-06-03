<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

abstract class Controller
{
    protected const EMAIL_MAX_LENGTH = 30;

    protected function authUser(): User
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            throw new RuntimeException('Authenticated user is not available.');
        }

        return $user;
    }

    protected function emailValidationRules(array $extraRules = []): array
    {
        return array_merge([
            'required',
            'string',
            'email:rfc',
            'max:' . self::EMAIL_MAX_LENGTH,
        ], $extraRules);
    }

    protected function emailValidationMessages(): array
    {
        return [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal ' . self::EMAIL_MAX_LENGTH . ' karakter.',
            'email.unique' => 'Email sudah terdaftar.',
            'email.exists' => 'Email tidak terdaftar.',
        ];
    }

    protected function recordActivity(
        string $action,
        ?string $module = null,
        ?string $description = null,
        ?User $user = null,
        Produk|int|null $produk = null
    ): void
    {
        try {
            $user ??= Auth::user();

            if (! $user instanceof User) {
                return;
            }

            if (! in_array($user->role, ['admin', 'user'], true)) {
                return;
            }

            ActivityLog::create([
                'id_user' => $user->id_user,
                'id_produk' => $produk instanceof Produk ? $produk->id_produk : $produk,
                'nama' => $user->nama,
                'role' => $user->role,
                'action' => $action,
                'module' => $module,
                'description' => $description,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (Throwable $exception) {
            Log::warning('Activity log gagal disimpan.', [
                'action' => $action,
                'module' => $module,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
