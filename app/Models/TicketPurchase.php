<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'phone',
        'proof_image_path',
        'status',
    ];

    /**
     * A purchase belongs to a ticket.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * A purchase belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
