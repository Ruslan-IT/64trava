<?php

namespace App\Jobs;

use App\Services\ExcelProductImportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

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

    public function handle(ExcelProductImportService $products): void
    {
        $startedAt = now()->toIso8601String();
        $absolute = Storage::disk('local')->path($this->path);

        if (! is_file($absolute)) {
            throw new RuntimeException('Файл импорта не найден.');
        }

        $result = $products->import($absolute, $this->brand);
        $reportPath = 'imports/reports/'.Str::uuid()->toString().'.json';
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
    }
}
