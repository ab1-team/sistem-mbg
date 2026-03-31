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
        Schema::create('po_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->onDelete('cascade');
            $table->foreignId('material_id')->constrained('materials');
            $table->decimal('quantity_needed', 12, 3);
            $table->decimal('quantity_in_stock', 12, 3)->default(0);
            $table->decimal('quantity_to_order', 12, 3);
            $table->string('unit', 20);
            $table->decimal('estimated_unit_price', 15, 2)->default(0);
            $table->decimal('actual_unit_price', 15, 2)->nullable();
            $table->decimal('quantity_received', 12, 3)->default(0);
            $table->enum('item_status', ['pending', 'diteruskan', 'diterima_supplier', 'ditolak_supplier', 'dikirim', 'diterima_gudang', 'retur'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index('purchase_order_id', 'idx_po_items_po');
            $table->index('material_id', 'idx_po_items_material');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('po_items');
    }
};
