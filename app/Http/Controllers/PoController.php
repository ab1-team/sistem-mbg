<?php

namespace App\Http\Controllers;

use App\Enums\PoStatus;
use App\Models\MenuPeriod;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PoController extends Controller
{
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with(['dapur', 'creator'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('purchase-orders.index', compact('purchaseOrders'));
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        // Audit Trail Yayasan Review (Fase 3.3)
        // Jika status draf/dikirim, ubah ke DIREVIEW_YAYASAN saat dibuka Admin
        if (in_array($purchaseOrder->status, [PoStatus::DRAF, PoStatus::DIKIRIM_KE_YAYASAN])) {
            $purchaseOrder->changeStatus(PoStatus::DIREVIEW_YAYASAN, 'Mulai proses review Yayasan');
        }

        $purchaseOrder->load(['dapur', 'menuPeriod.period', 'items.material', 'items.assignments.supplier', 'creator', 'statusHistory.user']);

        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    public function submitToSupplier(PurchaseOrder $purchaseOrder)
    {
        // 1. Validation: Ensure all items are fully assigned
        foreach ($purchaseOrder->items as $item) {
            $assigned = $item->assignments()->sum('quantity_assigned');
            if (abs($item->quantity_to_order - $assigned) > 0.001) {
                return back()->with('error', "Bahan '{$item->material->name}' belum sepenuhnya dialokasikan ke supplier.");
            }
        }

        // 2. Update Status
        $purchaseOrder->changeStatus(PoStatus::DITERUSKAN_KE_SUPPLIER, 'Alokasi supplier selesai, pesanan diteruskan.');

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Berhasil meneruskan pesanan ke Supplier.');
    }

    /**
     * Batalkan PO dengan alasan resmi.
     * Sesuai Roadmap 3.5
     */
    public function cancel(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate(['reason' => 'required|string|max:500']);

        try {
            // Simpan alasan pembatalan di field khusus (Schema 4.1)
            $purchaseOrder->update(['cancellation_reason' => $request->reason]);

            // Eksekusi transisi status via State Machine (Fase 3.2/3.3)
            $purchaseOrder->changeStatus(PoStatus::DIBATALKAN, $request->reason);

            return redirect()->route('purchase-orders.show', $purchaseOrder)->with('success', 'PO telah berhasil dibatalkan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function generateFromMenu(MenuPeriod $menuPeriod)
    {
        // 1. Validation
        if ($menuPeriod->status !== MenuPeriod::STATUS_APPROVED) {
            return back()->with('error', 'Hanya rencana menu yang sudah disetujui yang dapat dibuatkan PO.');
        }

        // Check if already has PO
        $existingPo = PurchaseOrder::where('menu_period_id', $menuPeriod->id)->first();
        if ($existingPo) {
            return redirect()->route('purchase-orders.show', $existingPo)->with('info', 'PO untuk rencana ini sudah ada.');
        }

        return DB::transaction(function () use ($menuPeriod) {
            // 2. Aggregate Requirements (Logic from MenuPeriodShow)
            $requirements = [];
            foreach ($menuPeriod->schedules as $s) {
                foreach ($s->items as $item) {
                    foreach ($item->boms as $bom) {
                        $materialId = $bom->material_id;
                        $needed = ($bom->quantity / 1) * $s->target_portions;

                        if (! isset($requirements[$materialId])) {
                            $requirements[$materialId] = [
                                'material' => $bom->material,
                                'total' => 0,
                            ];
                        }
                        $requirements[$materialId]['total'] += $needed;
                    }
                }
            }

            // 3. Create PO Header
            $poNumber = 'PO/'.now()->format('Y/m').'/'.str_pad(PurchaseOrder::count() + 1, 3, '0', STR_PAD_LEFT);

            $purchaseOrder = PurchaseOrder::create([
                'po_number' => $poNumber,
                'dapur_id' => $menuPeriod->dapur_id,
                'menu_period_id' => $menuPeriod->id,
                'status' => PoStatus::DRAF,
                'notes' => "Otomatis dari Menu: {$menuPeriod->title}",
                'created_by' => auth()->id(),
                'total_estimated_cost' => 0,
            ]);

            // Catat status DRAF awal ke histori
            $purchaseOrder->statusHistory()->create([
                'from_status' => null,
                'to_status' => PoStatus::DRAF,
                'changed_by' => auth()->id(),
                'ip_address' => request()->ip(),
            ]);

            $totalEstimatedCost = 0;

            // 4. Create PO Items
            foreach ($requirements as $id => $data) {
                $material = $data['material'];
                $qtyNeeded = $data['total'];
                $estPrice = $material->estimated_price ?? 0;
                $itemTotal = $qtyNeeded * $estPrice;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'material_id' => $id,
                    'quantity_needed' => $qtyNeeded,
                    'quantity_in_stock' => 0, // Placeholder for Phase 5
                    'quantity_to_order' => $qtyNeeded,
                    'unit' => $material->unit,
                    'estimated_unit_price' => $estPrice,
                    'item_status' => 'pending',
                ]);

                $totalEstimatedCost += $itemTotal;
            }

            // 5. Update Total Cost
            $purchaseOrder->update([
                'total_estimated_cost' => $totalEstimatedCost,
            ]);

            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('success', "PO {$poNumber} berhasil di-generate otomatis.");
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        // Handle status update specifically (State Machine logic)
        if ($request->has('status')) {
            try {
                $statusVal = is_string($request->status) ? $request->status : $request->status->value;
                $newStatus = PoStatus::from($statusVal);
                $purchaseOrder->changeStatus($newStatus, $request->reason ?? 'Penyelesaian manual melalui dashboard.');
                return redirect()->route('purchase-orders.show', $purchaseOrder)
                    ->with('success', "Status PO berhasil diperbarui menjadi {$newStatus->label()}.");
            } catch (\Exception $e) {
                return back()->with('error', $e->getMessage());
            }
        }

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Data PO berhasil diperbarui.');
    }
}
