<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatGPT на Laravel</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

<div class="w-full max-w-lg bg-white rounded-lg shadow-lg p-6">
    <h1 class="text-2xl font-semibold text-center mb-4">ChatGPT на Laravel</h1>

    <div id="messages" class="h-96 overflow-y-auto border rounded-lg p-4 space-y-2 bg-gray-50"></div>

    <form id="chatForm" class="mt-4 flex">
        @csrf
        <input type="text" id="userInput" name="message" placeholder="Введите сообщение..."
               class="flex-1 p-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300">
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

        const userMessageDiv = document.createElement("div");
        userMessageDiv.classList.add("bg-blue-500", "text-white", "p-2", "rounded-lg", "max-w-xs", "self-end");
        userMessageDiv.innerText = message;
        messagesDiv.appendChild(userMessageDiv);
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

            const botMessageDiv = document.createElement("div");
            botMessageDiv.classList.add("bg-gray-200", "p-2", "rounded-lg", "max-w-xs");
            botMessageDiv.innerText = reply;
            messagesDiv.appendChild(botMessageDiv);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        } catch (error) {
            console.error("Ошибка:", error);
        }
    });
</script>

</body>
</html>
