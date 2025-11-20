<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bio',
        'specializations',
        'experience_years',
        'price_per_hour',
        'rating',
        'images',
        'locations',
        'supports_online',
        'online_link',
    ];

    protected function casts(): array
    {
        return [
            'specializations' => 'array',
            'images' => 'array',
            'locations' => 'array',
            'price_per_hour' => 'decimal:2',
            'rating' => 'float',
            'supports_online' => 'boolean',
            'online_link' => 'string',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'trainer_id');
    }

    public function updateRating(): void
    {
        $avgRating = $this->reviews()->avg('rating') ?? 0;
        $this->update(['rating' => round($avgRating, 2)]);
    }
}

