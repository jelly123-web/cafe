<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('base_salary', 20, 2)->default(0)->change();
            $table->decimal('allowances', 20, 2)->default(0)->change();
            $table->decimal('deductions', 20, 2)->default(0)->change();
            $table->decimal('net_salary', 20, 2)->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('base_salary', 14, 2)->default(0)->change();
            $table->decimal('allowances', 14, 2)->default(0)->change();
            $table->decimal('deductions', 14, 2)->default(0)->change();
            $table->decimal('net_salary', 14, 2)->default(0)->change();
        });
    }
};
