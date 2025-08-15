<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image_path',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
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

    /**
     * Purchases that hold the ticket (pending or accepted).
     */
    public function activePurchases()
    {
        return $this->purchases()->whereIn('status', ['pending', 'accepted']);
    }

    /* -----------------------------------------------------------------
     |  Accessors
     | -----------------------------------------------------------------*/
    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset('storage/'.$this->image_path) : null;
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

    /**
     * Tickets that still have stock remaining.
     * (Uses withCount on pending/accepted and compares to quantity.)
     */
    public function scopeInStock($query)
{
    // Tickets where held (pending/accepted) count is less than quantity
    return $query->whereRaw(
        "COALESCE((
            SELECT COUNT(*)
            FROM ticket_purchases
            WHERE ticket_purchases.ticket_id = tickets.id
              AND status IN ('pending','accepted')
        ), 0) < tickets.quantity"
    );
}
}
