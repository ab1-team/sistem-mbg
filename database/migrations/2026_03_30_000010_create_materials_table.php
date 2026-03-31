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
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 150);
            $table->enum('category', ['sayuran', 'daging', 'ikan', 'bumbu', 'sembako', 'minuman', 'lainnya']);
            $table->string('unit', 20);
            $table->decimal('price_estimate', 15, 2)->default(0);
            $table->decimal('min_stock_threshold', 12, 3)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
