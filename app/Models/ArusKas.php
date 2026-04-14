<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArusKas extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_akun',
        'urutan',
        'sub',
        'super_sub',
        'status',
    ];

    public function rekenings(): HasMany
    {
        return $this->hasMany(ArusKasRekening::class, 'arus_kas_id');
    }
}
