<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'booking_id',
        'room_id',
        'user_id',
        'cashier_shift_id',
        'customer_name',
        'customer_phone',
        'total_room',
        'total_fnb',
        'total_service',
        'discount',
        'grand_total',
        'paid_amount',
        'change_amount',
        'payment_method',
        'is_complimentary',
        'complimentary_reason',
        'status',
        'notes',
    ];

    protected $casts = [
        'total_room' => 'decimal:2',
        'total_fnb' => 'decimal:2',
        'total_service' => 'decimal:2',
        'discount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'is_complimentary' => 'boolean',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shift()
    {
        return $this->belongsTo(CashierShift::class, 'cashier_shift_id');
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function getFormattedGrandTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->grand_total, 0, ',', '.');
    }

    public function getTotalProfitAttribute(): float
    {
        return (float) $this->items->sum(function ($item) {
            return ($item->price - $item->purchase_price) * $item->quantity;
        });
    }
}
