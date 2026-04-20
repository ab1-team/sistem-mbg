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
        Schema::table('po_supplier_assignments', function (Blueprint $table) {
            $table->foreignId('sub_supplier_id')->nullable()->after('supplier_id')->constrained('sub_suppliers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('po_supplier_assignments', function (Blueprint $table) {
            $table->dropForeign(['sub_supplier_id']);
            $table->dropColumn('sub_supplier_id');
        });
    }
};
