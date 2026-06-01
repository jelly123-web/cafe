<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_transactions', function (Blueprint $table) {
            $table->timestamp('paid_at')->nullable()->after('cancelled_by');
            $table->string('payment_method')->nullable()->after('paid_at');
        });
    }

    public function down(): void
    {
        Schema::table('sale_transactions', function (Blueprint $table) {
            $table->dropColumn(['paid_at', 'payment_method']);
        });
    }
};
