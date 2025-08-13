<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'serial',
        'image_path',
    ];

    protected $appends = [
        'image_url',
    ];

    /* -----------------------------------------------------------------
     |  Relationships
     | -----------------------------------------------------------------*/
    public function purchases()
    {
        return $this->hasMany(TicketPurchase::class);
    }

    public function activePurchases()
    {
        // Considered "held" when pending or accepted
        return $this->purchases()->whereIn('status', ['pending', 'accepted']);
    }

    /* -----------------------------------------------------------------
     |  Accessors
     | -----------------------------------------------------------------*/
    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }

    /* -----------------------------------------------------------------
     |  Mutators
     | -----------------------------------------------------------------*/
    public function setSerialAttribute($value): void
    {
        // keep consistent formatting
        $this->attributes['serial'] = strtoupper(trim((string) $value));
    }

    /* -----------------------------------------------------------------
     |  Scopes
     | -----------------------------------------------------------------*/
    /**
     * Tickets that are not currently held by anyone (no pending/accepted).
     */
    public function scopeAvailable($query)
    {
        return $query->whereDoesntHave('purchases', function ($q) {
            $q->whereIn('status', ['pending', 'accepted']);
        });
    }
}
