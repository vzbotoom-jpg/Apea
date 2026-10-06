<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();

        return view('admin.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'no_telepon' => 'nullable|string|max:15',
            'alamat' => 'nullable|string',
            'password' => 'nullable|min:8|confirmed',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.profile.edit')
            ->with('success', 'Profil berhasil diperbarui!');
    }

    public function settings()
    {
        $settings = [
            'max_alat_per_hari'       => (int) Setting::get('max_alat_per_hari', 2),
            'masa_tenggang_hari'      => (int) Setting::get('masa_tenggang_hari', 2),
            'denda_per_hari'          => (int) Setting::get('denda_per_hari', 5000),
            'maks_perpanjangan_hari'  => (int) Setting::get('maks_perpanjangan_hari', 7),
            'metode_pembayaran_aktif' => json_decode(Setting::get('metode_pembayaran_aktif', json_encode(['tunai', 'transfer'])), true),
            'info_transfer'           => Setting::get('info_transfer', ''),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'max_alat_per_hari'       => 'required|integer|min:1|max:10',
            'masa_tenggang_hari'      => 'required|integer|min:0|max:14',
            'denda_per_hari'          => 'required|integer|min:0|max:1000000',
            'maks_perpanjangan_hari'  => 'required|integer|min:1|max:30',
            'metode_pembayaran_aktif' => 'required|array|min:1',
            'metode_pembayaran_aktif.*' => 'in:tunai,transfer',
            'info_transfer'           => 'nullable|string|max:255',
        ]);

        $metode = array_values($validated['metode_pembayaran_aktif']);
        $info   = $validated['info_transfer'] ?? '';
        unset($validated['metode_pembayaran_aktif'], $validated['info_transfer']);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        Setting::set('metode_pembayaran_aktif', json_encode($metode));
        Setting::set('info_transfer', $info);

        return redirect()->route('admin.settings')->with('success', '✅ Pengaturan sistem berhasil disimpan.');
    }
}
