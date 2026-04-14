<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use Barryvdh\DomPDF\Facade\Pdf;

class KitchenInvoiceController extends Controller
{
    /**
     * Display a listing of kitchen invoices (based on POs).
     */
    public function index()
    {
        $user = auth()->user();
        $query = PurchaseOrder::with(['dapur', 'menuPeriod'])
            ->whereIn('status', ['submitted', 'processing', 'completed'])
            ->latest();

        if ($user->dapur_id) {
            $query->where('dapur_id', $user->dapur_id);
        }

        $invoices = $query->paginate(15);

        return view('finance.kitchen-invoices.index', compact('invoices'));
    }

    /**
     * Generate PDF for a specific Kitchen Invoice (Consolidated).
     */
    public function downloadPdf(PurchaseOrder $purchaseOrder)
    {
        $user = auth()->user();

        // Security Check
        if ($user->dapur_id && $purchaseOrder->dapur_id !== $user->dapur_id) {
            abort(403, 'Akses ditolak.');
        }

        $purchaseOrder->load(['dapur', 'menuPeriod', 'items.material']);

        $pdf = Pdf::loadView('finance.kitchen-invoice-pdf', [
            'po' => $purchaseOrder,
            'dapur' => $purchaseOrder->dapur,
            'items' => $purchaseOrder->items,
        ]);

        $safePoNumber = str_replace(['/', '\\'], '-', $purchaseOrder->po_number);

        return $pdf->stream("Invoice-Dapur-{$safePoNumber}.pdf");
    }
}
