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
        Schema::create('menu_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_period_id')->constrained('menu_periods');
            $table->foreignId('menu_item_id')->constrained('menu_items');
            $table->date('serve_date');
            $table->enum('meal_type', ['sarapan', 'makan_siang', 'makan_malam', 'snack']);
            $table->integer('target_portions')->unsigned();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['menu_period_id', 'serve_date', 'meal_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_schedules');
    }
};
