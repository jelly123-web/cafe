<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            if (! Schema::hasColumn('menus', 'barcode')) {
                $table->string('barcode')->nullable()->unique()->after('code');
            }
        });

        Schema::table('inventory_items', function (Blueprint $table) {
            if (! Schema::hasColumn('inventory_items', 'barcode')) {
                $table->string('barcode')->nullable()->unique()->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            if (Schema::hasColumn('inventory_items', 'barcode')) {
                $table->dropUnique('inventory_items_barcode_unique');
                $table->dropColumn('barcode');
            }
        });

        Schema::table('menus', function (Blueprint $table) {
            if (Schema::hasColumn('menus', 'barcode')) {
                $table->dropUnique('menus_barcode_unique');
                $table->dropColumn('barcode');
            }
        });
    }
};
