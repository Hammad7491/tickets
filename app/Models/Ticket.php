<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'price',
        'quantity',
        'notes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    protected $attributes = [
        'quantity' => 1,
    ];

    // Generate unique 4-digit code (1000–9999)
    public static function generateUniqueCode(int $tries = 10): string
    {
        for ($i = 0; $i < $tries; $i++) {
            $code = (string) random_int(1000, 9999);
            if (! static::where('code', $code)->exists()) {
                return $code;
            }
        }
        return substr((string) time(), -4);
    }
}
