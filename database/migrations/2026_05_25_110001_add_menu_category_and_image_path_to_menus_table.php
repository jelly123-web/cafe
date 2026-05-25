<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->foreignId('menu_category_id')
                ->nullable()
                ->after('id')
                ->constrained('menu_categories')
                ->nullOnDelete();
            $table->string('image_path')->nullable()->after('cost_price');
        });
    }

    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropConstrainedForeignId('menu_category_id');
            $table->dropColumn('image_path');
        });
    }
};
