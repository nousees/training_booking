<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Бронирование персональных тренировок</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-sans bg-gray-50">
        <div class="min-h-screen flex flex-col">
            <!-- Top navigation -->
            <header class="border-b bg-white/90 backdrop-blur">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="flex flex-col leading-tight">
                            <span class="text-lg font-semibold text-gray-900">
                                Солнечный <span class="text-emerald-500">Фитнес</span>
                            </span>
                            <span class="text-xs text-gray-500">Персональные тренировки онлайн и офлайн</span>
                        </div>
                    </div>
                    <nav class="hidden sm:flex items-center gap-6 text-sm text-gray-600">
                        @auth
                            @if(auth()->user()->isClient())
                                <a href="{{ route('profile') }}" class="hover:text-emerald-600">Личный кабинет</a>
                            @elseif(auth()->user()->isTrainer())
                                <a href="{{ route('trainer-panel.dashboard') }}" class="hover:text-emerald-600">Кабинет тренера</a>
                            @elseif(auth()->user()->isOwner())
                                <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600">Админ-панель</a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="hover:text-emerald-600">Войти</a>
                            <a href="{{ route('register') }}" class="hover:text-emerald-600">Регистрация</a>
                        @endauth
                    </nav>
                </div>
            </header>

            <!-- Hero section -->
            <main class="flex-1">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-24 grid lg:grid-cols-2 gap-10 items-center mt-[30px] "style="
    padding-top: 150px;">
                    <div class="space-y-6">
                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 leading-tight">
                            Удобное расписание персональных тренировок
                        </h1>
                        <p class="text-sm sm:text-base text-gray-600 max-w-xl">
                            Управляйте своим спортивным графиком: выбирайте тренера, следите за ближайшими тренировками и не пропускайте ни одного занятия.
                        </p>
                        <div class="grid grid-cols-3 gap-4 text-xs sm:text-sm text-gray-600">
                            <div>
                                <div class="text-lg font-semibold text-gray-900">7 дней</div>
                                <div>Недельное расписание тренера</div>
                            </div>
                            <div>
                                <div class="text-lg font-semibold text-gray-900">Онлайн</div>
                                <div>Тренировки в Zoom и других сервисах</div>
                            </div>
                            <div>
                                <div class="text-lg font-semibold text-gray-900">Напоминания</div>
                                <div>Уведомления о предстоящих занятиях</div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-center lg:justify-end">
                        <div class="relative w-full max-w-md h-[520px]">
                            <div class="w-full h-full rounded-2xl overflow-hidden border border-gray-100 bg-gray-200">
                            <img
                                    src="https://images.pexels.com/photos/841130/pexels-photo-841130.jpeg?auto=compress&cs=tinysrgb&w=600"
                                    alt="Фитнес-тренировка в зале"
                                    class="h-full w-full object-cover rounded-2xl"
                                    loading="lazy"
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <footer class="border-t bg-white py-3 mt-2">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center text-xs text-gray-500">
                    <span>© {{ date('Y') }} Солнечный Фитнес</span>
                    <span>Платформа бронирования тренировок</span>
                </div>
            </footer>
        </div>
    </body>
</html>
