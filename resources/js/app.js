import './bootstrap';

function initTrainerScheduler() {
    try {
        if (!window.DayPilot) {
            return;
        }

        const container = document.getElementById('trainer-scheduler');
        if (!container) {
            return;
        }

        // Avoid double init
        if (container.__dpInitialized) {
            return;
        }
        container.__dpInitialized = true;

        const dp = new DayPilot.Scheduler('trainer-scheduler');

        dp.resources = [
            { id: 'main', name: 'Моё расписание' },
        ];

        dp.startDate = container.dataset.startDate || new Date().toISOString().slice(0, 10);
        dp.days = 7;
        dp.scale = 'Hour';
        dp.timeHeaders = [
            { groupBy: 'Day', format: 'dd.MM.yyyy' },
            { groupBy: 'Hour' },
        ];

        const eventsJson = container.dataset.events;
        let events = [];
        if (eventsJson) {
            try {
                events = JSON.parse(eventsJson);
            } catch (e) {
                console.error('Failed to parse scheduler events', e);
            }
        }

        dp.onBeforeEventRender = function (args) {
            if (args.data.status === 'booked') {
                args.data.barColor = '#3b82f6';
            } else if (args.data.status === 'available') {
                args.data.barColor = '#22c55e';
            } else {
                args.data.barColor = '#ef4444';
            }
        };

        dp.events.list = events;
        dp.init();
    } catch (e) {
        console.error('Error initializing trainer scheduler', e);
    }
}

document.addEventListener('DOMContentLoaded', initTrainerScheduler);
document.addEventListener('livewire:navigated', initTrainerScheduler);
