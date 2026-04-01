<?php

namespace App\Http\Controllers\Supplier;

use App\Enums\PoStatus;
use App\Http\Controllers\Controller;
use App\Models\PoSupplierAssignment;
use Illuminate\Http\Request;

class PoController extends Controller
{
    public function index()
    {
        $supplierId = auth()->user()->supplier_id;

        if (! $supplierId) {
            return redirect()->route('dashboard')->with('error', 'Profil user Anda tidak terikat dengan supplier manapun.');
        }

        $assignments = PoSupplierAssignment::where('supplier_id', $supplierId)
            ->with(['item.purchaseOrder', 'item.material'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('supplier.purchase-orders.index', compact('assignments'));
    }

    public function show(PoSupplierAssignment $purchaseOrder) // Route model binding using assignment ID
    {
        $assignment = $purchaseOrder; // Rename for clarity
        $supplierId = auth()->user()->supplier_id;

        if ($assignment->supplier_id !== $supplierId) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        $assignment->load(['item.purchaseOrder', 'item.material']);

        return view('supplier.purchase-orders.show', compact('assignment'));
    }

    public function respond(Request $request, PoSupplierAssignment $assignment)
    {
        $supplierId = auth()->user()->supplier_id;

        if ($assignment->supplier_id !== $supplierId) {
            abort(403);
        }

        $request->validate([
            'action' => 'required|in:accept,reject,process,ship',
            'rejection_reason' => 'required_if:action,reject|nullable|string|min:5',
        ]);

        $statusMap = [
            'accept' => 'diterima',
            'reject' => 'ditolak',
            'process' => 'diproses',
            'ship' => 'dikirim',
        ];

        $assignment->update([
            'status' => $statusMap[$request->action],
            'rejection_reason' => $request->action === 'reject' ? $request->rejection_reason : $assignment->rejection_reason,
            'responded_at' => in_array($request->action, ['accept', 'reject']) ? now() : $assignment->responded_at,
            'shipped_at' => $request->action === 'ship' ? now() : $assignment->shipped_at,
        ]);

        // Sync main PO status based on Supplier action (Roadmap 3.4/State Machine)
        $po = $assignment->item->purchaseOrder;

        if ($request->action === 'accept' || $request->action === 'process') {
            $po->changeStatus(PoStatus::DIPROSES_SUPPLIER, "Supplier ({$assignment->supplier->name}) menerima/memproses item: {$assignment->item->material->name}");
        } elseif ($request->action === 'ship') {
            $po->changeStatus(PoStatus::DALAM_PENGIRIMAN, "Supplier ({$assignment->supplier->name}) mengirim item: {$assignment->item->material->name}");
        } elseif ($request->action === 'reject') {
            $po->changeStatus(PoStatus::DIREVIEW_YAYASAN, "Supplier ({$assignment->supplier->name}) MENOLAK item: {$assignment->item->material->name}. Alasan: {$request->rejection_reason}");
        }

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }
}
