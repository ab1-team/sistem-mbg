<?php

namespace App\Observers;

use App\Enums\PoStatus;
use App\Models\PurchaseOrder;
use App\Models\User;
use App\Services\InvoiceService;
use Filament\Notifications\Notification;

class PurchaseOrderObserver
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Handle the PurchaseOrder "updated" event.
     */
    public function updated(PurchaseOrder $purchaseOrder): void
    {
        // Trigger Invoicing Otomatis (Roadmap 4.2)
        // Jika status berubah menjadi DITERIMA_LENGKAP
        if ($purchaseOrder->isDirty('status') && $purchaseOrder->status === PoStatus::DITERIMA_LENGKAP) {
            // Pastikan belum ada invoice untuk PO ini agar tidak duplikat
            if ($purchaseOrder->invoices()->count() === 0) {
                $this->invoiceService->generateFromPo($purchaseOrder);

                // Notify Finance (Roadmap 4.2)
                $finances = User::role('finance_yayasan')->get();
                foreach ($finances as $finance) {
                    Notification::make()
                        ->title('Invoice Baru Tersedia')
                        ->body("PO {$purchaseOrder->po_number} telah diterima lengkap. Invoice otomatis telah dibuat.")
                        ->success()
                        ->sendToDatabase($finance);
                }
            }
        }
    }
}
