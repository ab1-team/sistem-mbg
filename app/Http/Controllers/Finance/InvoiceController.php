<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Notifications\InvoicePaid;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Tampilkan daftar penagihan (Invoice) dari Supplier.
     */
    public function index()
    {
        $user = auth()->user();
        $query = Invoice::with(['purchaseOrder', 'supplier', 'dapur'])
            ->latest();

        if ($user->dapur_id) {
            $query->where('dapur_id', $user->dapur_id);
        }

        $invoices = $query->paginate(15);

        return view('invoices.index', compact('invoices'));
    }

    /**
     * Tampilkan detail penagihan dan item-itemnya.
     */
    public function show(Invoice $invoice)
    {
        $user = auth()->user();

        // Cek akses jika user terikat dapur tertentu
        if ($user->dapur_id && $invoice->dapur_id !== $user->dapur_id) {
            return redirect()->route('finance.invoices.index')->with('error', 'Anda tidak memiliki akses ke Invoice ini.');
        }

        $invoice->load(['purchaseOrder', 'supplier', 'dapur', 'items.material', 'items.poItem']);

        return view('invoices.show', compact('invoice'));
    }

    /**
     * Preview Invoice PDF di Browser.
     * Sesuai Request User (Fase 6 Extension)
     */
    public function previewPdf(Invoice $invoice)
    {
        $user = auth()->user();

        // Cek akses jika user terikat dapur tertentu
        if ($user->dapur_id && $invoice->dapur_id !== $user->dapur_id) {
            abort(403, 'Akses ditolak.');
        }

        $invoice->load(['purchaseOrder', 'supplier', 'dapur', 'items.material', 'items.poItem']);

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));

        return $pdf->stream("Invoice-{$invoice->invoice_number}.pdf");
    }

    /**
     * Verifikasi penagihan oleh Finance.
     */
    public function verify(Invoice $invoice)
    {
        $user = auth()->user();

        // Cek akses jika user terikat dapur tertentu
        if ($user->dapur_id && $invoice->dapur_id !== $user->dapur_id) {
            abort(403);
        }

        $invoice->update(['status' => 'diverifikasi']);

        return redirect()->back()->with('success', 'Penagihan telah diverifikasi.');
    }

    /**
     * Proses pembayaran penagihan.
     */
    public function pay(Request $request, Invoice $invoice)
    {
        $user = auth()->user();

        // Cek akses jika user terikat dapur tertentu
        if ($user->dapur_id && $invoice->dapur_id !== $user->dapur_id) {
            abort(403);
        }

        $request->validate([
            'payment_proof' => 'required|image|max:2048',
        ]);

        $path = $request->file('payment_proof')->store('payments', 'public');

        $invoice->update([
            'status' => 'dibayar',
            'paid_at' => now(),
            'payment_proof' => $path,
        ]);

        // Notify Supplier
        $supplierUsers = $invoice->supplier?->users;
        if ($supplierUsers) {
            foreach ($supplierUsers as $supplierUser) {
                $supplierUser->notify(new InvoicePaid($invoice));
            }
        }

        return redirect()->back()->with('success', 'Pembayaran penagihan berhasil dicatat.');
    }
}
