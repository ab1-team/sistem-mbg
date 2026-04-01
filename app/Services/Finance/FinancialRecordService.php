<?php

namespace App\Services\Finance;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Period;
use App\Models\Revenue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinancialRecordService
{
    /**
     * Create a manual revenue record.
     */
    public function createRevenue(array $data): Revenue
    {
        return Revenue::create([
            'dapur_id' => $data['dapur_id'],
            'period_id' => $data['period_id'],
            'reference_type' => $data['reference_type'] ?? null,
            'reference_id' => $data['reference_id'] ?? null,
            'amount' => $data['amount'],
            'notes' => $data['notes'] ?? null,
        ]);
    }

    /**
     * Create a manual expense record.
     */
    public function createExpense(array $data): Expense
    {
        return Expense::create([
            'dapur_id' => $data['dapur_id'],
            'period_id' => $data['period_id'],
            'category' => $data['category'],
            'amount' => $data['amount'],
            'notes' => $data['notes'] ?? null,
            'attachment' => $data['attachment'] ?? null,
            'created_by' => Auth::id(),
        ]);
    }

    /**
     * Record an expense automatically from a finalized invoice.
     */
    public function recordExpenseFromInvoice(Invoice $invoice): ?Expense
    {
        // Prevent duplicate expense for the same invoice
        $exists = Expense::where('notes', 'LIKE', "%Invoice: {$invoice->invoice_number}%")->exists();
        if ($exists) {
            return null;
        }

        // Get period from invoice date or current active period
        $period = Period::where('start_date', '<=', $invoice->created_at)
            ->where('end_date', '>=', $invoice->created_at)
            ->first() ?? Period::getActive();

        if (!$period) {
            return null;
        }

        return DB::transaction(function () use ($invoice, $period) {
            return Expense::create([
                'dapur_id' => $invoice->dapur_id,
                'period_id' => $period->id,
                'category' => 'bahan_baku',
                'amount' => $invoice->grand_total,
                'notes' => "Otomatis dari Invoice: {$invoice->invoice_number}",
                'created_by' => Auth::id() ?? 1, // Default to system user if no auth
            ]);
        });
    }
}
