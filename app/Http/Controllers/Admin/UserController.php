<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeSuperAdmin();

        $search = trim((string) $request->query('search'));

        $users = User::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder->where('nama', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('no_hp', 'like', "%{$search}%")
                        ->orWhere('role', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('id_user')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search'));
    }

    public function create(Request $request)
    {
        $this->authorizeSuperAdmin();

        return view('admin.users.create', [
            'user' => new User(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeSuperAdmin();

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => $this->emailValidationRules(['unique:ms_user,email']),
            'password' => 'required|string|min:8',
            'no_hp' => 'required|string|max:20',
            'role' => 'required|in:super_admin,admin,user',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
        ], $this->emailValidationMessages());

        $validated['password'] = Hash::make($validated['password']);

        if ($request->hasFile('photo')) {
            $validated['foto_profil'] = $request->file('photo')->store('profile-photos', 'public');
        }

        unset($validated['photo']);

        User::create($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(Request $request, User $user)
    {
        $this->authorizeSuperAdmin();

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeSuperAdmin();

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => $this->emailValidationRules(['unique:ms_user,email,' . $user->id_user . ',id_user']),
            'no_hp' => 'required|string|max:20',
            'role' => 'required|in:super_admin,admin,user',
            'password' => 'nullable|string|min:8',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
        ], $this->emailValidationMessages());

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if ($request->hasFile('photo')) {
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }

            $validated['foto_profil'] = $request->file('photo')->store('profile-photos', 'public');
        }

        unset($validated['photo']);

        $user->update($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Profil dan hak akses pengguna berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        $this->authorizeSuperAdmin();
        $currentUser = $this->authUser();

        if ((int) $currentUser->id_user === (int) $user->id_user) {
            return back()->with('error', 'Akun yang sedang dipakai tidak bisa dihapus.');
        }

        if ($user->pesanan()->exists()) {
            return back()->with('error', 'Pengguna tidak bisa dihapus karena sudah memiliki riwayat pesanan.');
        }

        try {
            $user->delete();
        } catch (QueryException $exception) {
            return back()->with('error', 'Pengguna tidak bisa dihapus karena masih terhubung dengan data lain.');
        }

        if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
            Storage::disk('public')->delete($user->foto_profil);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }

    protected function authorizeSuperAdmin(): void
    {
        abort_unless($this->authUser()->role === 'super_admin', 403, 'Hanya super admin yang bisa mengelola pengguna.');
    }
}
