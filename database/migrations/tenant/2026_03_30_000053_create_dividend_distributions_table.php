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
        Schema::create('dividend_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profit_calculation_id')->constrained('profit_calculations')->onDelete('cascade');
            $table->foreignId('investor_id')->constrained('investors');
            $table->decimal('share_percentage', 5, 2);
            $table->decimal('amount', 18, 2);
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dividend_distributions');
    }
};
