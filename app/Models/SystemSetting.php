<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'platform_commission_percent',
        'cancellation_window_hours',
        'currency',
        'maintenance_mode',
    ];

    protected function casts(): array
    {
        return [
            'platform_commission_percent' => 'decimal:2',
            'cancellation_window_hours' => 'integer',
            'maintenance_mode' => 'boolean',
        ];
    }

    public static function get(): self
    {
        return static::firstOrCreate(['id' => 1], [
            'platform_commission_percent' => 10.00,
            'cancellation_window_hours' => 24,
            'currency' => 'USD',
            'maintenance_mode' => false,
        ]);
    }
}

