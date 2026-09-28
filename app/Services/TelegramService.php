<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramService
{
    /**
     * Отправить текстовое сообщение.
     */
    public function sendMessage(string $message, ?string $chatId = null): array
    {
        $response = Http::timeout(10)->post(
            $this->apiUrl('sendMessage'),
            [
                'chat_id' => $chatId ?: config('services.telegram.manager_chat_id'),
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

    public function importIsAllowed(string $userId): bool
    {
        $userId = trim($userId);

        if ($userId === '') {
            return false;
        }

        $allowed = array_filter(array_map(
            'trim',
            explode(',', (string) config('services.telegram.import_user_ids'))
        ));
        $manager = trim((string) config('services.telegram.manager_chat_id'));

        if ($manager !== '') {
            $allowed[] = $manager;
        }

        return in_array($userId, $allowed, true);
    }

    public function downloadFile(string $fileId): ?string
    {
        $meta = Http::timeout(20)->get($this->apiUrl('getFile'), [
            'file_id' => $fileId,
        ]);
        $path = $meta->json('result.file_path');

        if (! $meta->successful() || ! is_string($path) || $path === '') {
            return null;
        }

        $file = Http::timeout(60)->get($this->fileUrl($path));

        if (! $file->successful() || $file->body() === '') {
            return null;
        }

        return $file->body();
    }

    /**
     * @param  array<string, mixed>  $report
     */
    public function sendImportReport(array $report, bool $afterPhotoRetry = false): void
    {
        $chatId = $report['telegram']['chat_id'] ?? null;

        if (! is_string($chatId) || $chatId === '') {
            return;
        }

        $this->sendMessage($this->importReportText($report, $afterPhotoRetry), $chatId);
    }

    /**
     * @param  array<string, mixed>  $report
     */
    public function importReportText(array $report, bool $afterPhotoRetry = false): string
    {
        $photos = collect($report['photos'] ?? []);
        $loaded = $photos->where('saved', true)->count();
        $failed = $photos->filter(fn ($photo) => empty($photo['saved']))->count();
        $productErrors = collect($report['errors'] ?? [])
            ->filter(fn ($error) => is_array($error) && empty($error['url']))
            ->unique(fn ($error) => $error['row'] ?? '')
            ->count();
        $inFile = (int) ($report['imported'] ?? 0) + (int) ($report['updated'] ?? 0) + $productErrors;
        $name = (string) ($report['telegram']['original_name'] ?? 'файл');
        $heading = $afterPhotoRetry
            ? 'Повторная загрузка фотографий завершена.'
            : 'Импорт завершён.';

        $text = $heading."\n\n"
            ."Файл: {$name}\n\n"
            ."Товаров в файле: {$inFile}\n"
            .'Создано: '.(int) ($report['imported'] ?? 0)."\n"
            .'Обновлено: '.(int) ($report['updated'] ?? 0)."\n\n"
            ."Фотографий:\n"
            ."Загружено: {$loaded}\n"
            ."Не загружено: {$failed}\n\n"
            ."Ошибок импорта: {$productErrors}";

        if (($report['photo_retry'] ?? []) !== []) {
            $text .= "\n\nНе все фотографии удалось загрузить. Повторная загрузка отсутствующих фотографий поставлена в очередь. Уже загруженные файлы повторно не скачиваются.";
        } elseif ($failed > 0) {
            $text .= "\n\nЧасть фотографий не загружена. Повторный импорт того же файла догрузит только отсутствующие фотографии.";
        }

        return $text;
    }

    private function apiUrl(string $method): string
    {
        return 'https://api.telegram.org/bot'.config('services.telegram.bot_token').'/'.$method;
    }

    private function fileUrl(string $path): string
    {
        return 'https://api.telegram.org/file/bot'.config('services.telegram.bot_token').'/'.$path;
    }
}
