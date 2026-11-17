<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_number',
        'name',
        'type',
        'price_per_hour',
        'capacity',
        'status',
        'facilities',
        'image',
        'description',
        'block_reason',
    ];

    protected $casts = [
        'price_per_hour' => 'decimal:2',
        'capacity' => 'integer',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function activeBooking()
    {
        return $this->hasOne(Booking::class)
            ->whereIn('status', ['confirmed'])
            ->latest('start_time');
    }

    public function companions()
    {
        return $this->hasMany(Companion::class, 'current_room_id');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function getFacilitiesArrayAttribute(): array
    {
        if (empty($this->facilities)) {
            return [];
        }
        return array_map('trim', explode(',', $this->facilities));
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price_per_hour, 0, ',', '.');
    }

    public function getImageUrlAttribute(): string
    {
        if (!empty($this->image)) {
            if (str_starts_with($this->image, 'http') || str_starts_with($this->image, '/')) {
                return $this->image;
            }
            return asset('storage/' . $this->image);
        }

        // Curated high quality room photos by room type
        $defaults = [
            'small' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?auto=format&fit=crop&w=800&q=80',
            'medium' => 'https://images.unsplash.com/photo-1563245372-f21724e3856d?auto=format&fit=crop&w=800&q=80',
            'large' => 'https://images.unsplash.com/photo-1574391884720-bbc3740c59d1?auto=format&fit=crop&w=800&q=80',
            'vip' => 'https://images.unsplash.com/photo-1545128485-c400e7702796?auto=format&fit=crop&w=800&q=80',
        ];

        return $defaults[$this->type] ?? 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?auto=format&fit=crop&w=800&q=80';
    }
}
