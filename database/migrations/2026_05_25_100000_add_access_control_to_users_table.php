<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('role');
            $table->json('permissions')->nullable()->after('is_active');
        });

        DB::table('users')->where('role', 'superadmin')->update([
            'is_active' => true,
            'permissions' => json_encode([
                'view_dashboard' => true,
                'view_sales' => true,
                'manage_menus' => true,
                'manage_branches' => true,
                'manage_users' => true,
                'manage_orders' => true,
                'view_all_orders' => true,
                'cancel_orders' => true,
                'order_history' => true,
                'monitor_orders_realtime' => true,
            ]),
        ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'permissions']);
        });
    }
};
