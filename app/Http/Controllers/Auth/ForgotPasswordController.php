<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;

class ForgotPasswordController extends Controller
{
    public function show()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => $this->emailValidationRules(['exists:ms_user,email'])
        ], $this->emailValidationMessages());

        $user = User::where('email', $request->email)->first();
        $token = Str::random(60);

        // Simpan token ke database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => hash('sha256', $token),
                'created_at' => now()
            ]
        );

        // Buat link reset
        $resetLink = route('password.reset', ['token' => $token, 'email' => $request->email]);

        // Kirim email (untuk testing, tampilkan link di page)
        // Mail::send('emails.reset-password', ['link' => $resetLink], function($m) use ($user) {
        //     $m->to($user->email)->subject('Reset Password');
        // });

        // Untuk development, store di session
        session()->put('reset_link', $resetLink);

        return redirect('/login')->with('success', 'Link reset password telah dikirim ke email Anda. (Untuk testing: ' . $resetLink . ')');
    }
}
