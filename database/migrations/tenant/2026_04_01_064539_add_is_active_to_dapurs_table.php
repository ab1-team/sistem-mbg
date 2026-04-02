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
        if (! Schema::hasColumn('dapurs', 'is_active')) {
            Schema::table('dapurs', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('capacity_portions');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('dapurs', 'is_active')) {
            Schema::table('dapurs', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }
    }
};
