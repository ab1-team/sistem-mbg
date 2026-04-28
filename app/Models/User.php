<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'dapur_id',
        'supplier_id',
        'is_active',
        'last_login_at',
    ];

    protected static function booted()
    {
        static::creating(function ($user) {
            if (! $user->uuid) {
                $user->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the dapur that the user belongs to.
     */
    public function dapur()
    {
        return $this->belongsTo(Dapur::class, 'dapur_id');
    }

    /**
     * Get the supplier that the user belongs to.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    /**
     * Get the push notification tokens for the user.
     */
    public function fcmTokens()
    {
        return $this->hasMany(FcmToken::class);
    }

    /**
     * Route notifications for the FCM channel.
     */
    public function routeNotificationForFcm()
    {
        return $this->fcmTokens->pluck('token')->toArray();
    }
}
