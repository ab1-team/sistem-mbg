<?php

namespace App\Models;

use App\Enums\PoStatus;
use App\Notifications\PurchaseOrderStatusChanged;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_number',
        'dapur_id',
        'menu_period_id',
        'status',
        'total_estimated_cost',
        'total_actual_cost',
        'notes',
        'cancellation_reason',
        'created_by',
        'po_date',
        'submitted_at',
        'verified_at',
        'verified_by',
    ];

    protected $casts = [
        'status' => PoStatus::class,
        'submitted_at' => 'datetime',
        'verified_at' => 'datetime',
        'po_date' => 'date',
        'total_estimated_cost' => 'decimal:2',
        'total_actual_cost' => 'decimal:2',
    ];

    public function statusHistory(): HasMany
    {
        return $this->hasMany(PoStatusHistory::class);
    }

    public function goodsReceipts(): HasMany
    {
        return $this->hasMany(GoodsReceipt::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Mengubah status PO dengan validasi state machine dan pencatatan audit trail.
     * Sesuai Roadmap 3.2 & 3.3
     */
    public function changeStatus(PoStatus $newStatus, ?string $reason = null, ?array $metadata = null)
    {
        $oldStatus = $this->status;

        // Validasi transisi (kecuali jika status sama, abaikan)
        if ($oldStatus !== $newStatus && ! in_array($newStatus, $oldStatus->allowedTransitions())) {
            throw new \Exception("Transisi status dari {$oldStatus->value} ke {$newStatus->value} tidak diizinkan.");
        }

        $this->update(['status' => $newStatus]);

        // Catat histori audit trail (Schema 4.4)
        $this->statusHistory()->create([
            'from_status' => $oldStatus,
            'to_status' => $newStatus,
            'changed_by' => auth()->id() ?? 1, // Fallback ke ID 1 jika via CLI/System
            'reason' => $reason,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
        ]);

        // Trigger Notifikasi (Roadmap 3.2)
        $this->creator?->notify(new PurchaseOrderStatusChanged($this, $newStatus->label(), $reason));

        // Jika dikirim ke yayasan, notify admin yayasan
        if ($newStatus === PoStatus::DIKIRIM_KE_YAYASAN) {
            $admins = User::role('admin_yayasan')->get();
            foreach ($admins as $admin) {
                $admin->notify(new PurchaseOrderStatusChanged($this, $newStatus->label(), $reason));
            }
        }

        return $this;
    }

    /**
     * Recalculate total estimated cost based on items.
     */
    public function recalculateTotal(): void
    {
        $total = $this->items()->sum(\DB::raw('quantity_to_order * estimated_unit_price'));
        $this->update(['total_estimated_cost' => $total]);
    }

    public function dapur()
    {
        return $this->belongsTo(Dapur::class);
    }

    public function menuPeriod()
    {
        return $this->belongsTo(MenuPeriod::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
