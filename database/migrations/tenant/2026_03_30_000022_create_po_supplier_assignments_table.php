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
        Schema::create('po_supplier_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('po_item_id')->constrained('po_items')->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->foreignId('assigned_by')->constrained('users');
            $table->decimal('quantity_assigned', 12, 3);
            $table->decimal('unit_price_agreed', 15, 2)->nullable();
            $table->enum('status', ['diteruskan', 'diterima', 'ditolak', 'diproses', 'dikirim', 'selesai'])->default('diteruskan');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('po_supplier_assignments');
    }
};
