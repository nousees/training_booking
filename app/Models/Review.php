<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'user_id',
        'trainer_id',
        'rating',
        'comment',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
        ];
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    protected static function booted(): void
    {
        static::created(function ($review) {
            $review->trainer->trainerProfile?->updateRating();
        });

        static::updated(function ($review) {
            $review->trainer->trainerProfile?->updateRating();
        });

        static::deleted(function ($review) {
            $review->trainer->trainerProfile?->updateRating();
        });
    }
}

