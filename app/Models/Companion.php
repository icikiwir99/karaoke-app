<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Companion extends Model
{
    use HasFactory;

    protected $fillable = [
        'stage_name',
        'real_name',
        'phone',
        'price_per_hour',
        'commission_rate',
        'status',
        'current_room_id',
        'photo',
        'gender',
        'rating',
        'skills',
        'notes',
        'total_hours_served',
    ];

    protected $casts = [
        'price_per_hour' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'rating' => 'decimal:2',
        'total_hours_served' => 'decimal:2',
    ];

    public function currentRoom()
    {
        return $this->belongsTo(Room::class, 'current_room_id');
    }

    public function bookingCompanions()
    {
        return $this->hasMany(BookingCompanion::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price_per_hour, 0, ',', '.');
    }

    public function getSkillsArrayAttribute(): array
    {
        if (empty($this->skills)) {
            return [];
        }
        return array_map('trim', explode(',', $this->skills));
    }
}
