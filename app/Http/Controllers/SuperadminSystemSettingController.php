<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SuperadminSystemSettingController extends Controller
{
    public function index(): View
    {
        $settings = SystemSetting::query()
            ->whereIn('key', ['cafe_name', 'cafe_logo'])
            ->pluck('value', 'key');

        return view('superadmin.settings.index', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cafe_name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        SystemSetting::setValue('cafe_name', $validated['cafe_name']);

        if ($request->hasFile('logo')) {
            $oldLogo = SystemSetting::getValue('cafe_logo');

            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }

            $logoPath = $request->file('logo')->store('system', 'public');
            SystemSetting::setValue('cafe_logo', $logoPath);
        }

        return redirect()->route('superadmin.settings.index')->with('status', 'Pengaturan sistem berhasil diperbarui.');
    }
}
