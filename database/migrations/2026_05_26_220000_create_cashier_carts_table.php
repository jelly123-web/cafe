<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cashier_carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique('user_id');
        });

        Schema::create('cashier_cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cashier_cart_id')->constrained('cashier_carts')->cascadeOnDelete();
            $table->foreignId('menu_id')->constrained('menus')->cascadeOnDelete();
            $table->unsignedInteger('qty');
            $table->decimal('unit_price', 14, 2);
            $table->decimal('unit_cost', 14, 2)->default(0);
            $table->timestamps();

            $table->unique(['cashier_cart_id', 'menu_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cashier_cart_items');
        Schema::dropIfExists('cashier_carts');
    }
};

