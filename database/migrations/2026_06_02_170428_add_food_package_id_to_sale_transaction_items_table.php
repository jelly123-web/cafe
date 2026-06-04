<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sale_transaction_items', function (Blueprint $table) {
            $table->foreignId('food_package_id')->nullable()->after('menu_id')->constrained('food_packages')->nullOnDelete();
            $table->foreignId('menu_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_transaction_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('food_package_id');
            $table->foreignId('menu_id')->nullable(false)->change();
        });
    }
};
