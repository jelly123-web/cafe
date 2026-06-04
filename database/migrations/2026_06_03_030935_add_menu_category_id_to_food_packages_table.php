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
        Schema::table('food_packages', function (Blueprint $table) {
            $table->foreignId('menu_category_id')->nullable()->constrained('menu_categories')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('food_packages', function (Blueprint $table) {
            $table->dropForeign(['menu_category_id']);
            $table->dropColumn('menu_category_id');
        });
    }
};
