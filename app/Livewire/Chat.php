<?php

namespace App\Livewire;

use App\Models\Message;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Chat extends Component
{
    public $chatHistory;
    public $prompt = '';
    public $answer = '';
    public $question = '';
    public $messages = [];

    public function mount(): void
    {
        // Загружаем историю из базы данных
        $this->messages = Message::where('user_id', Auth::id())
            ->get()
            ->map(function($msg) {
                return [
                    'id' => uniqid(),
                    'question' => $msg->question,
                    'answer' => $msg->answer
                ];
            })
            ->toArray();
    }

    function submitPrompt(): void
    {
        $this->question = $this->prompt;

        $this->messages[] = [
            'id' => uniqid(),
            'question' => $this->question,
            'answer' => ''
        ];

        $this->prompt = '';
        $this->answer = '';

        $this->dispatch('messageAdded');
        $this->js('$wire.ask()');
    }

    function ask(): void
    {
        $client = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . config('openai.api_key'),
                'Content-Type' => 'application/json',
            ],
            'proxy' => config('openai.proxy_url'),
        ]);

        try {
            $response = $client->post('chat/completions', [
                'json' => [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'user', 'content' => $this->question],
                    ],
                    'stream' => true,
                ],
                'stream' => true,
            ]);

            $body = $response->getBody();
            $buffer = '';
            $lastMessageId = $this->messages[count($this->messages) - 1]['id'];
            $fullAnswer = '';

            while (!$body->eof()) {
                $buffer .= $body->read(1024);

                while (($newlinePos = strpos($buffer, "\n")) !== false) {
                    $line = substr($buffer, 0, $newlinePos);
                    $buffer = substr($buffer, $newlinePos + 1);

                    $line = trim($line);
                    if (str_starts_with($line, 'data: ')) {
                        $json = substr($line, 6);
                        if ($json === '[DONE]') break 2;

                        $payload = json_decode($json, true);
                        if (isset($payload['choices'][0]['delta']['content'])) {
                            $chunk = $payload['choices'][0]['delta']['content'];
                            $this->stream(to: "answer_{$lastMessageId}", content: $chunk);
                            $this->messages[count($this->messages) - 1]['answer'] .= $chunk;
                            $fullAnswer .= $chunk;
                            $this->dispatch('answerUpdated');
                        }
                    }
                }
            }

            // Сохраняем полный ответ в базу данных
            Message::create([
                'user_id' => Auth::id(),
                'question' => $this->question,
                'answer' => $fullAnswer,
            ]);

        } catch (RequestException $e) {
            $error = 'Ошибка: ' . $e->getMessage();
            $this->answer = $error;
            $this->messages[count($this->messages) - 1]['answer'] = $error;
            $this->dispatch('answerUpdated');
        }
    }

    public function clearChat(): void
    {
        // Очищаем историю в браузере
        $this->messages = [];

        // Очищаем историю в базе данных
        Message::where('user_id', Auth::id())->delete();
    }

    public function render()
    {
        return view('livewire.chat');
    }
}
