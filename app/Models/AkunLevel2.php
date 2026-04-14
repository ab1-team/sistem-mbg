<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AkunLevel2 extends Model
{
    use HasFactory;

    protected $table = 'akun_level2s';

    protected $fillable = [
        'kode',
        'nama',
        'parent_id',
        'jenis_mutasi',
        'no_rek_bank',
        'atas_nama_rek',
    ];

    public function level1(): BelongsTo
    {
        return $this->belongsTo(AkunLevel1::class, 'parent_id');
    }

    public function level3s(): HasMany
    {
        return $this->hasMany(AkunLevel3::class, 'parent_id');
    }
}
