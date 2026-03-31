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
        Schema::create('goods_receipt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goods_receipt_id')->constrained('goods_receipts')->onDelete('cascade');
            $table->foreignId('po_item_id')->constrained('po_items');
            $table->foreignId('material_id')->constrained('materials');
            $table->decimal('quantity_ordered', 12, 3);
            $table->decimal('quantity_received', 12, 3);
            $table->string('unit', 20);
            $table->boolean('is_rejected')->default(false);
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_receipt_items');
    }
};
