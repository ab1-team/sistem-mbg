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
        // 1. Update MenuItem table
        Schema::table('menu_items', function (Blueprint $table) {
            // First change to string temporarily if needed, but MySQL allows direct change if values are handled.
            // We use DB::statement for more control over ENUM changes.
        });

        // Mapping: sarapan, makan_siang, makan_malam, snack -> dewasa
        DB::statement("ALTER TABLE menu_items MODIFY COLUMN meal_type ENUM('anak_anak', 'dewasa', 'sarapan', 'makan_siang', 'makan_malam', 'snack')");

        DB::table('menu_items')->update(['meal_type' => 'dewasa']);

        DB::statement("ALTER TABLE menu_items MODIFY COLUMN meal_type ENUM('anak_anak', 'dewasa') NOT NULL");

        // 2. Update MenuSchedule table
        DB::statement("ALTER TABLE menu_schedules MODIFY COLUMN meal_type ENUM('anak_anak', 'dewasa', 'sarapan', 'makan_siang', 'makan_malam', 'snack')");

        DB::table('menu_schedules')->update(['meal_type' => 'dewasa']);

        DB::statement("ALTER TABLE menu_schedules MODIFY COLUMN meal_type ENUM('anak_anak', 'dewasa') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE menu_items MODIFY COLUMN meal_type ENUM('sarapan', 'makan_siang', 'makan_malam', 'snack', 'anak_anak', 'dewasa')");
        DB::statement("ALTER TABLE menu_items MODIFY COLUMN meal_type ENUM('sarapan', 'makan_siang', 'makan_malam', 'snack') NOT NULL");

        DB::statement("ALTER TABLE menu_schedules MODIFY COLUMN meal_type ENUM('sarapan', 'makan_siang', 'makan_malam', 'snack', 'anak_anak', 'dewasa')");
        DB::statement("ALTER TABLE menu_schedules MODIFY COLUMN meal_type ENUM('sarapan', 'makan_siang', 'makan_malam', 'snack') NOT NULL");
    }
};
