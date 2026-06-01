<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE inventory_movements MODIFY COLUMN type ENUM('in','out','opname') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE inventory_movements MODIFY COLUMN type ENUM('in','out') NOT NULL");
    }
};

