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
    public $showDetailsModal = false;
    public $detailsSession = null;
    public $expandedDates = [];

    public $availableLocations = [];

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
        $this->loadLocations();
    }

    protected function loadLocations(): void
    {
        $user = Auth::user();
        $this->availableLocations = [];

        if (!$user || !$user->trainerProfile) {
            return;
        }

        $profile = $user->trainerProfile;
        $locations = is_array($profile->locations) ? $profile->locations : [];

        foreach ($locations as $loc) {
            $loc = trim((string)$loc);
            if ($loc !== '' && !in_array($loc, $this->availableLocations, true)) {
                $this->availableLocations[] = $loc;
            }
        }

        if ($profile->supports_online) {
            $onlineLabel = 'Онлайн';
            $onlineValue = $profile->online_link ?: 'Онлайн тренировка';
            if ($profile->online_link) {
                $onlineLabel = 'Онлайн (' . $profile->online_link . ')';
            }
            // Добавляем как отдельную строку со значением ссылки/описания
            if (!in_array($onlineValue, $this->availableLocations, true)) {
                $this->availableLocations[] = $onlineValue;
            }
        }
    }

    public function openForm($sessionId = null)
    {
        $this->loadLocations();
        if ($sessionId) {
            $this->editingSession = TrainingSession::findOrFail($sessionId);
            $this->date = $this->editingSession->date->format('Y-m-d');
            $this->startTime = $this->editingSession->start_time->format('H:i');
            $this->endTime = $this->editingSession->end_time->format('H:i');
            $this->location = $this->editingSession->location;
            $this->price = $this->editingSession->price;

            // если у слота локация отсутствует в текущих вариантах, добавим её, чтобы не потерять
            if ($this->location && !in_array($this->location, $this->availableLocations, true)) {
                $this->availableLocations[] = $this->location;
            }
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

    public function showDetails($sessionId)
    {
        $this->detailsSession = TrainingSession::with(['booking.user'])->findOrFail($sessionId);
        $this->showDetailsModal = true;
    }

    public function closeDetails()
    {
        $this->showDetailsModal = false;
        $this->detailsSession = null;
    }

    public function toggleDateSection(string $date)
    {
        if (in_array($date, $this->expandedDates, true)) {
            $this->expandedDates = array_values(array_diff($this->expandedDates, [$date]));
        } else {
            $this->expandedDates[] = $date;
        }
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

        $this->date = $this->normalizeDateStr($this->date) ?? $this->date;
        $this->validate();

        $scheduleService = app(ScheduleService::class);


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

        $this->date = $this->normalizeDateStr($this->date) ?? $this->date;
        $this->startTime = '';
        $this->endTime = '';
        $this->computeFreeTimes();
        $this->autoComputeEndTime();
    }


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


        $step = in_array((int)$this->timeStep, [30,60], true) ? (int)$this->timeStep : 30;


        $minStart = null;
        if ($this->date === now()->format('Y-m-d')) {
            $now = now()->copy()->second(0);
            $mod = $now->minute % $step;
            $ceil = $mod === 0 ? $now->copy()->addMinutes($step) : $now->copy()->addMinutes($step - $mod);
            $minStart = $ceil;
        }


        foreach ($free as $interval) {
            $start = \Carbon\Carbon::parse($this->date.' '.$interval['start']);
            $end = \Carbon\Carbon::parse($this->date.' '.$interval['end']);

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


    }

    protected function autoComputeEndTime(): void
    {

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
            session()->flash('error', 'Нельзя удалить уже забронированный слот.');
            return;
        }

        $session->delete();
        session()->flash('message', 'Слот успешно удалён!');
    }

    public function render()
    {
        $today = now()->toDateString();
        $until = now()->copy()->addDays(6)->toDateString();

        $query = TrainingSession::where('trainer_id', Auth::id())
            ->whereBetween('date', [$today, $until])
            ->orderBy('date')
            ->orderBy('start_time');

        return view('livewire.trainer.schedule-manager', [
            'sessions' => $query->get(),
        ]);
    }
}

