<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Support\CroppedImageStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SuperadminSystemSettingController extends Controller
{
    public function index(): View
    {
        $settings = SystemSetting::query()
            ->whereIn('key', [
                'cafe_name',
                'cafe_logo',
                'hero_banner_tag',
                'hero_banner_title',
                'hero_banner_desc',
                'hero_banner_button_text',
                'hero_banner_image',
            ])
            ->pluck('value', 'key');

        return view('superadmin.settings.index', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cafe_name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image'],
            'cropped_logo' => ['nullable', 'string'],
            'hero_banner_tag' => ['nullable', 'string', 'max:120'],
            'hero_banner_title' => ['nullable', 'string', 'max:255'],
            'hero_banner_desc' => ['nullable', 'string', 'max:500'],
            'hero_banner_button_text' => ['nullable', 'string', 'max:80'],
            'hero_banner_image' => ['nullable', 'image'],
        ]);

        SystemSetting::setValue('cafe_name', $validated['cafe_name']);
        SystemSetting::setValue('hero_banner_tag', trim((string) ($validated['hero_banner_tag'] ?? 'PROMO SPESIAL HARI INI')));
        SystemSetting::setValue('hero_banner_title', trim((string) ($validated['hero_banner_title'] ?? 'Diskon 50% Untuk Semua Paket Nasi Goreng')));
        SystemSetting::setValue('hero_banner_desc', trim((string) ($validated['hero_banner_desc'] ?? 'Nikmati paket lengkap dengan harga setengah. Berlaku sampai pukul 23:59 malam ini.')));
        SystemSetting::setValue('hero_banner_button_text', trim((string) ($validated['hero_banner_button_text'] ?? 'Lihat Promo')));

        if ($request->filled('cropped_logo') || $request->hasFile('logo')) {
            $oldLogo = SystemSetting::getValue('cafe_logo');

            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }

            $logoPath = $request->filled('cropped_logo')
                ? CroppedImageStore::store($request->string('cropped_logo')->toString(), 'system', 'logo')
                : $request->file('logo')->store('system', 'public');

            SystemSetting::setValue('cafe_logo', $logoPath);
        }

        if ($request->hasFile('hero_banner_image')) {
            $oldHeroImage = SystemSetting::getValue('hero_banner_image');

            if ($oldHeroImage) {
                Storage::disk('public')->delete($oldHeroImage);
            }

            $heroImagePath = $request->file('hero_banner_image')->store('system/hero', 'public');
            SystemSetting::setValue('hero_banner_image', $heroImagePath);
        }

        return redirect()->route('superadmin.settings.index')->with('status', 'Pengaturan sistem berhasil diperbarui.');
    }
}
