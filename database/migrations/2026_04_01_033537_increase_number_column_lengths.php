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
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('invoice_number', 100)->change();
        });

        Schema::table('goods_receipts', function (Blueprint $table) {
            $table->string('gr_number', 100)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('invoice_number', 30)->change();
        });

        Schema::table('goods_receipts', function (Blueprint $table) {
            $table->string('gr_number', 30)->change();
        });
    }
};
