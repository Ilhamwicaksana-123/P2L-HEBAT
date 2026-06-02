<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(): View
    {
        $user = $this->authUser();
        $addresses = $this->isAdminUser($user)
            ? collect()
            : $user->alamat()->orderByDesc('is_default')->latest()->get();

        return view('profil.profil', [
            'user' => $user,
            'addresses' => $addresses,
            'backRoute' => $this->getBackRoute($user),
            'backLabel' => $this->getBackLabel($user),
        ]);
    }

    public function edit(): View
    {
        $user = $this->authUser();

        return view('profil.edit', [
            'user' => $user,
            'backRoute' => $this->getBackRoute($user),
            'backLabel' => $this->getBackLabel($user),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $this->authUser();

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:ms_user,email,' . $user->id_user . ',id_user',
            'no_hp' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user->nama = $request->nama;
        $user->email = $request->email;
        $user->no_hp = $request->filled('no_hp')
            ? $request->input('no_hp')
            : (string) $user->no_hp;

        if ($request->hasFile('photo')) {
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }

            $user->foto_profil = $request->file('photo')->store('profile-photos', 'public');
        }

        $user->save();

        return redirect()
            ->to($this->getProfileRedirectRoute($user))
            ->with('success', 'Profil berhasil diperbarui!');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $this->authUser();
        $requiresCurrentPassword = $this->requiresCurrentPasswordConfirmation($user);

        $request->validate([
            'current_password' => [$requiresCurrentPassword ? 'required' : 'nullable', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($requiresCurrentPassword
            && ! Hash::check((string) $request->current_password, (string) $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Password saat ini tidak sesuai.'])
                ->withInput($request->except(['password', 'password_confirmation', 'current_password']));
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()
            ->to($this->getProfileRedirectRoute($user))
            ->with('success', 'Password berhasil diperbarui.');
    }

    protected function requiresCurrentPasswordConfirmation(User $user): bool
    {
        return filled($user->password);
    }

    protected function getBackRoute(User $user): string
    {
        return $this->isAdminUser($user)
            ? route('admin.dashboard')
            : route('produk.index');
    }

    protected function getBackLabel(User $user): string
    {
        return $this->isAdminUser($user)
            ? 'Kembali ke Dashboard'
            : 'Kembali ke Produk';
    }

    protected function getProfileRedirectRoute(User $user): string
    {
        return $this->isAdminUser($user)
            ? route('admin.dashboard')
            : route('profil.show');
    }

    protected function isAdminUser(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'admin'], true);
    }
}
