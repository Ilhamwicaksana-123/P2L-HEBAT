<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AlamatController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateAlamat($request);
        $user = $this->authUser();
        $makeDefault = (bool) ($validated['is_default'] ?? true);
        $hasExistingAddress = $user->alamat()->exists();

        if ($makeDefault || ! $hasExistingAddress) {
            $user->alamat()->update(['is_default' => false]);
        }

        Alamat::create([
            'id_user' => $user->id_user,
            'nama_penerima' => $validated['nama_penerima'],
            'no_hp' => $validated['no_hp'],
            'alamat' => $validated['alamat'],
            'kota' => $validated['kota'],
            'kode_pos' => $validated['kode_pos'],
            'is_default' => $makeDefault || ! $hasExistingAddress,
        ]);
        $this->recordActivity('create', 'alamat', 'Pengguna menambahkan alamat pengiriman.');

        return redirect()->back()->with('success', 'Alamat berhasil disimpan.');
    }

    public function update(Request $request, int $alamat): RedirectResponse
    {
        $user = $this->authUser();
        $address = $this->findUserAddress($user->id_user, $alamat);
        $validated = $this->validateAlamat($request);
        $makeDefault = (bool) ($validated['is_default'] ?? false);

        if ($makeDefault) {
            $user->alamat()->update(['is_default' => false]);
        }

        $address->update([
            'nama_penerima' => $validated['nama_penerima'],
            'no_hp' => $validated['no_hp'],
            'alamat' => $validated['alamat'],
            'kota' => $validated['kota'],
            'kode_pos' => $validated['kode_pos'],
            'is_default' => $makeDefault ? true : $address->is_default,
        ]);
        $this->recordActivity('update', 'alamat', 'Pengguna memperbarui alamat pengiriman.');

        return redirect()->back()->with('success', 'Alamat berhasil diperbarui.');
    }

    public function destroy(int $alamat): RedirectResponse
    {
        $user = $this->authUser();
        $address = $this->findUserAddress($user->id_user, $alamat);
        $wasDefault = (bool) $address->is_default;

        $address->delete();

        if ($wasDefault) {
            /** @var Alamat|null $replacement */
            $replacement = $user->alamat()->latest('id_alamat')->first();

            if ($replacement) {
                $replacement->update(['is_default' => true]);
            }
        }
        $this->recordActivity('delete', 'alamat', 'Pengguna menghapus alamat pengiriman.');

        return redirect()->back()->with('success', 'Alamat berhasil dihapus.');
    }

    /**
     * @return array{
     *     nama_penerima: string,
     *     no_hp: string,
     *     alamat: string,
     *     kota: string,
     *     kode_pos: string,
     *     is_default?: mixed
     * }
     */
    private function validateAlamat(Request $request): array
    {
        return $request->validate([
            'nama_penerima' => 'required|string|max:100',
            'no_hp' => 'required|string|max:20',
            'alamat' => 'required|string|max:70',
            'kota' => 'required|string|max:100',
            'kode_pos' => 'required|string|max:10',
            'is_default' => 'nullable|boolean',
        ]);
    }

    private function findUserAddress(int $userId, int $alamat): Alamat
    {
        return Alamat::query()
            ->where('id_alamat', $alamat)
            ->where('id_user', $userId)
            ->firstOrFail();
    }
}
