<?php

namespace App\Models;

use App\Enums\PoStatus;
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
        'submitted_at',
    ];

    protected $casts = [
        'status' => PoStatus::class,
        'submitted_at' => 'datetime',
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

        return $this;
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
}
