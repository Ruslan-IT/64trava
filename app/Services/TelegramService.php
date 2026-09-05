<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramService
{
    public function sendMessage(string $message): array
    {
        $response = Http::timeout(10)->post(
            'https://api.telegram.org/bot' . config('services.telegram.bot_token') . '/sendMessage',
            [
                'chat_id' => config('services.telegram.manager_chat_id'),
                'text' => $message,
            ]
        );

        return [
            'status' => $response->status(),
            'body' => $response->body(),
        ];
    }
}
