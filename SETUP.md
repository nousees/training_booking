# Инструкция по запуску проекта Training Booking

## Предварительные требования

- Docker и Docker Compose установлены
- Git (опционально)

## Шаг 1: Настройка окружения

1. Создайте файл `.env` из примера (если его еще нет):
```bash
cp .env.example .env
```

2. Убедитесь, что в `.env` указаны правильные настройки для Docker:
```env
APP_NAME="Training Booking"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8001

DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=training_db
DB_USERNAME=training_user
DB_PASSWORD=training_password

REDIS_HOST=redis
REDIS_PORT=6379
QUEUE_CONNECTION=redis
CACHE_STORE=redis
SESSION_DRIVER=redis
```

## Шаг 2: Запуск Docker контейнеров

```bash
# Сборка и запуск всех контейнеров
docker-compose up -d --build

# Проверка статуса контейнеров
docker-compose ps
```

Должны запуститься следующие контейнеры:
- `training_app` - PHP-FPM приложение
- `training_nginx` - Nginx веб-сервер (порт 8001)
- `training_db` - PostgreSQL база данных (порт 5436)
- `training_redis` - Redis (порт 6379)
- `training_queue` - Обработчик очередей
- `training_scheduler` - Планировщик задач
- `training_node` - Node.js для сборки фронтенда

## Шаг 3: Установка зависимостей

```bash
# Установка PHP зависимостей
docker-compose exec app composer install

# Установка Node.js зависимостей
docker-compose exec node npm install
```

## Шаг 4: Настройка Laravel

```bash
# Генерация ключа приложения
docker-compose exec app php artisan key:generate

# Очистка кэша конфигурации
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
```

## Шаг 5: Запуск миграций и заполнение базы данных

```bash
# Запуск миграций
docker-compose exec app php artisan migrate

# Заполнение базы тестовыми данными
docker-compose exec app php artisan db:seed
```

## Шаг 6: Сборка фронтенда

```bash
# Сборка assets для production
docker-compose exec node npm run build

# Или для разработки (watch mode)
docker-compose exec node npm run dev
```

## Шаг 7: Проверка работы

Откройте в браузере: **http://localhost:8001**

## Тестовые аккаунты

После выполнения `php artisan db:seed` будут созданы следующие тестовые аккаунты:

### Владелец платформы (Owner)
- **Email:** owner@example.com
- **Password:** password
- **Доступ:** `/admin`

### Тренеры (Trainers)
- **Email:** trainer1@example.com - trainer5@example.com
- **Password:** password
- **Доступ:** `/trainer-panel/schedule`, `/trainer-panel/bookings`

### Клиенты (Clients)
- **Email:** client1@example.com - client10@example.com
- **Password:** password
- **Доступ:** `/trainers`, `/profile/bookings`

## Полезные команды

### Просмотр логов
```bash
# Логи всех контейнеров
docker-compose logs -f

# Логи конкретного контейнера
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f postgres
```

### Выполнение Artisan команд
```bash
docker-compose exec app php artisan [command]
```

### Доступ к базе данных
```bash
# Через psql
docker-compose exec postgres psql -U training_user -d training_db

# Или через внешний клиент
# Host: localhost
# Port: 5436
# Database: training_db
# User: training_user
# Password: training_password
```

### Очистка и перезапуск
```bash
# Остановка контейнеров
docker-compose down

# Остановка с удалением volumes (удалит все данные БД!)
docker-compose down -v

# Пересборка после изменений
docker-compose up -d --build
```

### Сброс базы данных
```bash
# Удаление всех таблиц и повторное создание
docker-compose exec app php artisan migrate:fresh --seed
```

## Тестирование функционала

### 1. Регистрация нового пользователя
- Перейдите на `/register`
- Выберите роль (Client или Trainer)
- Заполните форму и зарегистрируйтесь

### 2. Просмотр тренеров (как клиент)
- Войдите как клиент
- Перейдите на `/trainers`
- Используйте фильтры для поиска тренеров
- Откройте профиль тренера

### 3. Бронирование тренировки (как клиент)
- На странице профиля тренера выберите доступный слот
- Нажмите "Book Now"
- Подтвердите бронирование

### 4. Управление расписанием (как тренер)
- Войдите как тренер
- Перейдите на `/trainer-panel/schedule`
- Создайте новые слоты тренировок
- Редактируйте или удаляйте существующие

### 5. Управление бронированиями (как тренер)
- Перейдите на `/trainer-panel/bookings`
- Подтвердите или отмените бронирования

### 6. Администрирование (как владелец)
- Войдите как owner@example.com
- Перейдите на `/admin`
- Управляйте пользователями, модерацией отзывов и настройками платформы

## Решение проблем

### Ошибка подключения к базе данных
```bash
# Проверьте, что контейнер postgres запущен
docker-compose ps postgres

# Проверьте логи
docker-compose logs postgres
```

### Ошибки прав доступа
```bash
# Исправление прав на storage и cache
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### Проблемы с миграциями
```bash
# Сброс миграций
docker-compose exec app php artisan migrate:fresh

# Повторный запуск сидера
docker-compose exec app php artisan db:seed
```

### Проблемы с фронтендом
```bash
# Пересборка assets
docker-compose exec node npm run build

# Очистка кэша Vite
docker-compose exec node rm -rf node_modules/.vite
```

## Структура проекта

- `/trainers` - Список тренеров (публичная)
- `/trainer/{id}` - Профиль тренера (публичная)
- `/profile` - Профиль клиента
- `/profile/bookings` - Бронирования клиента
- `/trainer-panel/schedule` - Управление расписанием (тренер)
- `/trainer-panel/bookings` - Управление бронированиями (тренер)
- `/admin` - Админ-панель (владелец)

## Дополнительная информация

- Приложение использует PostgreSQL для хранения данных
- Redis используется для кэширования, сессий и очередей
- Очереди обрабатываются автоматически в контейнере `training_queue`
- Планировщик задач запускается в контейнере `training_scheduler`
- Все задачи выполняются ежедневно в полночь (UTC)


