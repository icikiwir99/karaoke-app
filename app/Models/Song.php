<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'artist',
        'genre',
        'language',
        'lyrics_snippet',
        'is_favorite',
        'views_count',
    ];

    protected $casts = [
        'is_favorite' => 'boolean',
        'views_count' => 'integer',
    ];

    public function scopeSearch($query, $search)
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('artist', 'like', "%{$search}%")
              ->orWhere('genre', 'like', "%{$search}%")
              ->orWhere('lyrics_snippet', 'like', "%{$search}%");
        });
    }
}
