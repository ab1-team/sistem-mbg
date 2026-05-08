<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'bank_name',
        'bank_account',
        'bank_holder',
        'category',
        'is_active',
    ];

    public function subSuppliers()
    {
        return $this->hasMany(SubSupplier::class);
    }

    /**
     * Get the materials supplied by this supplier.
     */
    public function materials()
    {
        return $this->belongsToMany(Material::class, 'material_suppliers')
            ->withPivot('is_active')
            ->withTimestamps();
    }
}
