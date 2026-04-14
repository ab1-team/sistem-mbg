<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AkunLevel3 extends Model
{
    use HasFactory;

    protected $table = 'akun_level3s';

    protected $fillable = [
        'kode',
        'nama',
        'parent_id',
        'jenis_mutasi',
        'no_rek_bank',
        'atas_nama_rek',
    ];

    public function level2(): BelongsTo
    {
        return $this->belongsTo(AkunLevel2::class, 'parent_id');
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class, 'parent_id');
    }
}
