<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AkunLevel1 extends Model
{
    use HasFactory;

    protected $table = 'akun_level1s';

    protected $fillable = [
        'kode',
        'nama',
        'parent_id',
        'jenis_mutasi',
        'no_rek_bank',
        'atas_nama_rek',
    ];

    public function level2s(): HasMany
    {
        return $this->hasMany(AkunLevel2::class, 'parent_id');
    }
}
