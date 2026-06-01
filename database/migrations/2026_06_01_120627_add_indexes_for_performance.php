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
        Schema::table('sale_transactions', function (Blueprint $table) {
            // Some might already exist from previous migrations
            if (! $this->hasIndex('sale_transactions', 'sale_transactions_branch_id_status_index')) {
                $table->index(['branch_id', 'status']);
            }
        });

        Schema::table('menus', function (Blueprint $table) {
            if (! $this->hasIndex('menus', 'menus_is_sold_out_index')) {
                $table->index('is_sold_out');
            }
        });

        Schema::table('sale_transaction_items', function (Blueprint $table) {
            if (! $this->hasIndex('sale_transaction_items', 'sale_transaction_items_sale_transaction_id_index')) {
                $table->index('sale_transaction_id');
            }
            if (! $this->hasIndex('sale_transaction_items', 'sale_transaction_items_menu_id_index')) {
                $table->index('menu_id');
            }
        });

        Schema::table('payrolls', function (Blueprint $table) {
            if (! $this->hasIndex('payrolls', 'payrolls_employee_id_index')) {
                $table->index('employee_id');
            }
            if (! $this->hasIndex('payrolls', 'payrolls_paid_at_index')) {
                $table->index('paid_at');
            }
        });
    }

    private function hasIndex($table, $indexName): bool
    {
        $conn = Schema::getConnection();
        $dbName = $conn->getDatabaseName();
        $indexes = $conn->select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
        return count($indexes) > 0;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_transactions', function (Blueprint $table) {
            if ($this->hasIndex('sale_transactions', 'sale_transactions_branch_id_status_index')) {
                $table->dropIndex(['branch_id', 'status']);
            }
        });

        Schema::table('menus', function (Blueprint $table) {
            if ($this->hasIndex('menus', 'menus_is_sold_out_index')) {
                $table->dropIndex(['is_sold_out']);
            }
        });

        Schema::table('sale_transaction_items', function (Blueprint $table) {
            if ($this->hasIndex('sale_transaction_items', 'sale_transaction_items_sale_transaction_id_index')) {
                $table->dropIndex(['sale_transaction_id']);
            }
            if ($this->hasIndex('sale_transaction_items', 'sale_transaction_items_menu_id_index')) {
                $table->dropIndex(['menu_id']);
            }
        });

        Schema::table('payrolls', function (Blueprint $table) {
            if ($this->hasIndex('payrolls', 'payrolls_employee_id_index')) {
                $table->dropIndex(['employee_id']);
            }
            if ($this->hasIndex('payrolls', 'payrolls_paid_at_index')) {
                $table->dropIndex(['paid_at']);
            }
        });
    }
};
