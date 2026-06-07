<?php

namespace App\Providers;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $cafeBrand = cache()->remember('cafe_brand_data', 3600, function () {
            $cafeName = config('app.name', 'Cafe');
            $cafeLogo = null;
            $heroTag = 'PROMO SPESIAL HARI INI';
            $heroTitle = 'Diskon 50% Untuk Semua Paket Nasi Goreng';
            $heroDesc = 'Nikmati paket lengkap dengan harga setengah. Berlaku sampai pukul 23:59 malam ini.';
            $heroButtonText = 'Lihat Promo';
            $heroImage = null;

            $host = (string) config('database.connections.mysql.host', '127.0.0.1');
            $port = (int) config('database.connections.mysql.port', 3306);
            $dbReachable = @fsockopen($host, $port, $errno, $errstr, 0.2);
            
            if (is_resource($dbReachable)) {
                fclose($dbReachable);
                try {
                    $cafeName = SystemSetting::getValue('cafe_name', $cafeName);
                    $cafeLogo = SystemSetting::getValue('cafe_logo');
                    $heroTag = SystemSetting::getValue('hero_banner_tag', $heroTag);
                    $heroTitle = SystemSetting::getValue('hero_banner_title', $heroTitle);
                    $heroDesc = SystemSetting::getValue('hero_banner_desc', $heroDesc);
                    $heroButtonText = SystemSetting::getValue('hero_banner_button_text', $heroButtonText);
                    $heroImage = SystemSetting::getValue('hero_banner_image');
                } catch (Throwable) {
                    // fallback
                }
            }

            return [
                'name' => $cafeName,
                'logo' => $cafeLogo,
                'logo_url' => $cafeLogo ? '/brand-logo?v=' . rawurlencode($cafeLogo) : null,
                'hero' => [
                    'tag' => $heroTag,
                    'title' => $heroTitle,
                    'desc' => $heroDesc,
                    'button_text' => $heroButtonText,
                    'image' => $heroImage,
                    'image_url' => $heroImage ? Storage::disk('public')->url($heroImage) : null,
                ],
            ];
        });

        View::share('cafeBrand', $cafeBrand);
        View::share('publicHero', $cafeBrand['hero'] ?? []);
    }
}
