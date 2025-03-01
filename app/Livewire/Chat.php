<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class Chat extends Component
{
    public $chatHistory;
    public $userInput = '';
    public $streamingReply = '';

    public function mount(): void
    {
        $this->chatHistory = Message::where('user_id', Auth::id())->get();
    }

    public function sendMessage(): void
    {
        if (!$this->userInput) return;

        $this->validate(['userInput' => 'required']);

        // Добавляем сообщение пользователя в БД
        Message::create([
            'user_id' => Auth::id(),
            'message' => $this->userInput,
            'is_bot' => false,
        ]);

        // Обновляем список сообщений в интерфейсе
        $this->chatHistory = Message::where('user_id', Auth::id())->get();

        // Отправляем запрос к OpenAI API через поток
        $this->streamingReply = '';
        $this->streamResponse($this->userInput);

        // Очищаем поле ввода
        $this->userInput = '';
    }

    public function streamResponse($userMessage): void
    {
        $apiKey = config('services.openai.api_key');
        $proxyUrl = config('services.openai.proxy_url');

        $response = Http::withHeaders([
            'Authorization' => "Bearer $apiKey",
            'Content-Type' => 'application/json',
        ])
            ->withOptions(['proxy' => $proxyUrl])
            ->timeout(60)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4',
                'messages' => [['role' => 'user', 'content' => $userMessage]],
                'stream' => true, // Включаем потоковую передачу
            ]);

        // Читаем поток
        foreach (explode("\n", $response->body()) as $chunk) {
            $chunk = trim($chunk);

            // Игнорируем пустые строки и строки с "data: [DONE]"
            if (empty($chunk) || $chunk === 'data: [DONE]') {
                continue;
            }

            // Убираем "data: " и декодируем JSON
            if (str_starts_with($chunk, 'data: ')) {
                $json = substr($chunk, 6);
            } else {
                $json = $chunk;
            }

            $data = json_decode($json, true);

            if (isset($data['choices'][0]['delta']['content'])) {
                $this->streamingReply .= $data['choices'][0]['delta']['content'];
            }
        }

        // Сохраняем полный ответ в базу
        Message::create([
            'user_id' => Auth::id(),
            'message' => $this->streamingReply,
            'is_bot' => true,
        ]);

        // Обновляем список сообщений
        $this->chatHistory = Message::where('user_id', Auth::id())->get();
    }

    public function render()
    {
        return view('livewire.chat');
    }
}
