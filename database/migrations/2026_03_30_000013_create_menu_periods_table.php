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
        Schema::create('menu_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dapur_id')->constrained('dapurs');
            $table->foreignId('period_id')->constrained('periods');
            $table->string('title', 150);
            $table->enum('status', ['draf', 'menunggu_approval', 'disetujui', 'ditolak'])->default('draf');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_note')->nullable();
            $table->timestamps();

            $table->index(['dapur_id', 'period_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_periods');
    }
};
