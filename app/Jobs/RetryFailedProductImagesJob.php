<?php

namespace App\Jobs;

use App\Services\ExcelProductImportService;
use App\Services\TelegramService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class RetryFailedProductImagesJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 300;

    public int $timeout = 7200;

    public function __construct(public string $reportPath) {}

    public function handle(ExcelProductImportService $products): void
    {
        $raw = Storage::disk('local')->get($this->reportPath);

        if (! is_string($raw) || $raw === '') {
            throw new RuntimeException('Отчёт импорта не найден.');
        }

        $report = json_decode($raw, true);

        if (! is_array($report)) {
            throw new RuntimeException('Отчёт импорта повреждён.');
        }

        $outcome = $products->retryProductImages($report['photos'] ?? []);
        $report['photos'] = $outcome['photos'];
        $report['photo_retry'] = $outcome['photo_retry'];
        $report['errors'] = $this->errorsAfterRetry(
            $report['errors'] ?? [],
            $outcome['errors'],
            $outcome['photos']
        );
        $report['finished_at'] = now()->toIso8601String();

        Storage::disk('local')->put(
            $this->reportPath,
            json_encode($report, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        );

        Log::info('Повторная загрузка фотографий завершена', [
            'report' => $this->reportPath,
            'remaining' => count($outcome['photo_retry']),
        ]);

        $finished = $outcome['photo_retry'] === []
            || ($this->job !== null && $this->attempts() >= $this->tries);

        if ($finished) {
            try {
                app(TelegramService::class)->sendImportReport($report, true);
            } catch (Throwable $exception) {
                Log::error('Не удалось отправить результат повторной загрузки фотографий в Telegram', [
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        if ($outcome['photo_retry'] !== [] && $this->job !== null && $this->attempts() < $this->tries) {
            throw new RuntimeException('Остались фотографии для повторной загрузки.');
        }
    }

    /**
     * @param  array<int, mixed>  $errors
     * @param  array<int, array<string, mixed>>  $retryErrors
     * @param  array<int, array<string, mixed>>  $photos
     * @return array<int, mixed>
     */
    private function errorsAfterRetry(array $errors, array $retryErrors, array $photos): array
    {
        $fresh = [];

        foreach ($retryErrors as $error) {
            $fresh[$this->errorKey($error)] = $error;
        }

        $resolved = [];

        foreach ($photos as $photo) {
            if (empty($photo['saved']) || ! isset($photo['url'])) {
                continue;
            }

            $resolved[$this->errorKey($photo)] = true;
        }

        $kept = [];

        foreach ($errors as $error) {
            if (! is_array($error)) {
                $kept[] = $error;

                continue;
            }

            $key = $this->errorKey($error);

            if (isset($fresh[$key]) || isset($resolved[$key])) {
                continue;
            }

            $kept[] = $error;
        }

        return array_values(array_merge($kept, array_values($fresh)));
    }

    /**
     * @param  array<string, mixed>  $error
     */
    private function errorKey(array $error): string
    {
        return ($error['row'] ?? '').'|'.($error['field'] ?? '').'|'.($error['url'] ?? '');
    }
}
