<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'dapur_id',
        'user_id',
        'no_pembayaran',
        'tanggal_pembayaran',
        'jenis_transaksi',
        'invoice_id',
        'withdrawal_request_id',
        'total_harga',
        'metode_pembayaran',
        'no_referensi',
        'payment_proof',
        'catatan',
        'rekening_debit',
        'rekening_kredit',
        'created_by',
    ];

    protected $casts = [
        'tanggal_pembayaran' => 'datetime',
        'total_harga' => 'decimal:2',
    ];

    public function dapur(): BelongsTo
    {
        return $this->belongsTo(Dapur::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function withdrawalRequest(): BelongsTo
    {
        return $this->belongsTo(WithdrawalRequest::class);
    }
}
