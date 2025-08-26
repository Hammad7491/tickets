<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_USER  = 'user';

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        // we rely on Spatie roles instead of a single 'role' column
        'avatar',
        'google_id',
        'facebook_id',
        'is_blocked',
    ];

    /**
     * Hidden attributes for arrays.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_blocked'        => 'boolean',
    ];

    /**
     * Default attributes.
     */
    protected $attributes = [
        'is_blocked' => false,
    ];

    /* -----------------------------------------------------------------
     |  Relationships
     | -----------------------------------------------------------------*/
    public function ticketPurchases()
    {
        return $this->hasMany(TicketPurchase::class);
    }

    /* -----------------------------------------------------------------
     |  Mutators
     | -----------------------------------------------------------------*/
    /**
     * Automatically hash password if plain text is assigned.
     * Allows blank (keep existing hash) and avoids double-hashing.
     */
    public function setPasswordAttribute($value): void
    {
        if (blank($value)) {
            return; // keep existing password when null/empty passed
        }

        $this->attributes['password'] = Hash::needsRehash($value)
            ? Hash::make($value)
            : $value;
    }

    /**
     * Always store phone as digits-only; null if empty.
     */
    public function setPhoneAttribute($value): void
    {
        $digits = preg_replace('/\D+/', '', (string) $value);
        $this->attributes['phone'] = $digits !== '' ? $digits : null;
    }

    /* -----------------------------------------------------------------
     |  Helpers
     | -----------------------------------------------------------------*/
    public function getIsAdminAttribute(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN) || ($this->getAttribute('role') === self::ROLE_ADMIN);
    }

    public function makeAdmin(): void
    {
        $this->syncRoles([self::ROLE_ADMIN]);
    }

    public function makeUser(): void
    {
        $this->syncRoles([self::ROLE_USER]);
    }

    public function scopeActive($query)
    {
        return $query->where('is_blocked', false);
    }
}
