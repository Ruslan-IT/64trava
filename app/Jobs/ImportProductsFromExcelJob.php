<?php

namespace App\Jobs;

use App\Services\ExcelProductImportService;
use App\Services\TelegramService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class ImportProductsFromExcelJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 7200;

    public int $backoff = 30;

    public function __construct(
        public string $path,
        public string $brand = '',
    ) {}

    /**
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [
            (new WithoutOverlapping('product-excel-import'))
                ->releaseAfter(60)
                ->expireAfter(7500),
        ];
    }

    public function handle(ExcelProductImportService $products): void
    {
        $startedAt = now()->toIso8601String();
        $absolute = Storage::disk('local')->path($this->path);

        if (! is_file($absolute)) {
            throw new RuntimeException('Файл импорта не найден.');
        }

        $result = $products->import($absolute, $this->brand);
        $reportPath = 'imports/reports/'.Str::uuid()->toString().'.json';
        $noticePath = $this->path.'.telegram.json';
        $notice = null;

        if (Storage::disk('local')->exists($noticePath)) {
            $decoded = json_decode((string) Storage::disk('local')->get($noticePath), true);
            $notice = is_array($decoded) ? $decoded : null;
        }

        $report = [
            'file' => $this->path,
            'imported' => $result['imported'] ?? 0,
            'updated' => $result['updated'] ?? 0,
            'skipped' => $result['skipped'] ?? 0,
            'errors' => $result['errors'] ?? [],
            'warnings' => $result['warnings'] ?? [],
            'photo_retry' => $result['photo_retry'] ?? [],
            'photos' => $result['photos'] ?? [],
            'started_at' => $startedAt,
            'finished_at' => now()->toIso8601String(),
        ];

        if (is_array($notice) && ($notice['chat_id'] ?? '') !== '') {
            $report['telegram'] = [
                'chat_id' => (string) $notice['chat_id'],
                'original_name' => (string) ($notice['original_name'] ?? basename($this->path)),
            ];
        }

        Storage::disk('local')->put(
            $reportPath,
            json_encode($report, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        );

        if (($result['photo_retry'] ?? []) !== []) {
            RetryFailedProductImagesJob::dispatch($reportPath)->delay(now()->addMinutes(5));
        }

        Log::info('Импорт Excel завершён', [
            'file' => $this->path,
            'report' => $reportPath,
            'imported' => $report['imported'],
            'updated' => $report['updated'],
            'skipped' => $report['skipped'],
            'errors' => count($report['errors']),
            'warnings' => count($report['warnings']),
            'photo_retry' => count($report['photo_retry']),
        ]);

        Storage::disk('local')->delete($this->path);
        Storage::disk('local')->delete($noticePath);

        try {
            app(TelegramService::class)->sendImportReport($report);
        } catch (Throwable $exception) {
            Log::error('Не удалось отправить результат импорта в Telegram', [
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
