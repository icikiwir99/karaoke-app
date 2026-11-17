<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashierShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shift_name',
        'start_time',
        'end_time',
        'starting_cash',
        'ending_cash',
        'actual_cash',
        'difference',
        'total_sales_cash',
        'total_sales_noncash',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'starting_cash' => 'decimal:2',
        'ending_cash' => 'decimal:2',
        'actual_cash' => 'decimal:2',
        'difference' => 'decimal:2',
        'total_sales_cash' => 'decimal:2',
        'total_sales_noncash' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function movements()
    {
        return $this->hasMany(CashMovement::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function getExpectedCashAttribute(): float
    {
        $cashIn = $this->movements()->where('type', 'in')->sum('amount');
        $cashOut = $this->movements()->where('type', 'out')->sum('amount');
        return (float) ($this->starting_cash + $this->total_sales_cash + $cashIn - $cashOut);
    }
}
