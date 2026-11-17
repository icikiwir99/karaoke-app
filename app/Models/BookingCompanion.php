<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingCompanion extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'companion_id',
        'hours',
        'price_per_hour',
        'subtotal',
        'status',
    ];

    protected $casts = [
        'hours' => 'decimal:2',
        'price_per_hour' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function companion()
    {
        return $this->belongsTo(Companion::class);
    }
}
