<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'trainer_id',
        'date',
        'start_time',
        'end_time',
        'location',
        'price',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'price' => 'decimal:2',
        ];
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function booking()
    {
        return $this->hasOne(Booking::class, 'session_id');
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function isBooked(): bool
    {
        return $this->status === 'booked';
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeForTrainer($query, $trainerId)
    {
        return $query->where('trainer_id', $trainerId);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now()->toDateString());
    }
}

