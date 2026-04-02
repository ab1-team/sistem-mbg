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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dapur_id')->constrained('dapurs');
            $table->foreignId('material_id')->constrained('materials');
            $table->decimal('current_stock', 12, 3)->default(0);
            $table->decimal('min_threshold', 12, 3)->default(0);
            $table->timestamp('last_stock_take_at')->nullable();
            $table->timestamps();

            $table->unique(['dapur_id', 'material_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
