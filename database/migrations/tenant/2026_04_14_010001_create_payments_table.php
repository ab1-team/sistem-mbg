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
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('dapur_id')->nullable()->constrained('dapurs')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->string('no_pembayaran')->unique();
            $table->dateTime('tanggal_pembayaran');
            $table->enum('jenis_transaksi', [
                'pembelian_bahan',
                'operasional_dapur',
                'gaji_staf',
                'distribusi_dividen',
                'pendapatan_dana',
                'lainnya',
            ])->default('lainnya');

            // Reference to other specific tables
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->foreignId('withdrawal_request_id')->nullable()->constrained('withdrawal_requests')->nullOnDelete();

            $table->decimal('total_harga', 20, 2);
            $table->string('metode_pembayaran')->nullable();
            $table->string('no_referensi')->nullable();
            $table->string('payment_proof')->nullable();
            $table->text('catatan')->nullable();

            // Accounting codes (stored as strings as requested)
            $table->string('rekening_debit', 20);
            $table->string('rekening_kredit', 20);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
