<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Gym;
use App\Models\Trainer;
use App\Models\Training;
use App\Models\Booking;

class TrainingBookingSeeder extends Seeder
{
    public function run(): void
    {
        // Создаем владельца
        $owner = User::create([
            'name' => 'Владелец спортзала',
            'email' => 'owner@example.com',
            'password' => bcrypt('password'),
            'role' => 'owner',
            'phone' => '+7 (999) 123-45-67',
        ]);

        // Создаем менеджера
        $manager = User::create([
            'name' => 'Менеджер спортзала',
            'email' => 'manager@example.com',
            'password' => bcrypt('password'),
            'role' => 'manager',
            'phone' => '+7 (999) 123-45-68',
        ]);

        // Создаем клиентов
        $client1 = User::create([
            'name' => 'Иван Петров',
            'email' => 'client1@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'phone' => '+7 (999) 123-45-69',
        ]);

        $client2 = User::create([
            'name' => 'Мария Сидорова',
            'email' => 'client2@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'phone' => '+7 (999) 123-45-70',
        ]);

        // Создаем спортзалы
        $gym1 = Gym::create([
            'name' => 'Фитнес-центр "Сила"',
            'description' => 'Современный фитнес-центр с новейшим оборудованием',
            'address' => 'ул. Ленина, 123, Москва',
            'phone' => '+7 (495) 123-45-67',
            'email' => 'info@silafitness.ru',
            'opening_time' => '06:00',
            'closing_time' => '23:00',
            'is_active' => true,
            'owner_id' => $owner->id,
        ]);

        $gym2 = Gym::create([
            'name' => 'Спорт-клуб "Энергия"',
            'description' => 'Профессиональный спорт-клуб для всех уровней подготовки',
            'address' => 'пр. Мира, 456, Москва',
            'phone' => '+7 (495) 234-56-78',
            'email' => 'info@energysport.ru',
            'opening_time' => '07:00',
            'closing_time' => '22:00',
            'is_active' => true,
            'owner_id' => $owner->id,
        ]);

        // Создаем тренеров
        $trainer1 = Trainer::create([
            'name' => 'Алексей Смирнов',
            'bio' => 'Опытный тренер с 10-летним стажем. Специализируется на силовых тренировках и функциональном тренинге.',
            'specialization' => 'Силовые тренировки, функциональный тренинг',
            'experience_years' => 10,
            'photo_path' => null,
            'is_active' => true,
            'gym_id' => $gym1->id,
        ]);

        $trainer2 = Trainer::create([
            'name' => 'Елена Козлова',
            'bio' => 'Сертифицированный тренер по йоге и пилатесу. Помогает клиентам достичь гармонии тела и духа.',
            'specialization' => 'Йога, пилатес, растяжка',
            'experience_years' => 8,
            'photo_path' => null,
            'is_active' => true,
            'gym_id' => $gym1->id,
        ]);

        $trainer3 = Trainer::create([
            'name' => 'Дмитрий Волков',
            'bio' => 'Мастер спорта по тяжелой атлетике. Специализируется на работе с профессиональными спортсменами.',
            'specialization' => 'Тяжелая атлетика, пауэрлифтинг',
            'experience_years' => 15,
            'photo_path' => null,
            'is_active' => true,
            'gym_id' => $gym2->id,
        ]);

        // Создаем тренировки
        $training1 = Training::create([
            'name' => 'Персональная силовая тренировка',
            'description' => 'Индивидуальная тренировка с фокусом на развитие силы и мышечной массы',
            'duration_minutes' => 60,
            'price' => 3000.00,
            'max_participants' => 1,
            'is_active' => true,
            'gym_id' => $gym1->id,
            'trainer_id' => $trainer1->id,
        ]);

        $training2 = Training::create([
            'name' => 'Йога для начинающих',
            'description' => 'Групповое занятие йогой для тех, кто только начинает свой путь в практике',
            'duration_minutes' => 90,
            'price' => 1500.00,
            'max_participants' => 12,
            'is_active' => true,
            'gym_id' => $gym1->id,
            'trainer_id' => $trainer2->id,
        ]);

        $training3 = Training::create([
            'name' => 'Интенсивная тренировка с тренером',
            'description' => 'Высокоинтенсивная тренировка для опытных спортсменов',
            'duration_minutes' => 45,
            'price' => 2500.00,
            'max_participants' => 1,
            'is_active' => true,
            'gym_id' => $gym2->id,
            'trainer_id' => $trainer3->id,
        ]);

        // Создаем бронирования
        Booking::create([
            'user_id' => $client1->id,
            'training_id' => $training1->id,
            'start_time' => now()->addDays(1)->setTime(10, 0),
            'end_time' => now()->addDays(1)->setTime(11, 0),
            'status' => 'confirmed',
            'notes' => 'Первая тренировка, нужна консультация по питанию',
        ]);

        Booking::create([
            'user_id' => $client2->id,
            'training_id' => $training2->id,
            'start_time' => now()->addDays(2)->setTime(18, 0),
            'end_time' => now()->addDays(2)->setTime(19, 30),
            'status' => 'pending',
            'notes' => 'Хочу попробовать йогу впервые',
        ]);

        Booking::create([
            'user_id' => $client1->id,
            'training_id' => $training3->id,
            'start_time' => now()->addDays(3)->setTime(14, 0),
            'end_time' => now()->addDays(3)->setTime(14, 45),
            'status' => 'confirmed',
            'notes' => 'Подготовка к соревнованиям',
        ]);
    }
}

