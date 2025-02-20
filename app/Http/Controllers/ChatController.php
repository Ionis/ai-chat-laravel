<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $messages = Message::where('user_id', Auth::id())->get();
        return view('chat', compact('messages'));
    }

    /**
     * @throws GuzzleException
     */
    public function send(Request $request)
    {
        $request->validate(['message' => 'required']);

        $userMessage = Message::create([
            'user_id' => Auth::id(),
            'message' => $request->message,
            'is_bot' => false,
        ]);

        $apiKey = config('services.openai.api_key');
        $userMessage = $request->input('message');

        if (!$userMessage) {
            return response()->json(['error' => 'No message provided'], 400);
        }

        $client = new Client();
        $response = $client->post('https://api.openai.com/v1/chat/completions', [
            'proxy' => config('services.openai.proxy_url'),
            'headers' => [
                'Authorization' => "Bearer $apiKey",
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-4',
                'messages' => [['role' => 'user', 'content' => $userMessage]],
            ],
        ]);

        $data = json_decode($response->getBody(), true);
        $botReply = $data['choices'][0]['message']['content'];

        $botMessage = Message::create([
            'user_id' => Auth::id(),
            'message' => $botReply,
            'is_bot' => true,
        ]);

        return response()->json(['reply' => $botMessage->message]);
    }
}
