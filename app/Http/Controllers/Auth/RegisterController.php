<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => $this->emailValidationRules(['unique:ms_user,email']),
            'password' => 'required'
        ], $this->emailValidationMessages());

        $user = User::create([
            'nama' => $request->name,
            'email' => $request->email,
            'no_hp' => '', // default kosong
            'password' => Hash::make($request->password),
            'role' => 'user', // default role
        ]);

        // Auto login setelah register
        Auth::login($user);
        $request->session()->put('auth_provider', 'manual');
        $this->recordActivity('register', 'auth', 'Pengguna membuat akun baru.', $user);

        return redirect('/produk')->with('register_success', 'Akun kamu sudah aktif. Silakan mulai belanja.');
    }
}
