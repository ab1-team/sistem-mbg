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
        Schema::table('cooking_schedules', function (Blueprint $table) {
            // Ubah enum status ke string agar lebih fleksibel dengan Enum PHP
            $table->string('status')->default('belum_mulai')->change();

            // Tambahkan timestamp detail sesuai Roadmap 5.2
            $table->timestamp('started_at')->nullable()->after('status');
            $table->timestamp('completed_at')->nullable()->after('started_at');
            $table->timestamp('distributed_at')->nullable()->after('completed_at');

            // Rename cooked_at ke completed_at (opsional, tapi agar konsisten)
            // Namun karena cooked_at sudah ada, kita bisa biarkan atau hapus nanti.
            // Untuk amannya kita biarkan cooked_at sebagai legacy atau alias.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cooking_schedules', function (Blueprint $table) {
            $table->dropColumn(['started_at', 'completed_at', 'distributed_at']);
            $table->enum('status', ['pending', 'cooking', 'done'])->default('pending')->change();
        });
    }
};
