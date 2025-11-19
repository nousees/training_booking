<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'status',
        'payment_status',
        'mode',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'mode' => 'string',
        ];
    }

    public function session()
    {
        return $this->belongsTo(TrainingSession::class, 'session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isCanceled(): bool
    {
        return $this->status === 'canceled';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function canBeCanceled(): bool
    {
        if ($this->isCanceled() || $this->isCompleted()) {
            return false;
        }

        $settings = SystemSetting::get();
        $session = $this->session;
        $sessionDateTime = \Carbon\Carbon::parse($session->date->format('Y-m-d') . ' ' . $session->start_time->format('H:i:s'));
        $hoursUntilSession = now()->diffInHours($sessionDateTime, false);

        return $hoursUntilSession >= $settings->cancellation_window_hours;
    }
}
