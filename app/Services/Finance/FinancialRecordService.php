<?php

namespace App\Services\Finance;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinancialRecordService
{
    /**
     * Create a payment record (Replacement for Revenue/Expense).
     */
    public function createPayment(array $data): Payment
    {
        return Payment::create([
            'dapur_id' => $data['dapur_id'],
            'user_id' => Auth::id() ?? 1,
            'no_pembayaran' => $data['no_pembayaran'] ?? 'PYM-'.now()->format('YmdHis'),
            'tanggal_pembayaran' => $data['tanggal_pembayaran'] ?? now(),
            'jenis_transaksi' => $data['jenis_transaksi'] ?? 'lainnya',
            'invoice_id' => $data['invoice_id'] ?? null,
            'total_harga' => $data['total_harga'],
            'catatan' => $data['catatan'] ?? null,
            'rekening_debit' => $data['rekening_debit'],
            'rekening_kredit' => $data['rekening_kredit'],
            'created_by' => Auth::id() ?? 1,
        ]);
    }

    /**
     * Record an expense automatically from a finalized invoice using the Payment model.
     * This replaces the old recordExpenseFromInvoice.
     */
    public function recordPaymentFromInvoice(Invoice $invoice, string $cashAccountCode): ?Payment
    {
        // Prevent duplicate payment for the same invoice
        $exists = Payment::where('invoice_id', $invoice->id)->exists();
        if ($exists) {
            return null;
        }

        return DB::transaction(function () use ($invoice, $cashAccountCode) {
            return $this->createPayment([
                'dapur_id' => $invoice->dapur_id,
                'jenis_transaksi' => 'pembelian_bahan',
                'invoice_id' => $invoice->id,
                'total_harga' => $invoice->grand_total,
                'catatan' => "Pembayaran otomatis dari Invoice: {$invoice->invoice_number}",
                'rekening_debit' => '5.1.01.01', // Example default: Beban Pokok Pendapatan
                'rekening_kredit' => $cashAccountCode, // Provided from settings or form
            ]);
        });
    }
}
