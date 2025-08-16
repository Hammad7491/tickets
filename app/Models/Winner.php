<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Winner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'serial_number',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];
}
