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
        Schema::table('goods_receipt_items', function (Blueprint $table) {
            $table->enum('qc_status', ['sesuai', 'kurang', 'rusak', 'retur'])->after('unit')->default('sesuai');
            $table->text('qc_notes')->nullable()->after('qc_status');
            $table->string('qc_photo')->nullable()->after('qc_notes');
            
            // Drop old simple rejection columns
            $table->dropColumn(['is_rejected', 'rejection_reason']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            // Re-type status to Roadmap specs
            $table->string('status')->default('generated')->change();
        });
    }

    public function down(): void
    {
        Schema::table('goods_receipt_items', function (Blueprint $table) {
            $table->boolean('is_rejected')->default(false);
            $table->text('rejection_reason')->nullable();
            $table->dropColumn(['qc_status', 'qc_notes', 'qc_photo']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('status', ['pending', 'paid', 'cancelled', 'overdue'])->default('pending')->change();
        });
    }
};
