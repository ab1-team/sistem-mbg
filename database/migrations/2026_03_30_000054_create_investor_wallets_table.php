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
        Schema::create('investor_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investor_id')->unique()->constrained('investors');
            $table->decimal('balance', 18, 2)->default(0);
            $table->timestamp('last_transaction_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investor_wallets');
    }
};
