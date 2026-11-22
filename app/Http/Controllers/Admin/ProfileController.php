<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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

        $emailChanged = $user->email !== $validated['email'];
        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        if ($emailChanged) {
            $user->forceFill(['email_verified_at' => null])->save();
            $user->sendEmailVerificationNotification();

            return redirect()->route('verification.notice')
                ->with('success', 'Email berubah. Silakan verifikasi alamat email baru Anda.');
        }

        return redirect()->route('admin.profile.edit')
            ->with('success', 'Profil berhasil diperbarui!');
    }

    public function settings()
    {
        $configuredMethods = json_decode(
            Setting::get('metode_pembayaran_aktif', json_encode(['tunai'])),
            true
        );

        $settings = [
            'max_alat_per_hari' => (int) Setting::get('max_alat_per_hari', 2),
            'masa_tenggang_hari' => (int) Setting::get('masa_tenggang_hari', 2),
            'denda_per_hari' => (int) Setting::get('denda_per_hari', 5000),
            'maks_perpanjangan_hari' => (int) Setting::get('maks_perpanjangan_hari', 7),
            'metode_pembayaran_aktif' => Pembayaran::normalizeMethods(
                is_array($configuredMethods) ? $configuredMethods : ['tunai']
            ),
            'qr_pembayaran' => Pembayaran::qrCodes(),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'max_alat_per_hari' => 'required|integer|min:1|max:10',
            'masa_tenggang_hari' => 'required|integer|min:0|max:14',
            'denda_per_hari' => 'required|integer|min:0|max:1000000',
            'maks_perpanjangan_hari' => 'required|integer|min:1|max:30',
            'metode_pembayaran_aktif' => 'required|array|min:1',
            'metode_pembayaran_aktif.*' => 'required|in:'.implode(',', array_keys(Pembayaran::METODE_LIST)),
            'qr_codes' => 'nullable|array',
            'qr_codes.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $metode = array_values(array_unique($validated['metode_pembayaran_aktif']));
        $qrCodes = Pembayaran::qrCodes();

        foreach ($metode as $method) {
            $hasSavedQr = ! empty($qrCodes[$method])
                && Storage::disk('public')->exists($qrCodes[$method]);
            if ($method !== 'tunai' && ! $request->hasFile("qr_codes.{$method}") && ! $hasSavedQr) {
                throw ValidationException::withMessages([
                    "qr_codes.{$method}" => 'Unggah QR untuk metode '.Pembayaran::METODE_LIST[$method].'.',
                ]);
            }
        }

        foreach ($metode as $method) {
            if ($request->hasFile("qr_codes.{$method}")) {
                $qrCodes[$method] = $request->file("qr_codes.{$method}")->store('qr-pembayaran', 'public');
            }
        }

        unset($validated['metode_pembayaran_aktif'], $validated['qr_codes']);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        Setting::set('metode_pembayaran_aktif', json_encode($metode));
        Setting::set('qr_pembayaran', json_encode($qrCodes));

        return redirect()->route('admin.settings')->with('success', '✅ Pengaturan sistem berhasil disimpan.');
    }
}
