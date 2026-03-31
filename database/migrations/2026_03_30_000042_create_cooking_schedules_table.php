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
        Schema::create('cooking_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_schedule_id')->constrained('menu_schedules');
            $table->foreignId('dapur_id')->constrained('dapurs');
            $table->enum('status', ['pending', 'cooking', 'done'])->default('pending');
            $table->timestamp('cooked_at')->nullable();
            $table->foreignId('cooked_by')->nullable()->constrained('users');
            $table->integer('portions_cooked')->unsigned()->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cooking_schedules');
    }
};
