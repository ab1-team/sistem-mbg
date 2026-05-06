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
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->date('delivery_date')->nullable()->after('po_date');
            $table->time('delivery_time_start')->nullable()->after('delivery_date');
            $table->time('delivery_time_end')->nullable()->after('delivery_time_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['delivery_date', 'delivery_time_start', 'delivery_time_end']);
        });
    }
};
