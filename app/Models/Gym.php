<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gym extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description', 
        'address',
        'phone',
        'email',
        'opening_time',
        'closing_time',
        'is_active',
        'owner_id',
    ];

    protected $casts = [
        'opening_time' => 'datetime',
        'closing_time' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function trainers()
    {
        return $this->hasMany(Trainer::class);
    }

    public function trainings()
    {
        return $this->hasMany(Training::class);
    }
}