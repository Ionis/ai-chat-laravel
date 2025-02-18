<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatGPT на Laravel</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background: #f4f4f4; }
        .chat-container { width: 400px; margin: 50px auto; padding: 10px; border-radius: 10px; background: white; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        .messages { height: 300px; overflow-y: auto; margin-bottom: 10px; padding: 10px; background: #f9f9f9; border-radius: 5px; }
        .message { padding: 8px; margin: 5px; border-radius: 5px; max-width: 70%; }
        .user { background: #007bff; color: white; text-align: right; }
        .bot { background: #ddd; color: black; text-align: left; }
        .input-area { display: flex; gap: 10px; }
        input { flex: 1; padding: 8px; }
        button { padding: 8px; background: #007bff; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
<div class="chat-container">
    <h1>ChatGPT на Laravel</h1>
    <div class="messages" id="messages"></div>
    <form id="chatForm">
        <div class="input-area">
            <input type="text" id="userInput" name="message" placeholder="Введите сообщение...">
            <button type="submit">Отправить</button>
        </div>
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
        userMessageDiv.classList.add("message", "user");
        userMessageDiv.innerText = message;
        messagesDiv.appendChild(userMessageDiv);
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
            botMessageDiv.classList.add("message", "bot");
            botMessageDiv.innerText = reply;
            messagesDiv.appendChild(botMessageDiv);
        } catch (error) {
            console.error("Ошибка:", error);
        }
    });
</script>
</body>
</html>
