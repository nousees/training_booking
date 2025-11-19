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

    public function mount()
    {
        $settings = SystemSetting::get();
        $this->platformCommissionPercent = $settings->platform_commission_percent;
        $this->cancellationWindowHours = $settings->cancellation_window_hours;
        $this->currency = $settings->currency;
        $this->maintenanceMode = $settings->maintenance_mode;
    }

    public function save()
    {
        $this->validate([
            'platformCommissionPercent' => 'required|numeric|min:0|max:100',
            'cancellationWindowHours' => 'required|integer|min:1',
            'currency' => 'required|string|size:3',
            'maintenanceMode' => 'boolean',
        ]);

        $settings = SystemSetting::get();
        $settings->update([
            'platform_commission_percent' => $this->platformCommissionPercent,
            'cancellation_window_hours' => $this->cancellationWindowHours,
            'currency' => strtoupper($this->currency),
            'maintenance_mode' => $this->maintenanceMode,
        ]);

        session()->flash('message', 'Settings saved successfully!');
    }

    public function render()
    {
        return view('livewire.admin.platform-settings');
    }
}

