<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('promos', function (Blueprint $table) {
            $table->enum('applies_to', ['all', 'specific'])->default('all')->after('type');
        });

        // Create pivot tables for specific scoping
        Schema::create('promo_menu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promo_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_id')->constrained()->cascadeOnDelete();
        });

        Schema::create('promo_food_package', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promo_id')->constrained()->cascadeOnDelete();
            $table->foreignId('food_package_id')->constrained()->cascadeOnDelete();
        });

        // Update enum type to include more options if possible
        // Note: Enum modification can be tricky, using DB statement for compatibility
        if (config('database.default') === 'mysql') {
            DB::statement("ALTER TABLE promos MODIFY COLUMN type ENUM('percentage', 'fixed_discount', 'buy_x_get_y', 'free_shipping') DEFAULT 'percentage'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo_food_package');
        Schema::dropIfExists('promo_menu');
        Schema::table('promos', function (Blueprint $table) {
            $table->dropColumn('applies_to');
        });
        
        if (config('database.default') === 'mysql') {
            DB::statement("ALTER TABLE promos MODIFY COLUMN type ENUM('percentage', 'fixed_discount', 'buy_x_get_y') DEFAULT 'percentage'");
        }
    }
};
