<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Move existing data to pivot table
        $schedules = DB::table('menu_schedules')->whereNotNull('menu_item_id')->get();
        foreach ($schedules as $s) {
            DB::table('menu_schedule_items')->insert([
                'menu_schedule_id' => $s->id,
                'menu_item_id' => $s->menu_item_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Make menu_item_id nullable
        Schema::table('menu_schedules', function (Blueprint $table) {
            $table->unsignedBigInteger('menu_item_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menu_schedules', function (Blueprint $table) {
            $table->unsignedBigInteger('menu_item_id')->nullable(false)->change();
        });
    }
};
