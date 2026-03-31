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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dapur_id')->constrained('dapurs');
            $table->foreignId('period_id')->constrained('periods');
            $table->string('category', 50); // e.g., 'Gaji', 'Listrik', 'Sewa'
            $table->decimal('amount', 18, 2);
            $table->text('notes')->nullable();
            $table->string('attachment')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
