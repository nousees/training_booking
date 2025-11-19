<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'avatar',
        'role',
        'timezone',
        'notify_email',
        'notify_in_app',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'blocked_at' => 'datetime',
        ];
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function isTrainer(): bool
    {
        return $this->role === 'trainer';
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function trainerProfile()
    {
        return $this->hasOne(TrainerProfile::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function trainingSessions()
    {
        return $this->hasMany(TrainingSession::class, 'trainer_id');
    }

    public function isBlocked(): bool
    {
        return !is_null($this->blocked_at);
    }
}
