<?php

namespace App\Http\Livewire\Admin;

use App\Models\SystemSetting;
use Livewire\Component;

class PlatformSettings extends Component
{
    public $platformCommissionPercent = '';
    public $cancellationWindowHours = '';
    public $currency = '';
    public $maintenanceMode = false;
    public $minBookingHoursBeforeStart = '';
    public $maxBookingDaysAhead = '';
    public $autoConfirmBookings = false;
    public $minBookingTimeBeforeStart = '';
    public $maxBookingTimeAhead = '';

    public function mount()
    {
        $settings = SystemSetting::get();
        $this->platformCommissionPercent = $settings->platform_commission_percent;
        $this->cancellationWindowHours = $settings->cancellation_window_hours;
        $this->minBookingHoursBeforeStart = $settings->min_booking_hours_before_start;
        $this->maxBookingDaysAhead = $settings->max_booking_days_ahead;
        $this->currency = $settings->currency;
        $this->maintenanceMode = $settings->maintenance_mode;
        $this->autoConfirmBookings = $settings->auto_confirm_bookings;
    }

    public function save()
    {
        $this->validate([
            'platformCommissionPercent' => 'required|numeric|min:0|max:100',
            'cancellationWindowHours' => 'required|integer|min:1',
            'minBookingHoursBeforeStart' => 'required|integer|min:0',
            'maxBookingDaysAhead' => 'required|integer|min:1',
            'currency' => 'required|string|size:3',
            'maintenanceMode' => 'boolean',
            'autoConfirmBookings' => 'boolean',
        ]);

        $settings = SystemSetting::get();
        $settings->update([
            'platform_commission_percent' => $this->platformCommissionPercent,
            'cancellation_window_hours' => $this->cancellationWindowHours,
            'min_booking_hours_before_start' => $this->minBookingHoursBeforeStart,
            'max_booking_days_ahead' => $this->maxBookingDaysAhead,
            'currency' => strtoupper($this->currency),
            'maintenance_mode' => $this->maintenanceMode,
            'auto_confirm_bookings' => $this->autoConfirmBookings,
        ]);

        session()->flash('message', 'Настройки успешно сохранены!');
    }

    public function render()
    {
        return view('livewire.admin.platform-settings');
    }
}

