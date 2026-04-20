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
        // Add fulfillment columns to assignments
        Schema::table('po_supplier_assignments', function (Blueprint $table) {
            $table->decimal('quantity_received', 12, 3)->default(0)->after('quantity_assigned');
            $table->boolean('is_fulfillment_closed')->default(false)->after('status');
        });

        // Add verification columns to POs
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->timestamp('verified_at')->nullable()->after('status');
            $table->foreignId('verified_by')->nullable()->constrained('users')->after('verified_at');
        });

        // Link GR items to assignments for precise tracking
        Schema::table('goods_receipt_items', function (Blueprint $table) {
            $table->foreignId('po_supplier_assignment_id')->nullable()->constrained('po_supplier_assignments')->after('po_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goods_receipt_items', function (Blueprint $table) {
            $table->dropForeign(['po_supplier_assignment_id']);
            $table->dropColumn('po_supplier_assignment_id');
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn(['verified_at', 'verified_by']);
        });

        Schema::table('po_supplier_assignments', function (Blueprint $table) {
            $table->dropColumn(['quantity_received', 'is_fulfillment_closed']);
        });
    }
};
