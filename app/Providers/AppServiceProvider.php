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
        try {
            $cafeName = SystemSetting::getValue('cafe_name', config('app.name', 'Cafe'));
            $cafeLogo = SystemSetting::getValue('cafe_logo');
        } catch (Throwable) {
            $cafeName = config('app.name', 'Cafe');
            $cafeLogo = null;
        }

        View::share('cafeBrand', [
            'name' => $cafeName,
            'logo' => $cafeLogo,
            'logo_url' => $cafeLogo ? Storage::disk('public')->url($cafeLogo) : null,
        ]);
    }
}
