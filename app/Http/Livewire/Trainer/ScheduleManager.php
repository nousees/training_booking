<?php

namespace App\Http\Livewire\Trainer;

use App\Models\TrainingSession;
use App\Services\ScheduleService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ScheduleManager extends Component
{
    use WithPagination;

    public $date = '';
    public $startTime = '';
    public $endTime = '';
    public $location = '';
    public $price = '';
    public $showForm = false;
    public $editingSession = null;
    public $freeStartOptions = [];
    public $freeEndOptions = [];
    public $timeStep = 30; // minutes: 30 or 60

    protected $rules = [
        'date' => 'required|date|after_or_equal:today',
        'startTime' => 'required',
        'endTime' => 'required|after:startTime',
        'location' => 'required|string|max:500',
        'price' => 'required|numeric|min:0',
        'timeStep' => 'required|in:30,60',
    ];

    public function mount()
    {
        $this->date = now()->format('Y-m-d');
    }

    public function openForm($sessionId = null)
    {
        if ($sessionId) {
            $this->editingSession = TrainingSession::findOrFail($sessionId);
            $this->date = $this->editingSession->date->format('Y-m-d');
            $this->startTime = $this->editingSession->start_time->format('H:i');
            $this->endTime = $this->editingSession->end_time->format('H:i');
            $this->location = $this->editingSession->location;
            $this->price = $this->editingSession->price;
        } else {
            $this->resetForm();
        }
        $this->computeFreeTimes();
        $this->showForm = true;
    }

    public function closeForm()
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->editingSession = null;
        $this->date = now()->format('Y-m-d');
        $this->startTime = '';
        $this->endTime = '';
        $this->location = '';
        $this->price = '';
        $this->freeStartOptions = [];
        $this->freeEndOptions = [];
        $this->timeStep = 30;
    }

    public function save()
    {
        // Нормализуем дату к Y-m-d для валидации/сервисов
        $this->date = $this->normalizeDateStr($this->date) ?? $this->date;
        $this->validate();

        $scheduleService = app(ScheduleService::class);

        // Дополнительная проверка: сегодня нельзя ставить начало в прошлом
        if ($this->date === now()->format('Y-m-d')) {
            $nowStr = now()->format('H:i');
            if ($this->startTime < $nowStr) {
                session()->flash('error', 'Нельзя создавать слот в прошлом времени.');
                $this->computeFreeTimes();
                return;
            }
        }

        try {
            if ($this->editingSession) {
                $scheduleService->updateSession($this->editingSession, [
                    'date' => $this->date,
                    'start_time' => $this->startTime,
                    'end_time' => $this->endTime,
                    'location' => $this->location,
                    'price' => $this->price,
                ]);
                session()->flash('message', 'Слот обновлён');
            } else {
                $scheduleService->createSession(Auth::user(), [
                    'date' => $this->date,
                    'start_time' => $this->startTime,
                    'end_time' => $this->endTime,
                    'location' => $this->location,
                    'price' => $this->price,
                ]);
                session()->flash('message', 'Слот создан');
            }
            $this->closeForm();
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
            $this->computeFreeTimes();
        }
    }

    public function updatedDate()
    {
        // Нормализуем пользовательский ввод (в т.ч. формат dd.mm.yyyy) к Y-m-d
        $this->date = $this->normalizeDateStr($this->date) ?? $this->date;
        $this->startTime = '';
        $this->endTime = '';
        $this->computeFreeTimes();
        $this->autoComputeEndTime();
    }

    // Явный обработчик события change на датапикере — чтобы гарантировать пересчёт сразу после выбора даты
    public function handleDateChange()
    {
        $this->updatedDate();
    }

    public function updatedStartTime()
    {
        $this->computeFreeTimes();
        $this->autoComputeEndTime();
    }

    public function updatedTimeStep()
    {
        $this->computeFreeTimes();
        $this->autoComputeEndTime();
    }

    protected function computeFreeTimes(): void
    {
        $this->freeStartOptions = [];
        $this->freeEndOptions = [];

        if (!$this->date) return;

        $service = app(ScheduleService::class);
        $dateNorm = $this->normalizeDateStr($this->date) ?? $this->date;
        $free = $service->getFreeIntervals(Auth::user(), $dateNorm, '07:00', '22:00');

        // Шаг времени
        $step = in_array((int)$this->timeStep, [30,60], true) ? (int)$this->timeStep : 30;

        // Для сегодняшнего дня — не предлагать прошедшее время
        $minStart = null;
        if ($this->date === now()->format('Y-m-d')) {
            $now = now()->copy()->second(0);
            $mod = $now->minute % $step;
            $ceil = $mod === 0 ? $now->copy()->addMinutes($step) : $now->copy()->addMinutes($step - $mod);
            $minStart = $ceil;
        }

        // Сформировать варианты начала (только те, где start + step <= конец интервала)
        foreach ($free as $interval) {
            $start = \Carbon\Carbon::parse($this->date.' '.$interval['start']);
            $end = \Carbon\Carbon::parse($this->date.' '.$interval['end']);
            // учесть минимум на сегодня
            $t0 = $start->copy();
            if ($minStart && $t0->lt($minStart)) {
                $t0 = $minStart->copy();
            }
            for ($t = $t0; $t->lt($end); $t->addMinutes($step)) {
                if ($t->copy()->addMinutes($step)->gt($end)) {
                    break;
                }
                $this->freeStartOptions[] = $t->format('H:i');
            }
        }

        // Конец вычисляется автоматически, отдельные варианты не нужны
    }

    protected function autoComputeEndTime(): void
    {
        // Автоподстановка конца: конец = начало + шаг, но не позже конца свободного интервала
        if (!$this->date || !$this->startTime) {
            $this->endTime = '';
            return;
        }

        $service = app(ScheduleService::class);
        $dateNorm = $this->normalizeDateStr($this->date) ?? $this->date;
        $free = $service->getFreeIntervals(Auth::user(), $dateNorm, '07:00', '22:00');
        $step = in_array((int)$this->timeStep, [30,60], true) ? (int)$this->timeStep : 30;

        $startSel = \Carbon\Carbon::parse($this->date.' '.$this->startTime);
        foreach ($free as $interval) {
            $intStart = \Carbon\Carbon::parse($this->date.' '.$interval['start']);
            $intEnd = \Carbon\Carbon::parse($this->date.' '.$interval['end']);
            if ($startSel->betweenIncluded($intStart, $intEnd)) {
                $candidate = $startSel->copy()->addMinutes($step);
                if ($candidate->gt($intEnd)) {
                    $candidate = $intEnd->copy();
                }
                $this->endTime = $candidate->format('H:i');
                return;
            }
        }

        // Если начало не попало ни в один свободный интервал (не должно случиться) — сбросить конец
        $this->endTime = '';
    }

    protected function normalizeDateStr(?string $value): ?string
    {
        if (!$value) return null;
        $value = trim($value);
        if (preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $value)) {
            try { return \Carbon\Carbon::createFromFormat('d.m.Y', $value)->format('Y-m-d'); } catch (\Throwable $e) {}
        }
        try { return \Carbon\Carbon::parse($value)->format('Y-m-d'); } catch (\Throwable $e) {}
        return null;
    }

    public function delete($sessionId)
    {
        $session = TrainingSession::findOrFail($sessionId);
        
        if ($session->trainer_id !== Auth::id()) {
            abort(403);
        }

        if ($session->status === 'booked') {
            session()->flash('error', 'Cannot delete a booked session.');
            return;
        }

        $session->delete();
        session()->flash('message', 'Session deleted successfully!');
    }

    public function render()
    {
        $sessions = TrainingSession::where('trainer_id', Auth::id())
            ->orderBy('date')
            ->orderBy('start_time')
            ->paginate(20);

        return view('livewire.trainer.schedule-manager', [
            'sessions' => $sessions,
        ]);
    }
}

