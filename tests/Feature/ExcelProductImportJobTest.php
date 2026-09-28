<?php

namespace Tests\Feature;

use App\Jobs\ImportProductsFromExcelJob;
use App\Services\ExcelProductImportService;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class ExcelProductImportJobTest extends TestCase
{
    public function test_job_imports_the_stored_file_and_deletes_it(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('imports/catalog.xlsx', 'xlsx');

        $mock = Mockery::mock(ExcelProductImportService::class);
        $mock->shouldReceive('import')
            ->once()
            ->withArgs(function (string $file, string $brand): bool {
                return $file === Storage::disk('local')->path('imports/catalog.xlsx')
                    && is_file($file)
                    && $brand === "Barney's Farm";
            })
            ->andReturn([
                'imported' => 1,
                'updated' => 0,
                'skipped' => 0,
                'errors' => [],
                'warnings' => [],
            ]);
        $this->app->instance(ExcelProductImportService::class, $mock);

        (new ImportProductsFromExcelJob('imports/catalog.xlsx', "Barney's Farm"))
            ->handle($this->app->make(ExcelProductImportService::class));

        Storage::disk('local')->assertMissing('imports/catalog.xlsx');
    }

    public function test_job_keeps_the_file_when_import_fails(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('imports/catalog.xlsx', 'xlsx');

        $mock = Mockery::mock(ExcelProductImportService::class);
        $mock->shouldReceive('import')->once()->andThrow(new RuntimeException('Файл повреждён'));
        $this->app->instance(ExcelProductImportService::class, $mock);

        try {
            (new ImportProductsFromExcelJob('imports/catalog.xlsx', ''))
                ->handle($this->app->make(ExcelProductImportService::class));
            $this->fail('Исключение импорта должно всплыть из Job.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Файл повреждён', $exception->getMessage());
        }

        Storage::disk('local')->assertExists('imports/catalog.xlsx');
        $this->assertSame(3, (new ImportProductsFromExcelJob('imports/catalog.xlsx'))->tries);
    }

    public function test_job_fails_when_the_file_is_missing_and_does_not_import(): void
    {
        Storage::fake('local');

        $mock = Mockery::mock(ExcelProductImportService::class);
        $mock->shouldNotReceive('import');
        $this->app->instance(ExcelProductImportService::class, $mock);

        $this->expectException(RuntimeException::class);

        (new ImportProductsFromExcelJob('imports/missing.xlsx', ''))
            ->handle($this->app->make(ExcelProductImportService::class));
    }

    public function test_import_cannot_overlap_another_import(): void
    {
        $middleware = (new ImportProductsFromExcelJob('imports/catalog.xlsx'))->middleware();

        $this->assertInstanceOf(WithoutOverlapping::class, $middleware[0]);
        $this->assertSame('product-excel-import', $middleware[0]->key);
        $this->assertSame(7500, $middleware[0]->expiresAfter);
        $this->assertSame(60, $middleware[0]->releaseAfter);
    }

    public function test_job_sends_the_import_result_to_telegram(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('imports/catalog.csv', 'csv');
        Storage::disk('local')->put('imports/catalog.csv.telegram.json', json_encode([
            'chat_id' => '955',
            'original_name' => 'products.csv',
        ], JSON_UNESCAPED_UNICODE));
        config(['services.telegram.bot_token' => 'test-token']);
        Http::fake([
            'https://api.telegram.org/*' => Http::response(['ok' => true]),
        ]);

        $mock = Mockery::mock(ExcelProductImportService::class);
        $mock->shouldReceive('import')->once()->andReturn([
            'imported' => 10,
            'updated' => 62,
            'skipped' => 0,
            'errors' => [],
            'warnings' => [],
            'photo_retry' => [['url' => 'https://cdn.test/missing.jpg']],
            'photos' => [
                ['saved' => true],
                ['saved' => false],
            ],
        ]);

        (new ImportProductsFromExcelJob('imports/catalog.csv', ''))->handle($mock);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'sendMessage')
                && $request['chat_id'] === '955'
                && str_contains($request['text'], 'Импорт завершён.')
                && str_contains($request['text'], 'Файл: products.csv')
                && str_contains($request['text'], 'Товаров в файле: 72')
                && str_contains($request['text'], 'Создано: 10')
                && str_contains($request['text'], 'Обновлено: 62')
                && str_contains($request['text'], 'Загружено: 1')
                && str_contains($request['text'], 'Не загружено: 1')
                && str_contains($request['text'], 'Ошибок импорта: 0')
                && str_contains($request['text'], 'Повторная загрузка отсутствующих фотографий поставлена в очередь');
        });
        $this->assertFalse(Storage::disk('local')->exists('imports/catalog.csv'));
    }
}
