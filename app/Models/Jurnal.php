<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jurnal extends Model
{
    use HasFactory;

    protected $fillable = [
        'dapur_id',
        'tanggal',
        'keterangan',
        'relasi',
        'jumlah',
        'urutan',
        'user_id',
    ];

    protected $casts = [
        'tanggal' => 'date', // Considering casting to date as it's a date string in DB
        'jumlah' => 'decimal:2',
    ];

    public function dapur(): BelongsTo
    {
        return $this->belongsTo(Dapur::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
