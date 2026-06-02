<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ResetPasswordController extends Controller
{
    public function show(Request $request, string $token)
    {
        $email = (string) $request->query('email');

        if ($email === '') {
            return redirect('/login')->with('error', 'Link reset password tidak valid atau sudah expired');
        }

        // Validasi token
        $reset = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$reset || !hash_equals($reset->token, hash('sha256', $token))) {
            return redirect('/login')->with('error', 'Link reset password tidak valid atau sudah expired');
        }

        return view('auth.reset-password', ['token' => $token, 'email' => $email]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:ms_user,email',
            'token' => 'required',
            'password' => 'required|confirmed'
        ], [
            'password.confirmed' => 'Password tidak cocok'
        ]);

        // Validasi token
        $reset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$reset || !hash_equals($reset->token, hash('sha256', $request->token))) {
            return back()->with('error', 'Link reset password tidak valid atau sudah expired');
        }

        // Update password
        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password)
        ]);

        // Hapus token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect('/login')->with('success', 'Password berhasil direset. Silakan login dengan password baru.');
    }
}
