<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Review;
use App\Models\SystemSetting;
use App\Models\TrainerProfile;
use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        SystemSetting::firstOrCreate(['id' => 1], [
            'platform_commission_percent' => 10.00,
            'cancellation_window_hours' => 24,
            'currency' => 'RUB',
            'maintenance_mode' => false,
        ]);

        $owner = User::create([
            'name' => 'Админ',
            'email' => 'owner@example.com',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'email_verified_at' => now(),
        ]);

        $trainers = [];
        $specializations = ['силовые', 'йога', 'снижение веса', 'кардио', 'пилатес', 'кроссфит', 'бокс', 'боевые искусства'];
        $trainerNames = [
            'Алексей Иванов',
            'Мария Смирнова',
            'Дмитрий Кузнецов',
            'Екатерина Попова',
            'Иван Соколов',
        ];
        
        $defaultImages = [
            'https://images.pexels.com/photos/1552249/pexels-photo-1552249.jpeg?auto=compress&cs=tinysrgb&w=600',
            'https://images.pexels.com/photos/7676402/pexels-photo-7676402.jpeg?auto=compress&cs=tinysrgb&w=600',
            'https://images.pexels.com/photos/841130/pexels-photo-841130.jpeg?auto=compress&cs=tinysrgb&w=600',
            'https://images.pexels.com/photos/6456307/pexels-photo-6456307.jpeg?auto=compress&cs=tinysrgb&w=600',
            'https://images.pexels.com/photos/414029/pexels-photo-414029.jpeg?auto=compress&cs=tinysrgb&w=600',
        ];

        for ($i = 1; $i <= 5; $i++) {
            $trainer = User::create([
                'name' => $trainerNames[$i-1] ?? "Тренер {$i}",
                'email' => "trainer{$i}@example.com",
                'password' => Hash::make('password'),
                'role' => 'trainer',
                'phone' => '+7 900 000-0' . $i,
                'email_verified_at' => now(),
            ]);

            $profile = TrainerProfile::create([
                'user_id' => $trainer->id,
                'bio' => "Опытный тренер с {$i}0-летним стажем в фитнесе и здоровье.",
                'specializations' => array_slice($specializations, 0, rand(2, 4)),
                'experience_years' => rand(2, 15),
                'price_per_hour' => rand(30, 100) + (rand(0, 99) / 100),
                'rating' => round(rand(35, 50) / 10, 1),
                'images' => [$defaultImages[$i-1] ?? $defaultImages[0]],
            ]);

            $trainers[] = $trainer;
        }

        $clients = [];
        $clientNames = [
            'Сергей', 'Анна', 'Ольга', 'Николай', 'Павел', 'Ирина', 'Ксения', 'Владимир', 'Татьяна', 'Юлия'
        ];
        for ($i = 1; $i <= 10; $i++) {
            $client = User::create([
                'name' => $clientNames[$i-1] ?? "Клиент {$i}",
                'email' => "client{$i}@example.com",
                'password' => Hash::make('password'),
                'role' => 'client',
                'phone' => '+7 901 000-0' . ($i % 10),
                'email_verified_at' => now(),
            ]);
            $clients[] = $client;
        }

        $sessions = [];
        foreach ($trainers as $trainer) {
            for ($day = 0; $day < 7; $day++) {
                $date = now()->addDays($day);

                // Старт рабочего дня тренера и конец дня
                $dayStart = $date->copy()->setTime(9, 0);
                $dayEnd = $date->copy()->setTime(21, 0);

                // Сколько тренировок в день (будут идти последовательно)
                $sessionsPerDay = rand(3, 6);

                $slotStart = $dayStart->copy();

                for ($s = 0; $s < $sessionsPerDay && $slotStart < $dayEnd; $s++) {
                    // Длительность 30 или 60 минут
                    $durationMinutes = [30, 60][array_rand([0, 1])];

                    /** @var Carbon $slotEnd */
                    $slotEnd = $slotStart->copy()->addMinutes($durationMinutes);

                    // Если слот выходит за конец дня — прекращаем цикл
                    if ($slotEnd > $dayEnd) {
                        break;
                    }

                    $session = TrainingSession::create([
                        'trainer_id' => $trainer->id,
                        'date' => $date->format('Y-m-d'),
                        'start_time' => $slotStart->format('H:i:s'),
                        'end_time' => $slotEnd->format('H:i:s'),
                        'location' => rand(0, 1)
                            ? 'г. Москва, ул. Спортивная, д. ' . rand(1, 99)
                            : 'Ссылка на Zoom: ' . Str::random(10),
                        'price' => $trainer->trainerProfile->price_per_hour * ($durationMinutes / 60),
                        'status' => rand(0, 1) ? 'available' : 'booked',
                    ]);

                    $sessions[] = $session;

                    if ($session->status === 'booked' && !empty($clients)) {
                        $client = $clients[array_rand($clients)];
                        $booking = Booking::create([
                            'session_id' => $session->id,
                            'user_id' => $client->id,
                            'status' => rand(0, 1) ? 'pending' : 'confirmed',
                            'payment_status' => rand(0, 1) ? 'unpaid' : 'paid',
                        ]);

                        if (rand(0, 1) && $booking->status === 'confirmed') {
                            Review::create([
                                'booking_id' => $booking->id,
                                'user_id' => $client->id,
                                'trainer_id' => $trainer->id,
                                'rating' => rand(3, 5),
                                'comment' => rand(0, 1) ? 'Отличная тренировка! Рекомендую.' : null,
                            ]);
                            $trainer->trainerProfile->updateRating();
                        }
                    }

                    // Небольшой перерыв между тренировками 0 или 30 минут
                    $breakMinutes = rand(0, 1) ? 30 : 0;
                    $slotStart = $slotEnd->copy()->addMinutes($breakMinutes);
                }
            }
        }


        $now = now();
        $pastBookings = Booking::whereIn('status', ['pending', 'confirmed'])
            ->whereHas('session', function ($q) use ($now) {
                $q->where(function ($qq) use ($now) {
                    $qq->where('date', '<', $now->toDateString())
                       ->orWhere(function ($qqq) use ($now) {
                           $qqq->where('date', '=', $now->toDateString())
                               ->where('end_time', '<', $now->format('H:i:s'));
                       });
                });
            })
            ->get();

        foreach ($pastBookings as $b) {
            $b->update(['status' => 'completed']);
        }


        if (!empty($trainers) && count($clients) >= 2) {
            $trainer = $trainers[0];
            $yesterday = now()->subDay()->format('Y-m-d');


            $session1 = TrainingSession::create([
                'trainer_id' => $trainer->id,
                'date' => $yesterday,
                'start_time' => '10:00:00',
                'end_time' => '11:00:00',
                'location' => 'г. Москва, ул. Спортивная, д. 7',
                'price' => $trainer->trainerProfile->price_per_hour,
                'status' => 'booked',
            ]);
            Booking::create([
                'session_id' => $session1->id,
                'user_id' => $clients[0]->id, // client1
                'status' => 'completed',
                'payment_status' => 'paid',
            ]);


            $session2 = TrainingSession::create([
                'trainer_id' => $trainer->id,
                'date' => $yesterday,
                'start_time' => '12:00:00',
                'end_time' => '13:00:00',
                'location' => 'г. Москва, ул. Спортивная, д. 9',
                'price' => $trainer->trainerProfile->price_per_hour,
                'status' => 'booked',
            ]);
            Booking::create([
                'session_id' => $session2->id,
                'user_id' => $clients[1]->id, // client2
                'status' => 'completed',
                'payment_status' => 'paid',
            ]);
        }
    }
}
