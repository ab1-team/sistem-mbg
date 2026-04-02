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
        Schema::create('po_item_sub_suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('po_supplier_assignment_id')->constrained('po_supplier_assignments')->onDelete('cascade');
            $table->string('sub_supplier_name', 150);
            $table->decimal('quantity_sourced', 12, 3);
            $table->decimal('buying_price', 15, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('po_item_sub_suppliers');
    }
};
