<?php

namespace App\Http\Controllers;

use App\Jobs\ImportProductsFromExcelJob;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TelegramWebhookController
{
    public function __invoke(Request $request, TelegramService $telegram): Response
    {
        $secret = (string) config('services.telegram.webhook_secret');

        if ($secret !== '' && $request->header('X-Telegram-Bot-Api-Secret-Token') !== $secret) {
            abort(403);
        }

        $message = $request->input('message');

        if (! is_array($message) || ! isset($message['document']) || ! is_array($message['document'])) {
            return response('ok');
        }

        $chatId = (string) ($message['chat']['id'] ?? '');
        $userId = (string) ($message['from']['id'] ?? '');

        if (! $telegram->importIsAllowed($userId)) {
            if ($chatId !== '') {
                $telegram->sendMessage('Недостаточно прав для импорта товаров.', $chatId);
            }

            return response('ok');
        }

        $document = $message['document'];
        $originalName = (string) ($document['file_name'] ?? 'products.csv');
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (! in_array($extension, ['csv', 'xlsx'], true)) {
            $telegram->sendMessage('Нужен файл CSV или XLSX с товарами.', $chatId);

            return response('ok');
        }

        if ($this->importIsQueued()) {
            $telegram->sendMessage('Импорт уже выполняется. Новый файл можно отправить после его завершения.', $chatId);

            return response('ok');
        }

        $fileId = (string) ($document['file_id'] ?? '');
        $contents = $fileId !== '' ? $telegram->downloadFile($fileId) : null;

        if (! is_string($contents) || ! $this->acceptableSpreadsheet($contents, $extension)) {
            $telegram->sendMessage('Файл не похож на допустимый CSV или XLSX.', $chatId);

            return response('ok');
        }

        $storedPath = 'imports/'.Str::uuid()->toString().'.'.$extension;
        Storage::disk('local')->put($storedPath, $contents);
        Storage::disk('local')->put($storedPath.'.telegram.json', json_encode([
            'chat_id' => $chatId,
            'original_name' => $originalName,
        ], JSON_UNESCAPED_UNICODE));

        ImportProductsFromExcelJob::dispatch($storedPath, '');
        $telegram->sendMessage('Файл принят. Импорт поставлен в очередь.', $chatId);

        return response('ok');
    }

    private function importIsQueued(): bool
    {
        return DB::table('jobs')
            ->where('payload', 'like', '%ImportProductsFromExcelJob%')
            ->exists();
    }

    private function acceptableSpreadsheet(string $contents, string $extension): bool
    {
        if ($contents === '') {
            return false;
        }

        if ($extension === 'xlsx') {
            return str_starts_with($contents, 'PK');
        }

        if (str_contains($contents, "\0")) {
            return false;
        }

        $line = strtok($contents, "\r\n") ?: '';

        return str_contains($line, ',') || str_contains($line, ';') || str_contains($line, "\t");
    }
}
