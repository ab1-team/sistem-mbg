<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Tampilkan daftar penagihan (Invoice) dari Supplier.
     */
    public function index()
    {
        $invoices = Invoice::with(['purchaseOrder', 'supplier', 'dapur'])
            ->latest()
            ->paginate(15);

        return view('invoices.index', compact('invoices'));
    }

    /**
     * Tampilkan detail penagihan dan item-itemnya.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['purchaseOrder', 'supplier', 'dapur', 'items.material', 'items.poItem']);

        return view('invoices.show', compact('invoice'));
    }

    /**
     * Cetak Invoice ke PDF.
     * Sesuai Roadmap 4.3
     */
    public function downloadPdf(Invoice $invoice)
    {
        $invoice->load(['purchaseOrder', 'supplier', 'dapur', 'items.material', 'items.poItem']);

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));

        return $pdf->download("Invoice-{$invoice->invoice_number}.pdf");
    }

    /**
     * Verifikasi penagihan oleh Finance.
     */
    public function verify(Invoice $invoice)
    {
        $invoice->update(['status' => 'diverifikasi']);

        return redirect()->back()->with('success', 'Penagihan telah diverifikasi.');
    }

    /**
     * Proses pembayaran penagihan.
     */
    public function pay(Request $request, Invoice $invoice)
    {
        $request->validate([
            'payment_proof' => 'required|image|max:2048',
        ]);

        $path = $request->file('payment_proof')->store('payments', 'public');

        $invoice->update([
            'status' => 'dibayar',
            'paid_at' => now(),
            'payment_proof' => $path,
        ]);

        return redirect()->back()->with('success', 'Pembayaran penagihan berhasil dicatat.');
    }
}
