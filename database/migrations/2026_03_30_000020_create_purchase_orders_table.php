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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number', 30)->unique();
            $table->foreignId('dapur_id')->constrained('dapurs');
            $table->foreignId('menu_period_id')->nullable()->constrained('menu_periods');
            $table->enum('status', ['draf', 'dikirim_ke_yayasan', 'direview_yayasan', 'diteruskan_ke_supplier', 'diproses_supplier', 'dalam_pengiriman', 'diterima_sebagian', 'diterima_lengkap', 'ditolak_yayasan', 'dibatalkan', 'selesai'])->default('draf');
            $table->decimal('total_estimated_cost', 18, 2)->default(0);
            $table->decimal('total_actual_cost', 18, 2)->default(0);
            $table->text('notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
