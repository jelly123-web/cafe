<?php

namespace App\Providers;

use App\Models\SystemSetting;
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

            $host = (string) config('database.connections.mysql.host', '127.0.0.1');
            $port = (int) config('database.connections.mysql.port', 3306);
            $dbReachable = @fsockopen($host, $port, $errno, $errstr, 0.2);
            
            if (is_resource($dbReachable)) {
                fclose($dbReachable);
                try {
                    $cafeName = SystemSetting::getValue('cafe_name', $cafeName);
                    $cafeLogo = SystemSetting::getValue('cafe_logo');
                } catch (Throwable) {
                    // fallback
                }
            }

            return [
                'name' => $cafeName,
                'logo' => $cafeLogo,
                'logo_url' => $cafeLogo ? '/brand-logo?v=' . rawurlencode($cafeLogo) : null,
            ];
        });

        View::share('cafeBrand', $cafeBrand);
    }
}
