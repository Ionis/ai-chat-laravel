<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ChatGPT на Laravel (Livewire)') }}</title>
    @vite('resources/css/app.css')
    @livewireStyles
</head>
<body class="antialiased bg-gray-100 dark:bg-gray-900 h-screen flex">
<!-- Основной контейнер -->
<div class="flex flex-1 h-full">
    <!-- Чат -->
    @livewire('chat')

    <!-- Боковая панель -->
    <aside class="w-64 bg-gray-200 dark:bg-gray-700 p-6 flex flex-col md:flex">
        <form method="POST" action="{{ route('logout') }}" class="flex flex-col">
            @csrf

            <button class="mb-4 bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                Выйти
            </button>
        </form>

        <h2 class="text-lg font-semibold dark:text-white">Меню</h2>
        <ul class="mt-2 space-y-2">
            <li><a href="#" class="block p-2 bg-gray-300 dark:bg-gray-600 rounded-lg">История чатов</a></li>
            <li><a href="#" class="block p-2 bg-gray-300 dark:bg-gray-600 rounded-lg">Настройки</a></li>
        </ul>
    </aside>
</div>

@livewireScripts

</body>
</html>
