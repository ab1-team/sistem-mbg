<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Dapur extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'slug',
        'address',
        'city',
        'province',
        'capacity_portions',
        'is_active',
    ];

    protected static function booted()
    {
        static::creating(function ($dapur) {
            if (! $dapur->slug) {
                $originalSlug = Str::slug($dapur->name);
                $slug = $originalSlug;
                $count = 1;

                while (static::withTrashed()->where('slug', $slug)->exists()) {
                    $slug = $originalSlug.'-'.$count++;
                }

                $dapur->slug = $slug;
            }
        });
    }

    public function wallet(): MorphOne
    {
        return $this->morphOne(Wallet::class, 'owner');
    }
}
