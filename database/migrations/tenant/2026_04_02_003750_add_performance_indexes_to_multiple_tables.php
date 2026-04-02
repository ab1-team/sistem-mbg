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
        // materials already has index(['category', 'is_active']) in current DB state
        // but not in any migration file. We'll skip it here to avoid duplication.

        Schema::table('menu_items', function (Blueprint $table) {
            $table->index(['meal_type', 'is_active'], 'idx_menu_items_perf_1');
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->index(['status', 'submitted_at'], 'idx_po_perf_1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropIndex('idx_menu_items_perf_1');
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropIndex('idx_po_perf_1');
        });
    }
};
