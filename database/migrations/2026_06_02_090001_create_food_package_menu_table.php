<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_package_menu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('food_package_id')->constrained('food_packages')->cascadeOnDelete();
            $table->foreignId('menu_id')->constrained('menus')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['food_package_id', 'menu_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_package_menu');
    }
};
