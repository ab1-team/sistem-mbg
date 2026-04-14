<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'dapur_id',
        'kode',
        'nama',
        'parent_id',
        'jenis_mutasi',
        'no_rek_bank',
        'atas_nama_rek',
    ];

    public function dapur(): BelongsTo
    {
        return $this->belongsTo(Dapur::class);
    }

    public function level3(): BelongsTo
    {
        return $this->belongsTo(AkunLevel3::class, 'parent_id');
    }

    public function balance(): HasOne
    {
        return $this->hasOne(Balance::class, 'kode_akun', 'kode');
    }
}
