<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramService
{
    /**
     * Отправить текстовое сообщение.
     */
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

    /**
     * Отправить файл.
     */
    public function sendDocument(string $filePath, ?string $caption = null): array
    {
        if (!file_exists($filePath)) {
            return [
                'status' => 0,
                'body' => 'File not found: ' . $filePath,
            ];
        }

        $request = Http::timeout(30)
            ->attach(
                'document',
                fopen($filePath, 'r'),
                basename($filePath)
            );

        $response = $request->post(
            'https://api.telegram.org/bot' . config('services.telegram.bot_token') . '/sendDocument',
            [
                'chat_id' => config('services.telegram.manager_chat_id'),
                'caption' => $caption,
            ]
        );

        return [
            'status' => $response->status(),
            'body' => $response->body(),
        ];
    }
}
