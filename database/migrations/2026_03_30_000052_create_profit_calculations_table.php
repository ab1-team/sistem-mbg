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
        Schema::create('profit_calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dapur_id')->constrained('dapurs');
            $table->foreignId('period_id')->constrained('periods');
            $table->decimal('total_revenue', 18, 2)->default(0);
            $table->decimal('total_cogs', 18, 2)->default(0); // HPP
            $table->decimal('total_expenses', 18, 2)->default(0);
            $table->decimal('gross_profit', 18, 2)->default(0);
            $table->decimal('net_profit', 18, 2)->default(0);
            $table->decimal('yayasan_share', 18, 2)->default(0);
            $table->decimal('investor_total_share', 18, 2)->default(0);
            $table->enum('status', ['draf', 'final'])->default('draf');
            $table->foreignId('calculated_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profit_calculations');
    }
};
