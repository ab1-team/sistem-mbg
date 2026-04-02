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
        Schema::table('materials', function (Blueprint $table) {
            $table->decimal('calories', 10, 2)->default(0)->after('unit');
            $table->decimal('protein', 10, 2)->default(0)->after('calories');
            $table->decimal('carbs', 10, 2)->default(0)->after('protein');
            $table->decimal('fat', 10, 2)->default(0)->after('carbs');
            $table->decimal('fiber', 10, 2)->default(0)->after('fat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn(['calories', 'protein', 'carbs', 'fat', 'fiber']);
        });
    }
};
