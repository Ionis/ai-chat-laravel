<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class ChatController extends Controller
{
    public function index()
    {
        return view('chat');
    }

    /**
     * @throws GuzzleException
     */
    public function chat(Request $request)
    {
        $apiKey = config('services.openai.api_key');
        $userMessage = $request->input('message');

        if (!$userMessage) {
            return response()->json(['error' => 'No message provided'], 400);
        }

        $client = new Client();
        $response = $client->post('https://api.openai.com/v1/chat/completions', [
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
        return response()->json(['reply' => $data['choices'][0]['message']['content']]);
    }
}
