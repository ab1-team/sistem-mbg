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
        Schema::create('revenues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dapur_id')->constrained('dapurs');
            $table->foreignId('period_id')->constrained('periods');
            $table->string('reference_type')->nullable(); // e.g., 'InvoicePayment'
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->decimal('amount', 18, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revenues');
    }
};
