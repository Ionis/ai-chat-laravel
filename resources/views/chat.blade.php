<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatGPT на Laravel</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 dark:bg-gray-900 flex items-center justify-center h-screen">

<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
        Выйти
    </button>
</form>

<div class="w-full max-w-lg bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
    <h1 class="text-2xl font-semibold text-center dark:text-white">ChatGPT на Laravel</h1>

    <div id="messages" class="h-96 overflow-y-auto border rounded-lg p-4 space-y-2 bg-gray-50 dark:bg-gray-700">
        @foreach($messages as $msg)
            <div class="flex {{ $msg->is_bot ? 'justify-start' : 'justify-end' }} items-center space-x-2">
                @if($msg->is_bot)
                    <img src="{{ asset('/images/bot-avatar.png') }}" class="w-8 h-8 rounded-full">
                    <span class="bg-gray-200 dark:bg-gray-600 text-black dark:text-white p-2 rounded-lg max-w-xs">
                    {{ $msg->message }}
                </span>
                @else
                    <span class="bg-blue-500 text-white p-2 rounded-lg max-w-xs">{{ $msg->message }}</span>
                    <img src="{{ asset('/images/user-avatar.jpg') }}" class="w-8 h-8 rounded-full">
                @endif
            </div>
        @endforeach
    </div>

    <form id="chatForm" class="mt-4 flex">
        <input type="text" id="userInput" name="message" placeholder="Введите сообщение..."
               class="flex-1 p-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300 dark:bg-gray-600 dark:text-white">
        <button type="submit" class="ml-2 bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">
            Отправить
        </button>
    </form>
</div>

<script>
    document.getElementById("chatForm").addEventListener("submit", async function(event) {
        event.preventDefault();

        const userInput = document.getElementById("userInput");
        const messagesDiv = document.getElementById("messages");
        const message = userInput.value.trim();
        if (!message) return;

        // Добавление сообщения пользователя с аватаркой
        messagesDiv.innerHTML += `
                <div class="flex justify-end items-center space-x-2">
                    <span class="bg-blue-500 text-white p-2 rounded-lg max-w-xs">${message}</span>
                    <img src="/images/user-avatar.jpg" class="w-8 h-8 rounded-full">
                </div>
            `;
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
        userInput.value = "";

        try {
            const response = await fetch("{{ route('chat.send') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ message })
            });

            const data = await response.json();
            const reply = data.reply || "Ошибка ответа";

            // Добавление ответа бота с аватаркой
            messagesDiv.innerHTML += `
                    <div class="flex justify-start items-center space-x-2">
                        <img src="/images/bot-avatar.png" class="w-8 h-8 rounded-full">
                        <span class="bg-gray-200 dark:bg-gray-600 text-black dark:text-white p-2 rounded-lg max-w-xs">${reply}</span>
                    </div>
                `;
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        } catch (error) {
            console.error("Ошибка:", error);
        }
    });
</script>

</body>
</html>
