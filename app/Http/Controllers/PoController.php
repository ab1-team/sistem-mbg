<?php

namespace App\Http\Controllers;

use App\Enums\PoStatus;
use App\Imports\PoItemsImport;
use App\Imports\PurchaseOrderBulkImport;
use App\Models\Dapur;
use App\Models\MenuPeriod;
use App\Models\PoSupplierAssignment;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\User;
use App\Notifications\NewPOAssigned;
use App\Services\InvoiceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PoController extends Controller
{
    public function index()
    {
        return view('purchase-orders.index');
    }

    public function create()
    {
        $user = auth()->user();
        $dapurs = $user->dapur_id ? collect([$user->dapur]) : Dapur::where('is_active', true)->orderBy('name')->get();

        return view('purchase-orders.create', compact('dapurs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'dapur_id' => 'required|exists:dapurs,id',
            'po_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = auth()->user();
        if ($user->dapur_id && $request->dapur_id != $user->dapur_id) {
            return back()->with('error', 'Anda hanya dapat membuat PO untuk dapur Anda sendiri.');
        }

        $poDate = Carbon::parse($request->po_date);
        $poNumber = 'PO/'.$poDate->format('Y/m').'/'.str_pad(PurchaseOrder::count() + 1, 3, '0', STR_PAD_LEFT);

        $purchaseOrder = PurchaseOrder::create([
            'po_number' => $poNumber,
            'po_date' => $poDate,
            'dapur_id' => $request->dapur_id,
            'status' => PoStatus::DRAF,
            'notes' => $request->notes,
            'created_by' => auth()->id(),
            'total_estimated_cost' => 0,
        ]);

        // Catat histori awal
        $purchaseOrder->statusHistory()->create([
            'from_status' => null,
            'to_status' => PoStatus::DRAF,
            'changed_by' => auth()->id(),
            'ip_address' => request()->ip(),
            'reason' => 'Pembuatan PO manual',
        ]);

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', "PO {$poNumber} berhasil dibuat secara manual.");
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $user = auth()->user();

        // Cek akses jika user terikat dapur tertentu
        if ($user->dapur_id && $purchaseOrder->dapur_id !== $user->dapur_id) {
            return redirect()->route('purchase-orders.index')->with('error', 'Anda tidak memiliki akses ke Purchase Order ini.');
        }

        // Audit Trail Yayasan Review (Fase 3.3)
        // Hanya ubah ke DIREVIEW_YAYASAN jika statusnya DIKIRIM_KE_YAYASAN (bukan DRAF)
        // dan yang membuka adalah admin/superadmin
        if ($purchaseOrder->status === PoStatus::DIKIRIM_KE_YAYASAN && auth()->user()->hasRole(['admin_yayasan', 'superadmin'])) {
            $purchaseOrder->changeStatus(PoStatus::DIREVIEW_YAYASAN, 'Mulai proses review Yayasan');
        }

        $purchaseOrder->load(['dapur', 'menuPeriod.period', 'items.material', 'items.assignments.supplier', 'items.assignments.subSupplier', 'creator', 'statusHistory.user']);

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

        // Notify Suppliers
        // Ambil semua supplier dari assignment di PO ini
        $supplierIds = PoSupplierAssignment::whereIn('po_item_id', $purchaseOrder->items->pluck('id'))
            ->whereNotNull('supplier_id')
            ->pluck('supplier_id')
            ->unique();

        $suppliers = User::whereIn('supplier_id', $supplierIds)
            ->get();

        foreach ($suppliers as $supplier) {
            $supplier->notify(new NewPOAssigned($purchaseOrder));
        }

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

    public function verify(Request $request, PurchaseOrder $purchaseOrder)
    {
        try {
            if ($purchaseOrder->status !== PoStatus::SELESAI) {
                $purchaseOrder->changeStatus(PoStatus::SELESAI, $request->notes ?: 'Diverifikasi oleh Kepala Dapur.');
                $purchaseOrder->update([
                    'verified_at' => now(),
                    'verified_by' => auth()->id(),
                ]);
            } else {
                // Retry generation if previously failed but status is already SELESAI
                if ($purchaseOrder->invoices()->count() === 0) {
                    app(InvoiceService::class)->generateFromPo($purchaseOrder);
                }
            }

            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('success', 'PO berhasil diverifikasi. Invoice telah diterbitkan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function markDeficit(Request $request, PurchaseOrder $purchaseOrder)
    {
        try {
            foreach ($purchaseOrder->items as $item) {
                foreach ($item->assignments as $assignment) {
                    $assignment->update(['is_fulfillment_closed' => true]);
                }
            }

            $purchaseOrder->changeStatus(PoStatus::DITERIMA_SEBAGIAN, 'Terdeteksi defisit oleh Kepala Dapur. Menunggu alokasi susulan.');

            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('warning', 'Defisit dilaporkan. Anda dapat mengalokasikan kekurangan ke supplier lain.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function generateFromMenu(MenuPeriod $menuPeriod)
    {
        $user = auth()->user();

        // Cek akses jika user terikat dapur tertentu
        if ($user->dapur_id && $menuPeriod->dapur_id !== $user->dapur_id) {
            return back()->with('error', 'Anda tidak memiliki akses ke rencana menu ini.');
        }

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
            // Eager load to avoid N+1 in loops
            $menuPeriod->load(['schedules.items.boms.material']);

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
            $poDate = now();
            $poNumber = 'PO/'.$poDate->format('Y/m').'/'.str_pad(PurchaseOrder::count() + 1, 3, '0', STR_PAD_LEFT);

            $purchaseOrder = PurchaseOrder::create([
                'po_number' => $poNumber,
                'po_date' => $poDate,
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

    /**
     * Import items from excel/csv to a specific PO.
     */
    public function importItems(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            Excel::import(new PoItemsImport($purchaseOrder), $request->file('file'));

            // Recalculate total after import
            $purchaseOrder->recalculateTotal();

            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('success', 'Item berhasil di-import ke Purchase Order.');
        } catch (\Exception $e) {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'Gagal mengimport item: '.$e->getMessage());
        }
    }

    /**
     * Show form for bulk PO import.
     */
    public function bulkImport()
    {
        $user = auth()->user();
        $dapurs = $user->dapur_id ? collect([$user->dapur]) : Dapur::where('is_active', true)->orderBy('name')->get();

        return view('purchase-orders.bulk-import', compact('dapurs'));
    }

    /**
     * Process the bulk PO import from CSV/Excel.
     */
    public function processBulkImport(Request $request)
    {
        $request->validate([
            'dapur_id' => 'required|exists:dapurs,id',
            'po_date' => 'required|date',
            'file' => 'required|file|max:10240',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $poDate = Carbon::parse($request->po_date);
                $poNumber = 'PO/'.$poDate->format('Y/m').'/'.str_pad(PurchaseOrder::count() + 1, 3, '0', STR_PAD_LEFT);

                $purchaseOrder = PurchaseOrder::create([
                    'po_number' => $poNumber,
                    'po_date' => $poDate,
                    'dapur_id' => $request->dapur_id,
                    'status' => PoStatus::DRAF,
                    'notes' => $request->notes,
                    'created_by' => auth()->id(),
                    'total_estimated_cost' => 0,
                ]);

                // Catat histori awal
                $purchaseOrder->statusHistory()->create([
                    'from_status' => null,
                    'to_status' => PoStatus::DRAF,
                    'changed_by' => auth()->id(),
                    'ip_address' => $request->ip(),
                    'reason' => 'Import massal dari file',
                ]);

                Excel::import(new PurchaseOrderBulkImport($purchaseOrder), $request->file('file'));

                // Recalculate total after import
                $purchaseOrder->recalculateTotal();

                return redirect()->route('purchase-orders.show', $purchaseOrder)
                    ->with('success', "PO {$poNumber} berhasil di-import.");
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal meng-import PO: '.$e->getMessage());
        }
    }

    /**
     * Download template for PO item import.
     */
    public function downloadTemplate(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_item_po.csv"',
        ];

        $columns = ['No', 'Uraian Jenis Bahan Makanan', 'Kuantitas', 'Satuan', 'Harga Satuan', 'Jumlah', 'Keterangan'];

        return response()->stream(function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            // Example row
            fputcsv($file, ['1', 'Beras Ramos', '20', 'Karung', '375000', '7500000', '1 karung 25 kg']);
            fputcsv($file, ['2', 'Minyak Goreng', '150', 'Pouch', '42500', '6375000', 'Kemasan 2 liter']);

            fclose($file);
        }, 200, $headers);
    }
}
