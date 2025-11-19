<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'platform_commission_percent',
        'cancellation_window_hours',
        'min_booking_hours_before_start',
        'max_booking_days_ahead',
        'currency',
        'maintenance_mode',
        'auto_confirm_bookings',
    ];

    protected function casts(): array
    {
        return [
            'platform_commission_percent' => 'decimal:2',
            'cancellation_window_hours' => 'integer',
            'min_booking_hours_before_start' => 'integer',
            'max_booking_days_ahead' => 'integer',
            'maintenance_mode' => 'boolean',
            'auto_confirm_bookings' => 'boolean',
        ];
    }

    public static function get(): self
    {
        return static::firstOrCreate(['id' => 1], [
            'platform_commission_percent' => 10.00,
            'cancellation_window_hours' => 24,
            'min_booking_hours_before_start' => 2,
            'max_booking_days_ahead' => 14,
            'currency' => 'USD',
            'maintenance_mode' => false,
            'auto_confirm_bookings' => false,
        ]);
    }
}

