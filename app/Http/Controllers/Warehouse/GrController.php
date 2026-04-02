<?php

namespace App\Http\Controllers\Warehouse;

use App\Enums\PoStatus;
use App\Http\Controllers\Controller;
use App\Models\GoodsReceipt;
use App\Models\PurchaseOrder;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GrController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Tampilkan daftar PO yang siap/sedang diterima.
     */
    public function index()
    {
        $user = auth()->user();
        $query = PurchaseOrder::whereIn('status', [
            PoStatus::DITERUSKAN_KE_SUPPLIER,
            PoStatus::DIPROSES_SUPPLIER,
            PoStatus::DALAM_PENGIRIMAN,
            PoStatus::DITERIMA_SEBAGIAN,
        ])->latest();

        if ($user->dapur_id) {
            $query->where('dapur_id', $user->dapur_id);
        }

        $purchaseOrders = $query->paginate(10);

        return view('gr.index', compact('purchaseOrders'));
    }

    /**
     * Form penerimaan barang untuk PO spesifik.
     */
    public function create(PurchaseOrder $purchaseOrder)
    {
        $user = auth()->user();

        // Cek akses jika user terikat dapur tertentu
        if ($user->dapur_id && $purchaseOrder->dapur_id !== $user->dapur_id) {
            return redirect()->route('gr.index')->with('error', 'Anda tidak memiliki akses ke Purchase Order ini.');
        }

        // Pastikan status PO valid untuk diterima
        $allowedStatuses = [
            PoStatus::DITERUSKAN_KE_SUPPLIER,
            PoStatus::DIPROSES_SUPPLIER,
            PoStatus::DALAM_PENGIRIMAN,
            PoStatus::DITERIMA_SEBAGIAN,
        ];

        if (! in_array($purchaseOrder->status, $allowedStatuses)) {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'Status PO tidak mengizinkan penerimaan barang saat ini.');
        }

        $purchaseOrder->load('items.material', 'items.assignments.supplier');

        return view('gr.create', compact('purchaseOrder'));
    }

    /**
     * Simpan data penerimaan barang dan update stok.
     */
    public function store(Request $request, PurchaseOrder $purchaseOrder)
    {
        $user = auth()->user();

        // Cek akses jika user terikat dapur tertentu
        if ($user->dapur_id && $purchaseOrder->dapur_id !== $user->dapur_id) {
            abort(403, 'Anda tidak memiliki akses ke Purchase Order ini.');
        }

        $validated = $request->validate([
            'received_at' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array',
            'items.*.po_item_id' => 'required|exists:po_items,id',
            'items.*.quantity_received' => 'required|numeric|min:0',
            'items.*.qc_status' => 'required|in:sesuai,kurang,rusak,retur',
            'items.*.qc_notes' => 'nullable|string',
            'items.*.qc_photo' => 'nullable|image|max:2048',
        ]);

        return DB::transaction(function () use ($validated, $purchaseOrder) {
            // 1. Buat Header Penerimaan
            $gr = GoodsReceipt::create([
                'gr_number' => 'GR-'.$purchaseOrder->dapur->code.'-'.now()->format('Ymd-His'),
                'purchase_order_id' => $purchaseOrder->id,
                'supplier_id' => $purchaseOrder->items->first()->assignments->first()->supplier_id ?? 1,
                'received_by' => auth()->id(),
                'received_at' => $validated['received_at'],
                'notes' => $validated['notes'],
            ]);

            $allCompleted = true;

            foreach ($validated['items'] as $itemData) {
                $poItem = $purchaseOrder->items()->find($itemData['po_item_id']);

                $photoPath = null;
                if (isset($itemData['qc_photo'])) {
                    $photoPath = $itemData['qc_photo']->store('qc_photos', 'public');
                }

                // 2. Buat Detail Penerimaan
                $grItem = $gr->items()->create([
                    'po_item_id' => $poItem->id,
                    'material_id' => $poItem->material_id,
                    'quantity_ordered' => $poItem->quantity_to_order,
                    'quantity_received' => $itemData['quantity_received'],
                    'unit' => $poItem->unit,
                    'qc_status' => $itemData['qc_status'],
                    'qc_notes' => $itemData['qc_notes'],
                    'qc_photo' => $photoPath,
                ]);

                // 3. Update Stok via Service (Jika Sesuai/Kurang)
                if (in_array($itemData['qc_status'], ['sesuai', 'kurang'])) {
                    $this->stockService->recordIngress(
                        $purchaseOrder->dapur,
                        $poItem->material,
                        (float) $itemData['quantity_received'],
                        $gr,
                        "Penerimaan item {$poItem->material->name} via QC {$gr->gr_number}"
                    );
                }

                // 4. Update qty diterima di level item PO (kumulatif untuk cek completion)
                $poItem->increment('quantity_received', $itemData['quantity_received']);

                if ($poItem->quantity_received < $poItem->quantity_to_order) {
                    $allCompleted = false;
                }
            }

            // 5. Update Status PO
            $newStatus = $allCompleted ? PoStatus::DITERIMA_LENGKAP : PoStatus::DITERIMA_SEBAGIAN;
            $purchaseOrder->changeStatus($newStatus, "Penerimaan barang real-time via {$gr->gr_number}");

            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('success', "Penerimaan {$gr->gr_number} berhasil diproses. Stok dapur diperbarui.");
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(GoodsReceipt $goodsReceipt)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GoodsReceipt $goodsReceipt)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GoodsReceipt $goodsReceipt)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GoodsReceipt $goodsReceipt)
    {
        //
    }
}
