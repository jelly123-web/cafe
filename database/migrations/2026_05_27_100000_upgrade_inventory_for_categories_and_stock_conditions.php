<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('unit', ['kg', 'pcs']);
            $table->timestamps();
        });

        Schema::table('inventory_items', function (Blueprint $table) {
            $table->foreignId('inventory_category_id')->nullable()->after('id')->constrained('inventory_categories')->nullOnDelete();
            $table->decimal('stock_good', 14, 2)->default(0)->after('min_stock');
            $table->decimal('stock_less_good', 14, 2)->default(0)->after('stock_good');
            $table->decimal('stock_damaged', 14, 2)->default(0)->after('stock_less_good');
        });

        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->string('stock_condition', 30)->nullable()->after('type');
            $table->string('to_stock_condition', 30)->nullable()->after('stock_condition');
            $table->string('usage_title')->nullable()->after('qty');
        });
    }

    public function down(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->dropColumn(['stock_condition', 'to_stock_condition', 'usage_title']);
        });

        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('inventory_category_id');
            $table->dropColumn(['stock_good', 'stock_less_good', 'stock_damaged']);
        });

        Schema::dropIfExists('inventory_categories');
    }
};

