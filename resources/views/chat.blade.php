<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatGPT на Laravel</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 dark:bg-gray-900 h-screen flex">
<!-- Основной контейнер -->
<div class="flex flex-1 h-full">
    <!-- Чат -->
    <div class="flex-1 flex flex-col p-6 bg-white dark:bg-gray-800">
        <h1 class="text-2xl font-semibold text-center dark:text-white mb-4">ChatGPT на Laravel</h1>

        <div id="messages" class="flex-grow overflow-y-auto border rounded-lg p-4 space-y-2 bg-gray-50 dark:bg-gray-700">
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

<script>
    document.getElementById("chatForm").addEventListener("submit", async function(event) {
        event.preventDefault();

        const userInput = document.getElementById("userInput");
        const messagesDiv = document.getElementById("messages");
        const message = userInput.value.trim();
        if (!message) return;

        try {
            const response = await fetch("{{ route('chat.send') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ message })
            });

            if (!response.ok) {
                throw new Error("Ошибка при отправке сообщения.");
            }

            const data = await response.json();
            const reply = data.reply || "Ошибка ответа";

            // Функция для добавления сообщений в чат
            function addMessage(text, isBot) {
                const messageDiv = document.createElement("div");
                messageDiv.classList.add("flex", "items-center", "space-x-2", isBot ? "justify-start" : "justify-end");

                const avatar = document.createElement("img");
                avatar.src = isBot ? "/images/bot-avatar.png" : "/images/user-avatar.jpg";
                avatar.classList.add("w-8", "h-8", "rounded-full");

                const messageSpan = document.createElement("span");
                messageSpan.classList.add(isBot ? "bg-gray-200" : "bg-blue-500", "text-white", "p-2", "rounded-lg", "max-w-xs");
                messageSpan.textContent = text;

                if (isBot) {
                    messageDiv.appendChild(avatar);
                    messageDiv.appendChild(messageSpan);
                } else {
                    messageDiv.appendChild(messageSpan);
                    messageDiv.appendChild(avatar);
                }

                messagesDiv.appendChild(messageDiv);
                messagesDiv.scrollTop = messagesDiv.scrollHeight;
            }

            addMessage(message, false); // Сообщение пользователя
            addMessage(reply, true); // Ответ бота
            userInput.value = "";
        } catch (error) {
            console.error("Ошибка:", error);
            alert("Не удалось отправить сообщение. Проверьте подключение к сети.");
        }
    });
</script>

</body>
</html>
