<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the enum list for the status column in purchase_orders table
        DB::statement("ALTER TABLE purchase_orders MODIFY COLUMN status ENUM(
            'draf', 
            'dikirim_ke_yayasan', 
            'direview_yayasan', 
            'diteruskan_ke_supplier', 
            'diproses_supplier', 
            'dalam_pengiriman', 
            'diterima_sebagian', 
            'diterima_lengkap', 
            'menunggu_verifikasi_dapur',
            'ditolak_yayasan', 
            'dibatalkan', 
            'selesai'
        ) NOT NULL DEFAULT 'draf'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE purchase_orders MODIFY COLUMN status ENUM(
            'draf', 
            'dikirim_ke_yayasan', 
            'direview_yayasan', 
            'diteruskan_ke_supplier', 
            'diproses_supplier', 
            'dalam_pengiriman', 
            'diterima_sebagian', 
            'diterima_lengkap', 
            'ditolak_yayasan', 
            'dibatalkan', 
            'selesai'
        ) NOT NULL DEFAULT 'draf'");
    }
};
