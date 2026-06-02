<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

abstract class Controller
{
    protected function authUser(): User
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            throw new RuntimeException('Authenticated user is not available.');
        }

        return $user;
    }
}
